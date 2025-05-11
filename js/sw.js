console.log('Service Worker loaded');

self.addEventListener('push', function (event) {
    console.log('Push event received:', event);
    console.log('Raw event data:', event.data);

    // Initialize empty data object
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
        console.log('Parsed push data:', data);
    } catch (error) {
        console.error('Error parsing push data:', error);
        data = { title: '', body: '' };
    }

    // Prepare notification options
    const options = {
        body: data.body || '',
        // Only include icon if provided in the payload
        ...(data.icon && { icon: data.icon }),
        data: {
            url: data.url || '' // Empty string if url is not provided
        }
    };

    event.waitUntil(
        self.registration.showNotification(data.title || '', options)
            .catch(err => console.error('Error while displaying the notification:', err))
    );
});

self.addEventListener('notificationclick', function (event) {
    console.log('Notification clicked:', event);
    event.notification.close();
    // Use empty string if url is not provided
    const url = event.notification.data.url || '';
    event.waitUntil(
        clients.openWindow(url)
    );
});

self.addEventListener('install', function (event) {
    console.log('Service Worker installed');
});

self.addEventListener('activate', function (event) {
    console.log('Service Worker activated');
});