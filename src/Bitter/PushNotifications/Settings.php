<?php

/**
 * @project:   Push Notifications
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter
 * @version    X.X.X
 */

namespace Bitter\PushNotifications;

use Concrete\Core\Package\Package;
use Concrete\Core\Package\PackageService;
use \Concrete\Core\Support\Facade\Application;

class Settings
{
    protected $app;
    /** @var Package */
    protected $pkg;
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
        PackageService $packageService
    )
    {
        $this->app = Application::getFacadeApplication();
        $this->pkg = $packageService->getByHandle("push_notifications")->getController();

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
        $config = $this->pkg->getConfig();

        $config->save("settings.api_key", $this->getApiKey());
        $config->save("settings.auth_domain", $this->getAuthDomain());
        $config->save("settings.database_url", $this->getDatabaseURL());
        $config->save("settings.project_id", $this->getProjectId());
        $config->save("settings.storage_bucket", $this->getStorageBucket());
        $config->save("settings.messaging_sender_id", $this->getMessagingSenderId());
        $config->save("settings.server_key", $this->getServerKey());
        $config->save("settings.welcome_message_enabled", $this->getWelcomeMessageEnabled());
        $config->save("settings.welcome_message_title", $this->getWelcomeMessageTitle());
        $config->save("settings.welcome_message_body", $this->getWelcomeMessageBody());
        $config->save("settings.welcome_message_click_action", $this->getWelcomeMessageClickAction());
        $config->save("settings.welcome_message_icon_file_id", $this->getWelcomeMessageIconFileId());

        return $this;
    }

    public function load()
    {
        $config = $this->pkg->getConfig();

        $this->setApiKey($config->get("settings.api_key", ""));
        $this->setAuthDomain($config->get("settings.auth_domain", ""));
        $this->setDatabaseURL($config->get("settings.database_url", ""));
        $this->setProjectId($config->get("settings.project_id", ''));
        $this->setStorageBucket($config->get("settings.storage_bucket", ''));
        $this->setMessagingSenderId($config->get("settings.messaging_sender_id", ''));
        $this->setServerKey($config->get("settings.server_key", ''));
        $this->setWelcomeMessageEnabled($config->get("settings.welcome_message_enabled", 0));
        $this->setWelcomeMessageTitle($config->get("settings.welcome_message_title", ''));
        $this->setWelcomeMessageBody($config->get("settings.welcome_message_body", ''));
        $this->setWelcomeMessageClickAction($config->get("settings.welcome_message_click_action", 0));
        $this->setWelcomeMessageIconFileId($config->get("settings.welcome_message_icon_file_id", 0));
    }
}