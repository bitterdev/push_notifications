self.addEventListener('push', function (event) {
    // Initialize empty data object
    let data = {};
    try {
        data = event.data ? event.data.json() : {};
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
    );
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    // Use empty string if url is not provided
    const url = event.notification.data.url || '';
    event.waitUntil(
        clients.openWindow(url)
    );
});