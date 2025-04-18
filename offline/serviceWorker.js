// serviceBroker.js
const serviceBroker = {
    // Check if service workers are supported
    isSupported: 'serviceWorker' in navigator,

    // Register the service worker
    register: async () => {
        if (!serviceBroker.isSupported) {
            console.warn('Service workers not supported');
            return false;
        }

        try {
            const registration = await navigator.serviceWorker.register('/service-worker.js');
            console.log('Service Worker registered with scope:', registration.scope);
            return registration;
        } catch (error) {
            console.error('Service Worker registration failed:', error);
            return false;
        }
    },

    // Request a background sync
    requestSync: async (syncTag = 'sync-notes') => {
        if (!serviceBroker.isSupported || !('SyncManager' in window)) {
            console.warn('Background Sync not supported');
            return false;
        }

        try {
            const registration = await navigator.serviceWorker.ready;
            await registration.sync.register(syncTag);
            return true;
        } catch (error) {
            console.error('Sync registration failed:', error);
            return false;
        }
    },

    // Send a message to the service worker
    sendMessage: async (message) => {
        if (!serviceBroker.isSupported) return false;

        const registration = await navigator.serviceWorker.ready;
        registration.active.postMessage(message);
        return true;
    },

    // Listen for messages from the service worker
    onMessage: (callback) => {
        if (!serviceBroker.isSupported) return;

        navigator.serviceWorker.addEventListener('message', event => {
            callback(event.data);
        });
    }
};

export default serviceBroker;