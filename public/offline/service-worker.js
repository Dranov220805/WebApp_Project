// service-worker.js
const CACHE_NAME = 'notes-app-v1';
const ASSETS_TO_CACHE = [
    '/',
    '/index.html',
    '/css/styles.css',
    '/js/index.js',
    '/js/serviceBroker.js',
    '/js/dbManager.js',
    '/js/syncManager.js',
    '/js/uiManager.js',
    '/images/logo.png',
    // Add other important assets
];

// Install event - cache essential files
self.addEventListener('install', event => {
    event.waitUntil(
        caches.open(CACHE_NAME)
            .then(cache => cache.addAll(ASSETS_TO_CACHE))
    );
});

// Activate event - clean up old caches
self.addEventListener('activate', event => {
    event.waitUntil(
        caches.keys().then(cacheNames => {
            return Promise.all(
                cacheNames.map(cacheName => {
                    if (cacheName !== CACHE_NAME) {
                        return caches.delete(cacheName);
                    }
                })
            );
        })
    );
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
    // Skip for API requests
    if (event.request.url.includes('/api/')) {
        return;
    }

    event.respondWith(
        caches.match(event.request)
            .then(cachedResponse => {
                if (cachedResponse) {
                    return cachedResponse;
                }

                return fetch(event.request)
                    .then(response => {
                        // Don't cache if not a valid response
                        if (!response || response.status !== 200 || response.type !== 'basic') {
                            return response;
                        }

                        // Clone the response
                        let responseToCache = response.clone();

                        caches.open(CACHE_NAME)
                            .then(cache => {
                                cache.put(event.request, responseToCache);
                            });

                        return response;
                    });
            })
    );
});

// Sync event - for background sync
self.addEventListener('sync', event => {
    if (event.tag === 'sync-notes') {
        event.waitUntil(
            // This will be called when the user comes back online
            // We'll post a message to the client to initiate sync
            self.clients.matchAll().then(clients => {
                clients.forEach(client => {
                    client.postMessage({
                        type: 'TRIGGER_SYNC',
                        timestamp: Date.now()
                    });
                });
            })
        );
    }
});

// Message handling
self.addEventListener('message', event => {
    console.log('Message received in SW:', event.data);

    // Handle specific messages
    if (event.data.type === 'SYNC_COMPLETE') {
        self.clients.matchAll().then(clients => {
            clients.forEach(client => {
                client.postMessage({
                    type: 'SYNC_COMPLETE',
                    timestamp: Date.now()
                });
            });
        });
    }
});