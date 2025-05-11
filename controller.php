<?php

namespace Concrete\Package\PushNotifications;

use Bitter\PushNotifications\Provider\ServiceProvider;
use Concrete\Core\Package\Package;

class Controller extends Package
{
    protected string $pkgHandle = 'push_notifications';
    protected string $pkgVersion = '1.3.0';
    protected $appVersionRequired = '9.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Bitter/PushNotifications' => 'Bitter\PushNotifications',
    ];

    public function getPackageDescription(): string
    {
        return t('Push Notifications for Concrete CMS allows you to easily send real-time push notifications to your users without Firebase, directly from the intuitive dashboard, boosting engagement and marketing effectiveness.');
    }

    public function getPackageName(): string
    {
        return t('Push Notifications');
    }

    public function on_start()
    {
        /** @noinspection PhpIncludeInspection */
        require $this->getPackagePath() . '/vendor/autoload.php';

        /** @var ServiceProvider $serviceProvider */
        /** @noinspection PhpUnhandledExceptionInspection */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }

    public function install()
    {
        parent::install();
        $this->installContentFile('install.xml');
    }

}