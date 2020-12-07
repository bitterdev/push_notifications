<?php

/**
 * @project:   Push Notifications
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter
 * @version    X.X.X
 */

namespace Concrete\Package\PushNotifications\Controller\SinglePage\Dashboard\PushNotifications;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Http\Request;

class Settings extends DashboardPageController
{
    public function view()
    {
        /** @var \Bitter\PushNotifications\Settings $settings */
        $settings = $this->app->make(\Bitter\PushNotifications\Settings::class);

        if ($this->token->validate("save_settings")) {
            /** @var $request Request */
            $request = $this->app->make(Request::class);

            $settings->setApiKey($request->request->get("apiKey", ""));
            $settings->setAuthDomain($request->request->get("authDomain", ""));
            $settings->setDatabaseURL($request->request->get("databaseURL", ""));
            $settings->setProjectId($request->request->get("projectId", ""));
            $settings->setStorageBucket($request->request->get("storageBucket", ""));
            $settings->setMessagingSenderId($request->request->get("messagingSenderId", ""));
            $settings->setServerKey($request->request->get("serverKey", ""));
            $settings->setWelcomeMessageEnabled($request->request->get("welcomeMessageEnabled", 0));
            $settings->setWelcomeMessageTitle($request->request->get("welcomeMessageTitle", ""));
            $settings->setWelcomeMessageBody($request->request->get("welcomeMessageBody", ""));
            $settings->setWelcomeMessageClickAction($request->request->get("welcomeMessageClickAction", 0));
            $settings->setWelcomeMessageIconFileId($request->request->get("welcomeMessageIconFileId", 0));
            $settings->save();
        }

        $this->set("app", $this->app);
        $this->set("apiKey", $settings->getApiKey());
        $this->set("authDomain", $settings->getAuthDomain());
        $this->set("databaseURL", $settings->getDatabaseURL());
        $this->set("projectId", $settings->getProjectId());
        $this->set("storageBucket", $settings->getStorageBucket());
        $this->set("messagingSenderId", $settings->getMessagingSenderId());
        $this->set("serverKey", $settings->getServerKey());
        $this->set("welcomeMessageEnabled", $settings->getWelcomeMessageEnabled());
        $this->set("welcomeMessageTitle", $settings->getWelcomeMessageTitle());
        $this->set("welcomeMessageBody", $settings->getWelcomeMessageBody());
        $this->set("welcomeMessageClickAction", $settings->getWelcomeMessageClickAction());
        $this->set("welcomeMessageIconFileId", $settings->getWelcomeMessageIconFileId());
    }

}
