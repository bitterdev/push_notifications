<?php

namespace Bitter\PushNotifications;

use Bitter\PushNotifications\API\V1\Middleware\FractalNegotiatorMiddleware;
use Bitter\PushNotifications\API\V1\PushNotifications;
use Concrete\Core\Routing\RouteListInterface;
use Concrete\Core\Routing\Router;

class RouteList implements RouteListInterface
{
    public function loadRoutes(Router $router)
    {
        $router
            ->buildGroup()
            ->setPrefix('/api/v1')
            ->addMiddleware(FractalNegotiatorMiddleware::class)
            ->routes(function ($groupRouter) {
                /** @var $groupRouter Router */
                /** @noinspection PhpParamsInspection */
                $groupRouter->all('/push_notifications/register_device', [PushNotifications::class, 'registerDevice']);
                /** @noinspection PhpParamsInspection */
                $groupRouter->all('/push_notifications/get_vapid_keys', [PushNotifications::class, 'getVapidKeys']);
            });

        $router->buildGroup()->setNamespace('Concrete\Package\PushNotifications\Controller\Dialog\Support')
            ->setPrefix('/ccm/system/dialogs/push_notifications')
            ->routes('dialogs/support.php', 'push_notifications');
    }
}