/**
 * @project:   Push Notifications
 *
 * @author     Fabian Bitter (fabian@bitter.de)
 * @copyright  (C) 2020 Fabian Bitter
 * @version    X.X.X
 */

importScripts("https://www.gstatic.com/firebasejs/5.3.1/firebase-app.js");
importScripts("https://www.gstatic.com/firebasejs/5.3.1/firebase-messaging.js");

firebase.initializeApp({
    'messagingSenderId': '%messagingSenderId%'
});

const messaging = firebase.messaging();
