/**
 * SchoolCloud ERP Mobile Push & Status-Bar Notification Service Worker
 */
self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(clients.claim());
});

// Handle incoming WebPush event
self.addEventListener('push', (event) => {
    let data = {
        title: 'School Notification',
        body: 'You have a new school update.',
        icon: '/images/school-logo.png',
        url: '/',
        tag: 'school-push-' + Date.now()
    };

    if (event.data) {
        try {
            const parsed = event.data.json();
            data = Object.assign(data, parsed);
        } catch (e) {
            data.body = event.data.text();
        }
    }

    const options = {
        body: data.body,
        icon: data.icon || '/images/school-logo.png',
        badge: data.badge || '/images/school-logo.png',
        vibrate: [200, 100, 200, 100, 200],
        sound: 'default',
        tag: data.tag || 'school-erp-push',
        renotify: true,
        requireInteraction: true,
        data: {
            url: data.url || '/'
        },
        actions: [
            { action: 'open', title: 'Open App' },
            { action: 'close', title: 'Dismiss' }
        ]
    };

    event.waitUntil(
        self.registration.showNotification(data.title, options)
    );
});

// Handle direct message from webpage to post into Android/iOS system notification shade
self.addEventListener('message', (event) => {
    if (event.data && (event.data.type === 'SHOW_NOTIFICATION' || event.data.type === 'TRIGGER_PUSH')) {
        const item = event.data;
        const options = {
            body: item.body || item.message || '',
            icon: item.icon || '/images/school-logo.png',
            badge: item.badge || '/images/school-logo.png',
            vibrate: [200, 100, 200, 100, 200],
            sound: 'default',
            tag: item.tag || ('school-notif-' + (item.id || Date.now())),
            renotify: true,
            requireInteraction: false,
            data: {
                url: item.url || item.action_url || '/'
            }
        };

        event.waitUntil(
            self.registration.showNotification(item.title || 'School Notification', options)
        );
    }
});

// Handle user clicking the system tray notification banner
self.addEventListener('notificationclick', (event) => {
    event.notification.close();

    if (event.action === 'close') return;

    const targetUrl = (event.notification.data && event.notification.data.url) ? event.notification.data.url : '/';

    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then((clientList) => {
            for (const client of clientList) {
                if ('focus' in client) {
                    if (targetUrl && targetUrl !== '/' && client.url.indexOf(targetUrl) === -1) {
                        client.navigate(targetUrl);
                    }
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
