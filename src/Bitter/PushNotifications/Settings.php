<?php

/**
 * @project:   Push Notifications
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter
 * @version    X.X.X
 */

namespace Bitter\PushNotifications;

use Concrete\Core\Config\Repository\Repository;
use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use \Concrete\Core\Support\Facade\Application;

class Settings
{
    protected $app;
    /** @var Package */
    protected $pkg;
    protected $config;

    private $apiKey = '';
    private $authDomain = '';
    private $databaseURL = '';
    private $projectId = '';
    private $storageBucket = '';
    private $messagingSenderId = '';
    private $serverKey = '';
    private $welcomeMessageEnabled = 0;
    private $welcomeMessageTitle = '';
    private $welcomeMessageBody = '';
    private $welcomeMessageClickAction = 0;
    private $welcomeMessageIconFileId = 0;

    public function __construct(
        PackageService $packageService,
        Repository $config
    )
    {
        $this->app = Application::getFacadeApplication();
        $this->pkg = $packageService->getByHandle("push_notifications")->getController();
        $this->config = $config;
        $this->load();
    }

    /**
     * @return string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * @param string $apiKey
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * @return string
     */
    public function getAuthDomain()
    {
        return $this->authDomain;
    }

    /**
     * @param string $authDomain
     */
    public function setAuthDomain($authDomain)
    {
        $this->authDomain = $authDomain;
    }

    /**
     * @return string
     */
    public function getDatabaseURL()
    {
        return $this->databaseURL;
    }

    /**
     * @param string $databaseURL
     */
    public function setDatabaseURL($databaseURL)
    {
        $this->databaseURL = $databaseURL;
    }

    /**
     * @return string
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * @param string $projectId
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * @return string
     */
    public function getStorageBucket()
    {
        return $this->storageBucket;
    }

    /**
     * @param string $storageBucket
     */
    public function setStorageBucket($storageBucket)
    {
        $this->storageBucket = $storageBucket;
    }

    /**
     * @return string
     */
    public function getMessagingSenderId()
    {
        return $this->messagingSenderId;
    }

    /**
     * @param string $messagingSenderId
     */
    public function setMessagingSenderId($messagingSenderId)
    {
        $this->messagingSenderId = $messagingSenderId;
    }

    /**
     * @return string
     */
    public function getServerKey()
    {
        return $this->serverKey;
    }

    /**
     * @param string $serverKey
     */
    public function setServerKey($serverKey)
    {
        $this->serverKey = $serverKey;
    }

    /**
     * @return int
     */
    public function getWelcomeMessageEnabled()
    {
        return $this->welcomeMessageEnabled;
    }

    /**
     * @param int $welcomeMessageEnabled
     */
    public function setWelcomeMessageEnabled($welcomeMessageEnabled)
    {
        $this->welcomeMessageEnabled = $welcomeMessageEnabled;
    }

    /**
     * @return string
     */
    public function getWelcomeMessageTitle()
    {
        return $this->welcomeMessageTitle;
    }

    /**
     * @param string $welcomeMessageTitle
     */
    public function setWelcomeMessageTitle($welcomeMessageTitle)
    {
        $this->welcomeMessageTitle = $welcomeMessageTitle;
    }

    /**
     * @return string
     */
    public function getWelcomeMessageBody()
    {
        return $this->welcomeMessageBody;
    }

    /**
     * @param string $welcomeMessageBody
     */
    public function setWelcomeMessageBody($welcomeMessageBody)
    {
        $this->welcomeMessageBody = $welcomeMessageBody;
    }

    /**
     * @return int
     */
    public function getWelcomeMessageClickAction()
    {
        return $this->welcomeMessageClickAction;
    }

    /**
     * @param int $welcomeMessageClickAction
     */
    public function setWelcomeMessageClickAction($welcomeMessageClickAction)
    {
        $this->welcomeMessageClickAction = $welcomeMessageClickAction;
    }

    /**
     * @return int
     */
    public function getWelcomeMessageIconFileId()
    {
        return $this->welcomeMessageIconFileId;
    }

    /**
     * @param int $welcomeMessageIconFileId
     */
    public function setWelcomeMessageIconFileId($welcomeMessageIconFileId)
    {
        $this->welcomeMessageIconFileId = $welcomeMessageIconFileId;
    }


    /**
     * @return Settings
     */
    public function save()
    {
        $this->config->save("push_notifications.settings.api_key", $this->getApiKey());
        $this->config->save("push_notifications.settings.auth_domain", $this->getAuthDomain());
        $this->config->save("push_notifications.settings.database_url", $this->getDatabaseURL());
        $this->config->save("push_notifications.settings.project_id", $this->getProjectId());
        $this->config->save("push_notifications.settings.storage_bucket", $this->getStorageBucket());
        $this->config->save("push_notifications.settings.messaging_sender_id", $this->getMessagingSenderId());
        $this->config->save("push_notifications.settings.server_key", $this->getServerKey());
        $this->config->save("push_notifications.settings.welcome_message_enabled", $this->getWelcomeMessageEnabled());
        $this->config->save("push_notifications.settings.welcome_message_title", $this->getWelcomeMessageTitle());
        $this->config->save("push_notifications.settings.welcome_message_body", $this->getWelcomeMessageBody());
        $this->config->save("push_notifications.settings.welcome_message_click_action", $this->getWelcomeMessageClickAction());
        $this->config->save("push_notifications.settings.welcome_message_icon_file_id", $this->getWelcomeMessageIconFileId());

        return $this;
    }

    public function load()
    {
        $this->setApiKey($this->config->get("push_notifications.settings.api_key", ""));
        $this->setAuthDomain($this->config->get("push_notifications.settings.auth_domain", ""));
        $this->setDatabaseURL($this->config->get("push_notifications.settings.database_url", ""));
        $this->setProjectId($this->config->get("push_notifications.settings.project_id", ''));
        $this->setStorageBucket($this->config->get("push_notifications.settings.storage_bucket", ''));
        $this->setMessagingSenderId($this->config->get("push_notifications.settings.messaging_sender_id", ''));
        $this->setServerKey($this->config->get("push_notifications.settings.server_key", ''));
        $this->setWelcomeMessageEnabled($this->config->get("push_notifications.settings.welcome_message_enabled", 0));
        $this->setWelcomeMessageTitle($this->config->get("push_notifications.settings.welcome_message_title", ''));
        $this->setWelcomeMessageBody($this->config->get("push_notifications.settings.welcome_message_body", ''));
        $this->setWelcomeMessageClickAction($this->config->get("push_notifications.settings.welcome_message_click_action", 0));
        $this->setWelcomeMessageIconFileId($this->config->get("push_notifications.settings.welcome_message_icon_file_id", 0));
    }
}
