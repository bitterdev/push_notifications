<?php

/**
 * @project:   Push Notifications
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter
 * @version    X.X.X
 */

namespace Bitter\PushNotifications\Provider;

use Bitter\PushNotifications\RouteList;
use Bitter\PushNotifications\Settings;
use Concrete\Core\Application\ApplicationAwareInterface;
use Concrete\Core\Application\ApplicationAwareTrait;
use Concrete\Core\File\File;
use Concrete\Core\Html\Service\Html;
use Concrete\Core\Http\Request;
use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Http\Service\Json;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Router;
use Concrete\Core\Http\Response;
use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Url;
use Concrete\Core\View\View;
use Concrete\Core\Database\Connection\Connection;
use Concrete\Core\Html\Service\Navigation;
use GuzzleHttp\Client as HttpClient;
use paragraph1\phpFCM\Client;
use paragraph1\phpFCM\Message;
use paragraph1\phpFCM\Notification;
use paragraph1\phpFCM\Recipient\Device;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Exception;

class ServiceProvider implements ApplicationAwareInterface
{

    use ApplicationAwareTrait;

    /** @var Router */
    protected $router;
    /** @var ResponseFactory */
    protected $responseFactory;
    /** @var Package */
    protected $pkg;
    protected $eventDispatcher;

    public function __construct(
        PackageService $packageService,
        ResponseFactory $responseFactory,
        EventDispatcherInterface $eventDispatcher,
        Router $router
    )
    {
        $this->router = $router;
        $this->pkg = $packageService->getByHandle("push_notifications")->getController();
        $this->eventDispatcher = $eventDispatcher;
        $this->responseFactory = $responseFactory;
    }

