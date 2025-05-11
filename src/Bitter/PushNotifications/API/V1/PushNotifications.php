<?php /** @noinspection PhpUnused */

namespace Bitter\PushNotifications\API\V1;

use Concrete\Core\Application\EditResponse;
use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Entity\Site\Site;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Http\Request;
use Concrete\Core\Site\Service;
use Concrete\Core\User\User;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Minishlink\WebPush\VAPID;
use Symfony\Component\HttpFoundation\JsonResponse;
use ErrorException;

class PushNotifications
{
    protected Repository $config;
    protected Connection $db;
    protected Request $request;
    protected Site $site;

    public function __construct(
        Repository $config,
        Connection $db,
        Request    $request,
        Service    $siteService
    )
    {
        $this->config = $config;
        $this->db = $db;
        $this->request = $request;
        $this->site = $siteService->getSite();
    }

    public function getVapidKeys(): JsonResponse
    {
        if (!$this->config->has("push_notifications.vapid_keys")) {
            try {
                $vapidKeys = VAPID::createVapidKeys();
                $this->config->save("push_notifications.vapid_keys", $vapidKeys);
            } catch (ErrorException) {
                $vapidKeys = [];
            }
        } else {
            $vapidKeys = $this->config->get("push_notifications.vapid_keys");
        }

        return new JsonResponse([
            "publicKey" => $vapidKeys["publicKey"] ?? null
        ]);
    }

    public function registerDevice(): JsonResponse
    {
        $errorList = new ErrorList();
        $editResponse = new EditResponse();

        $data = json_decode($this->request->getContent(), true);

        if (!$data || !isset($data['endpoint'], $data['keys']['p256dh'], $data['keys']['auth'])) {
            $errorList->add(t('Invalid subscription data'));
        } else {

            $endpoint = $data['endpoint'];
            $p256dh = $data['keys']['p256dh'];
            $auth = $data['keys']['auth'];

            $uID = null;
            $u = new User();

            if ($u->isRegistered()) {
                $uID = $u->getUserID();
            }

            try {
                /** @noinspection SqlDialectInspection */
                /** @noinspection SqlNoDataSourceInspection */
                $existing = (int)$this->db->fetchOne('SELECT COUNT(*) FROM PushSubscriptions WHERE endpoint = ?', [$endpoint]);

                if ($existing === 0) {
                    $this->db->insert('PushSubscriptions', [
                        'endpoint' => $endpoint,
                        'p256dh' => $p256dh,
                        'auth' => $auth,
                        'siteId' => $this->site->getSiteID(),
                        'uID' => $uID
                    ]);

                    $editResponse->setMessage(t("The device has been successfully registered."));
                } else {
                    $errorList->add(t("The device is already registered."));
                }
            } catch (Exception $e) {
                $errorList->add($e->getMessage());
            }
        }

        $editResponse->setError($errorList);

        return new JsonResponse($editResponse);
    }
}