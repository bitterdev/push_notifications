<?php

/**
 * @project:   Push Notifications
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter
 * @version    X.X.X
 */

namespace Concrete\Package\PushNotifications\Controller\SinglePage\Dashboard;

use Concrete\Core\Http\ResponseFactory;
use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Url;

/** @noinspection PhpUnused */

class PushNotifications extends DashboardPageController
{
    public function view()
    {
        /** @var ResponseFactory $responseFactory */
        $responseFactory = $this->app->make(ResponseFactory::class);
        return $responseFactory->redirect((string)Url::to("/dashboard/push_notifications/send_message"));
    }
}