    public function register()
    {
        /*
         * Load Composer Dependencies
         */

        if (file_exists($this->pkg->getPackagePath() . '/vendor/autoload.php')) {
            /** @noinspection PhpIncludeInspection */
            require $this->pkg->getPackagePath() . '/vendor/autoload.php';
        }

        /*
         * Inject Code
         */

        $this->eventDispatcher->addListener('on_before_render', function () {
            /** @var $jsonHelper Json */
            $jsonHelper = $this->app->make(Json::class);
            /** @var $htmlHelper Html */
            $htmlHelper = $this->app->make(Html::class);
            /** @var Settings $settings */
            $settings = $this->app->make(Settings::class);
            $view = View::getInstance();

            if ($settings->getApiKey()) {
                $view->requireAsset("javascript", "jquery");
                /** @noinspection JSUnresolvedLibraryURL */
                $view->addFooterItem("<script src=\"https://www.gstatic.com/firebasejs/5.3.1/firebase-app.js\"></script>");
                /** @noinspection JSUnresolvedLibraryURL */
                $view->addFooterItem("<script src=\"https://www.gstatic.com/firebasejs/5.3.1/firebase-messaging.js\"></script>");
                $view->addFooterItem($htmlHelper->javascript("push-notifications.js", $this->pkg->getPackageHandle()));

                /** @noinspection BadExpressionStatementJS */
                /** @noinspection JSUnresolvedVariable */
                $view->addFooterItem(
                    sprintf(
                        "<script type=\"text/javascript\">initPushNotifications(%s)</script>",

                        $jsonHelper->encode([
                            'FCM' => [
                                'apiKey' => $settings->getApiKey(),
                                'authDomain' => $settings->getAuthDomain(),
                                'databaseURL' => $settings->getDatabaseURL(),
                                'projectId' => $settings->getProjectId(),
                                'storageBucket' => $settings->getStorageBucket(),
                                'messagingSenderId' => $settings->getMessagingSenderId()
                            ],
                            'serviceWorkerLocation' => (string)Url::to("/service-worker.js")
                        ])
                    )
                );
            }
        });

        /*
         * Extend Meta Tags
         */

        $this->eventDispatcher->addListener('on_header_required_ready', function ($event) {
            /** @var $event GenericEvent */
            $metaTags = $event->getArgument('metaTags');

            $manifestUrl = (string)Url::to("/manifest.json");

            /** @noinspection HtmlUnknownTarget */
            $metaTags["manifest"] = sprintf("<link rel=\"manifest\" href=\"%s\">", $manifestUrl);

            $event->setArgument("metaTags", $metaTags);

            return $event;
        });

        /** @noinspection PhpDeprecationInspection */
        $this->router->register("/bitter/" . $this->pkg->getPackageHandle() . "/reminder/hide", function () {
            $this->pkg->getConfig()->save('reminder.hide', true);
            $app = Application::getFacadeApplication();
            /** @var $responseFactory ResponseFactory */
            $responseFactory = $app->make(ResponseFactory::class);
            $responseFactory->create("", Response::HTTP_OK)->send();
            $app->shutdown();
        });

        /** @noinspection PhpDeprecationInspection */
        $this->router->register("/bitter/" . $this->pkg->getPackageHandle() . "/did_you_know/hide", function () {
            $this->pkg->getConfig()->save('did_you_know.hide', true);
            $app = Application::getFacadeApplication();
            /** @var $responseFactory ResponseFactory */
            $responseFactory = $app->make(ResponseFactory::class);
            $responseFactory->create("", Response::HTTP_OK)->send();
            $app->shutdown();
        });

        /** @noinspection PhpDeprecationInspection */
        $this->router->register("/bitter/" . $this->pkg->getPackageHandle() . "/license_check/hide", function () {
            $this->pkg->getConfig()->save('license_check.hide', true);
            $app = Application::getFacadeApplication();
            /** @var $responseFactory ResponseFactory */
            $responseFactory = $app->make(ResponseFactory::class);
            $responseFactory->create("", Response::HTTP_OK)->send();
            $app->shutdown();
        });

        /*
         * Add proxy route for service worker to fill in
         * the sender id from the settings
         */

        /** @noinspection PhpDeprecationInspection */
        $this->router->register("/service-worker.js", function () {
            /** @var Settings $settings */
            $settings = $this->app->make(Settings::class);
            /** @var $fileHelper \Concrete\Core\File\Service\File */
            $fileHelper = $this->app->make("helper/file");

            $serviceWorkerFile = $this->pkg->getPackagePath() . "/js/service-worker.js";

            $fileContent = $fileHelper->getContents($serviceWorkerFile);

            $fileContent = str_replace("%messagingSenderId%", $settings->getMessagingSenderId(), $fileContent);

            return new Response(
                $fileContent,
                200,
                [
                    "Content-Type" => "text/javascript"
                ]
            );
        });

        /** @noinspection PhpDeprecationInspection */
        $this->router->register("/manifest.json", function () {
            /*
             * This sender ID is required in order to tell Firebase your website
             * allows messages to be pushed to it. Do not change the ID.
             * It is the same for all Firebase projects, regardless of
             * who you are or what you've built.
             */
            return new JsonResponse([
                "gcm_sender_id" => "103953800507"
            ]);
        });

        /*
         * Route for storing device token to databse
         */

        /** @noinspection PhpDeprecationInspection */
        $this->router->register("/fcm/register", function () {
            /** @var Settings $settings */
            $settings = $this->app->make(Settings::class);
            /** @var $request Request */
            $request = $this->app->make(Request::class);
            /** @var $db Connection */
            $db = $this->app->make(Connection::class);
            $token = $request->request->get("token");
            /** @var $navHelper Navigation */
            $navHelper = $this->app->make(Navigation::class);

            if (strlen($token) > 0) {
                /*
                 * Prepared statements should be secure enough...
                 */

                /** @noinspection SqlDialectInspection */
                /** @noinspection SqlNoDataSourceInspection */
                $newToken = intval($db->fetchColumn("SELECT COUNT(*) FROM PushNotificationsToken WHERE token = ?", [$token])) === 0;

                if ($newToken) {
                    /** @noinspection SqlDialectInspection */
                    /** @noinspection SqlNoDataSourceInspection */
                    $db->executeQuery("INSERT INTO PushNotificationsToken (token) VALUES (?)", [$token]);

                    /*
                     * Send Welcome message
                    */

                    if ($settings->getWelcomeMessageEnabled()) {
                        $clickActionUrl = "";

                        $destPageId = $settings->getWelcomeMessageClickAction();

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
                            $client->injectHttpClient(new HttpClient());

                            $message = new Message();

                            $notification = new Notification(
                                $settings->getWelcomeMessageTitle(),
                                $settings->getWelcomeMessageBody()
                            );

                            $iconFileId = $settings->getWelcomeMessageIconFileId();

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
                            $message->addRecipient(new Device($token));

                            $client->send($message);

                        } catch (Exception $err) {
                            // Skip any error...
                        }
                    }
                }
            }

            return new Response("", 200);
        });

        $list = new RouteList();
        $list->loadRoutes($this->router);
    }

}
