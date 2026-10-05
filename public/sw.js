// Casjoe PWA Service Worker & Push Engine
const CACHE_NAME = 'casjoe-pwa-v1';
const ASSETS_TO_CACHE = [
    '/assets/casjoe_logo.webp',
    '/assets/casjoe_logo.png',
    '/css/style.css',
    '/js/casjoe_theme.js'
];

self.addEventListener('install', function (event) {
    event.waitUntil(
        caches.open(CACHE_NAME).then(function (cache) {
            return cache.addAll(ASSETS_TO_CACHE).catch(function() {});
        })
    );
    self.skipWaiting();
});

self.addEventListener('activate', function (event) {
    event.waitUntil(
        caches.keys().then(function (cacheNames) {
            return Promise.all(
                cacheNames.filter(function (name) {
                    return name !== CACHE_NAME;
                }).map(function (name) {
                    return caches.delete(name);
                })
            );
        })
    );
    return self.clients.claim();
});

// Network-first fetch handler for dynamic SaaS pages
self.addEventListener('fetch', function (event) {
    // Only handle GET requests
    if (event.request.method !== 'GET') return;
    
    // Ignore non-http(s) schemes (like chrome-extension)
    if (!event.request.url.startsWith('http')) return;

    event.respondWith(
        fetch(event.request).catch(function () {
            return caches.match(event.request);
        })
    );
});

// Web Push Notifications
self.addEventListener('push', function (event) {
    if (!(self.Notification && self.Notification.permission === 'granted')) {
        return;
    }

    const data = event.data ? event.data.json() : {};
    const title = data.title || 'Casjoe Notification';
    const options = {
        body: data.body || 'You have an update in Casjoe.',
        icon: data.icon || '/assets/casjoe_logo.png',
        badge: '/assets/casjoe_logo.png',
        vibrate: [100, 50, 100],
        data: {
            url: data.url || '/dashboard'
        }
    };

    event.waitUntil(self.registration.showNotification(title, options));
});

self.addEventListener('notificationclick', function (event) {
    event.notification.close();
    event.waitUntil(
        clients.openWindow(event.notification.data.url)
    );
});
