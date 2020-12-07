<?php

/**
 * @project:   Push Notifications
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter
 * @version    X.X.X
 */

namespace Concrete\Package\PushNotifications;

use Bitter\PushNotifications\Provider\ServiceProvider;
use Concrete\Core\Package\Package;

class Controller extends Package
{
    protected $pkgHandle = 'push_notifications';
    protected $pkgVersion = '1.2.0';
    protected $appVersionRequired = '8.0.0';
    protected $pkgAutoloaderRegistries = [
        'src/Bitter/PushNotifications' => 'Bitter\PushNotifications',
    ];

    public function getPackageDescription()
    {
        return t('Send push notifications through Firebase Cloud Messaging Service (FCM) to users desktop.');
    }

    public function getPackageName()
    {
        return t('Push Notifications');
    }

    public function on_start()
    {
        /** @var ServiceProvider $serviceProvider */
        $serviceProvider = $this->app->make(ServiceProvider::class);
        $serviceProvider->register();
    }
    public function install()
    {
        parent::install();
        $this->installContentFile('install.xml');
    }

}
