<?php

/**
 * @project:   Push Notifications
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter
 * @version    X.X.X
 */

namespace Concrete\Package\PushNotifications\Controller\SinglePage\Dashboard\PushNotifications;

use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\File\File;
use Concrete\Core\Html\Service\Navigation;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\Request;
use Concrete\Core\Page\Page;
use Bitter\PushNotifications\Settings;
use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Recipient\Device;
use paragraph1\phpFCM\Notification;
use Exception;

/** @noinspection PhpUnused */

class SendMessage extends DashboardPageController
{
    public function view()
    {
        /** @varSettings $settings */
        $settings = $this->app->make(Settings::class);

        if ($this->token->validate("send_message")) {
            /** @var $request Request */
            $request = $this->app->make(Request::class);
            /** @var $db Connection */
            $db = $this->app->make(Connection::class);
            /** @var $navHelper Navigation */
            $navHelper = $this->app->make(Navigation::class);

            $deviceTokens = [];

            /** @noinspection SqlDialectInspection */
            /** @noinspection SqlNoDataSourceInspection */
            foreach ($db->fetchAll("SELECT token FROM PushNotificationsToken") as $row) {
                $deviceTokens[] = $row["token"];
            }

            $clickActionUrl = "";

            $destPageId = $request->request->get("destPageId", 0);

            if ($destPageId > 0) {
                $destPage = Page::getByID($destPageId);

                if (is_object($destPage)) {
                    /** @noinspection PhpParamsInspection */
                    $clickActionUrl = $navHelper->getCollectionURL($destPage);
                }
            }

            try {
                $client = new Client();
                $client->setApiKey($settings->getServerKey());
                $client->injectHttpClient(new \GuzzleHttp\Client());

                /*
                 * Split Array to chunks of max 100 items because
                 * FCM accept only 100 recipients per message...
                 */
                foreach (array_chunk($deviceTokens, 100) as $deviceTokensChunk) {
                    $message = new Message();

                    $notification = new Notification(
                        $request->request->get("title", ""),
                        $request->request->get("body", "")
                    );

                    $iconFileId = intval($request->request->get("iconFileId", 0));

                    if ($iconFileId > 0) {
                        $iconFile = File::getByID($iconFileId);

                        if (is_object($iconFile)) {
                            $approvedVersion = $iconFile->getApprovedVersion();

                            if (is_object($approvedVersion)) {
                                $notification->setIcon($approvedVersion->getURL());
                            }
                        }
                    }

                    $notification->setClickAction($clickActionUrl);

                    $message->setNotification($notification);

                    foreach ($deviceTokensChunk as $deviceToken) {
                        $message->addRecipient(new Device($deviceToken));
                    }

                    $client->send($message);
                }

                $this->set("success", t("The message was successfully sent."));

            } catch (Exception $err) {
                $this->error->add(t("The was an error while sending the message."));
            }
        }

        $this->set("app", $this->app);
    }

}
