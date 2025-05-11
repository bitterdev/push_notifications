<?php

namespace Concrete\Package\PushNotifications\Controller\SinglePage\Dashboard\PushNotifications;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Entity\File\Version;
use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\File\File;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Entity\File\File as FileEntity;
use Concrete\Core\Http\Request;
use Concrete\Core\Logging\Channels;
use Concrete\Core\Logging\LoggerAwareInterface;
use Concrete\Core\Logging\LoggerAwareTrait;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Page\Page;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\User\User;
use Doctrine\DBAL\Exception;
use Minishlink\WebPush\MessageSentReport;
use Minishlink\WebPush\Subscription;
use Minishlink\WebPush\VAPID;
use Minishlink\WebPush\WebPush;
use ErrorException;

/** @noinspection PhpUnused */

class SendMessage extends DashboardSitePageController implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    public function view()
    {
        /** @var Repository $config */
        /** @noinspection PhpUnhandledExceptionInspection */
        $config = $this->app->make(Repository::class);
        /** @var $request Request */
        /** @noinspection PhpUnhandledExceptionInspection */
        $request = $this->app->make(Request::class);
        /** @var $db Connection */
        /** @noinspection PhpUnhandledExceptionInspection */
        $db = $this->app->make(Connection::class);
        /** @var $formValidation Validation */
        /** @noinspection PhpUnhandledExceptionInspection */
        $formValidation = $this->app->make(Validation::class);
        $errorList = new ErrorList();
        $u = new User();

        $ui = $u->getUserInfoObject();

        if ($this->request->getMethod() === "POST") {
            $formValidation->setData($this->request->request->all());
            $formValidation->addRequiredToken("send_message");
            $formValidation->addRequired("title", t("You need to enter a valid title."));
            $formValidation->addRequired("body", t("You need to enter a valid body."));

            if ($formValidation->test()) {

                if (!$config->has("push_notifications.vapid_keys")) {
                    try {
                        $vapidKeys = VAPID::createVapidKeys();

                        $config->save("push_notifications.vapid_keys", $vapidKeys);
                    } catch (ErrorException) {
                        $vapidKeys = [];
                    }
                } else {
                    $vapidKeys = $config->get("push_notifications.vapid_keys");
                }

                $auth = [
                    'VAPID' => [
                        'subject' => 'mailto:' . $ui->getUserEmail(),
                        'publicKey' => $vapidKeys['publicKey'],
                        'privateKey' => $vapidKeys['privateKey'],
                    ],
                ];

                try {
                    $webPush = new WebPush($auth);

                    $iconUrl = null;
                    $targetPage = null;

                    if ($this->request->request->has("iconFile")) {
                        $iconFile = $this->request->request->get("iconFile");
                        $f = File::getByID($iconFile);

                        if ($f instanceof FileEntity) {
                            $fv = $f->getApprovedVersion();

                            if ($fv instanceof Version) {
                                $iconUrl = $fv->getURL();
                            }
                        }
                    }

                    if ($this->request->request->has("targetPage")) {
                        $targetPage = (string)Url::to(Page::getByID($request->request->get("targetPage", 0)));
                    }

                    if (!$errorList->has()) {
                        /** @noinspection SqlDialectInspection */
                        /** @noinspection SqlNoDataSourceInspection */
                        $subs = $db->fetchAllAssociative("SELECT * FROM PushSubscriptions WHERE siteId = ?", [
                            $this->getSite()->getSiteID()
                        ]);

                        foreach ($subs as $sub) {
                            $subscription = Subscription::create([
                                'endpoint' => $sub['endpoint'],
                                'publicKey' => $sub['p256dh'],
                                'authToken' => $sub['auth'],
                                'contentEncoding' => 'aesgcm'
                            ]);

                            $webPush->queueNotification($subscription, json_encode([
                                'title' => $this->request->request->get("title"),
                                'body' => $this->request->request->get("body"),
                                'icon' => $iconUrl,
                                'url' => $targetPage
                            ]));
                        }

                        $webPush->flush();

                        $results = $webPush->flush();

                        $successfulMessageCounter = 0;
                        $errorMessageCounter = 0;

                        foreach ($results as $result) {
                            if ($result instanceof MessageSentReport) {
                                if ($result->isSuccess()) {
                                    $successfulMessageCounter++;
                                    $this->logger->info(t("Message was sent successfully to endpoint %s.", $result->getEndpoint()));
                                } else {
                                    $errorMessageCounter++;
                                    $this->logger->error(t("Error while sending message to endpoint %s (Reason: %s).", $result->getEndpoint(), $result->getReason()));
                                }
                            }
                        }

                        if ($successfulMessageCounter > 0) {
                            $failedMessageNotice = "";

                            if ($errorMessageCounter > 0) {
                                $failedMessageNotice = " " . t2("%s message failed.", "%s failed.", $successfulMessageCounter) . " " . t("See more details in application logs.");
                            }

                            $this->set("success", t2("%s message has been successfully sent.", "%s messages has been successfully sent.", $successfulMessageCounter) . $failedMessageNotice);
                        } else {
                            $errorList->add(t("No messages were sent."));
                        }
                    }

                } catch (ErrorException|Exception $e) {
                    $errorList->add($e);
                }
            } else {
                $errorList = $formValidation->getError();
            }

            $this->error = $errorList;
        }
    }

    public function getLoggerChannel(): string
    {
        return Channels::CHANNEL_APPLICATION;
    }
}
