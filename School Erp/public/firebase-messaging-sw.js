/**
 * Firebase Cloud Messaging Background Push Service Worker for School ERP
 */
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

firebase.initializeApp({
    projectId: 'erp-1-23074',
    messagingSenderId: '628420405085',
    appId: '1:628420405085:web:schoolerp'
});

const messaging = firebase.messaging();

// Handles background push when app/browser is closed
messaging.onBackgroundMessage((payload) => {
    const notificationTitle = payload.notification?.title || payload.data?.title || 'School Notification';
    const notificationOptions = {
        body: payload.notification?.body || payload.data?.body || '',
        icon: '/images/school-logo.png',
        badge: '/images/school-logo.png',
        vibrate: [200, 100, 200, 100, 200],
        sound: 'default',
        tag: 'school-erp-push-' + Date.now(),
        renotify: true,
        requireInteraction: true,
        data: {
            url: payload.data?.action_url || '/'
        }
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

self.addEventListener('notificationclick', (event) => {
    event.notification.close();
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
