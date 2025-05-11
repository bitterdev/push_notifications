<?php

namespace Bitter\PushNotifications\Provider;

use Bitter\PushNotifications\RouteList;
use Concrete\Core\Application\Application;
use Concrete\Core\Foundation\Service\Provider;
use Concrete\Core\Html\Service\Html;
use Concrete\Core\Page\Page;
use Concrete\Core\Routing\Router;
use Concrete\Core\Site\Service;
use Concrete\Core\View\View;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ServiceProvider extends Provider
{
    protected Router $router;
    protected EventDispatcherInterface $eventDispatcher;

    public function __construct(
        Application              $app,
        EventDispatcherInterface $eventDispatcher,
        Router                   $router
    )
    {
        parent::__construct($app);

        $this->router = $router;
        $this->eventDispatcher = $eventDispatcher;
    }

    public function register()
    {
        $this->registerEventHandlers();
        $this->registerRoutes();
    }

    protected function registerEventHandlers()
    {
        $this->eventDispatcher->addListener('on_before_render', function () {
            /** @var Service $siteService */
            $siteService = $this->app->make(Service::class);
            $site = $siteService->getActiveSiteForEditing();
            $isEnabled = $site->getConfigRepository()->get("push_notifications.is_enabled", true);
    
            if ($isEnabled) {
                $c = Page::getCurrentPage();

                if ($c instanceof Page && !$c->isError() && !$c->isSystemPage()) {
                    $v = View::getInstance();
                    /** @var Html $htmlService */
                    $htmlService = $this->app->make(Html::class);
                    $v->requireAsset("core/cms");
                    $v->addFooterItem($htmlService->javascript("push-notifications.js", "push_notifications"));
                    $v->addFooterItem(sprintf(
                        "<script>(function($) { $(function(){ $(\".ccm-page\").pushNotifications(%s) }); })(jQuery);</script>",
                        json_encode([
                            "messageTitle" => t("Push Notifications"),
                            "messageText" => t("Would you like to receive push notifications?"),
                            "enableText" => t("Yes"),
                            "disableText" => t("No")
                        ])
                    ));
                }
            }
        });
    }

    protected function registerRoutes()
    {
        $list = new RouteList();
        $list->loadRoutes($this->router);
    }

}
