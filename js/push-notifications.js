/**
 * @project:   Push Notifications
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter
 * @version    X.X.X
 */

var initPushNotifications = function (config) {
    var sendTokenToServer = function (token) {
        $.ajax({
            type: "POST",
            url: CCM_DISPATCHER_FILENAME + "/fcm/register",
            data: {
                token: token
            }
        });
    }

    firebase.initializeApp(config.FCM);

    const messaging = firebase.messaging();

    navigator.serviceWorker.register(config.serviceWorkerLocation).then(function (registration) {
        messaging.useServiceWorker(registration);

        messaging.requestPermission().then(function () {
            messaging.getToken().then(function (currentToken) {
                sendTokenToServer(currentToken);
            }).catch(function (err) {
                console.log('An error occurred while retrieving token. ', err);
            });

        }).catch(function (err) {
            console.log('Unable to get permission to notify.', err);
        });

        messaging.onMessage(function (payload) {
            /*
             * Displaying foreground push notifications
             */
            registration.showNotification(payload.notification.title, payload.notification);
        });


    }).catch(function (err) {
        console.log('Unable to register service worker ', err);
    });

    messaging.onTokenRefresh(function () {
        messaging.getToken().then(function (refreshedToken) {
            sendTokenToServer(refreshedToken);
        }).catch(function (err) {
            console.log('Unable to retrieve refreshed token ', err);
        });
    });
}