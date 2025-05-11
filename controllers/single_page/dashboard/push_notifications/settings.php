<?php

namespace Concrete\Package\PushNotifications\Controller\SinglePage\Dashboard\PushNotifications;

use Concrete\Core\Error\ErrorList\ErrorList;
use Concrete\Core\Form\Service\Validation;
use Concrete\Core\Page\Controller\DashboardSitePageController;
use Concrete\Core\Site\Service;

/** @noinspection PhpUnused */

class Settings extends DashboardSitePageController
{
    public function view()
    {
        /** @var $formValidation Validation */
        /** @noinspection PhpUnhandledExceptionInspection */
        $formValidation = $this->app->make(Validation::class);
        /** @var Service $siteService */
        /** @noinspection PhpUnhandledExceptionInspection */
        $siteService = $this->app->make(Service::class);

        $errorList = new ErrorList();

        if ($this->request->getMethod() === "POST") {
            $formValidation->setData($this->request->request->all());
            $formValidation->addRequiredToken("update_settings");

            if ($formValidation->test()) {
                $enabledSites = (array)$this->request->request->get("enabledSites", []);

                foreach ($siteService->getList() as $site) {
                    $site->getConfigRepository()->save("push_notifications.is_enabled", in_array($site->getSiteID(), $enabledSites));
                }

                $this->set("success", t("The settings has been successfully updated."));
            } else {
                $errorList = $formValidation->getError();
            }

            $this->error = $errorList;
        }

        $siteList = [];
        $enabledSites = [];

        foreach ($siteService->getList() as $site) {
            $siteList[$site->getSiteID()] = $site->getSiteName();

            if ($site->getConfigRepository()->get("push_notifications.is_enabled", true)) {
                $enabledSites[] = $site->getSiteID();
            }
        }

        $this->set("siteList", $siteList);
        $this->set("enabledSites", $enabledSites);
    }

}
