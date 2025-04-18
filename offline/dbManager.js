// dbManager.js
const dbManager = {
    db: null,
    DB_NAME: 'NotesDatabase',
    DB_VERSION: 1,

    init: function() {
        return new Promise((resolve, reject) => {
            const request = indexedDB.open(this.DB_NAME, this.DB_VERSION);

            request.onerror = event => {
                console.error('IndexedDB error:', event.target.errorCode);
                reject(event.target.errorCode);
            };

            request.onupgradeneeded = event => {
                this.db = event.target.result;
                // Create object store for notes
                const notesStore = this.db.createObjectStore('notes', { keyPath: 'id' });
                notesStore.createIndex('title', 'title', { unique: false });
                notesStore.createIndex('content', 'content', { unique: false });
                notesStore.createIndex('timestamp', 'timestamp', { unique: false });
                notesStore.createIndex('synced', 'synced', { unique: false });

                // Create object store for sync queue
                this.db.createObjectStore('syncQueue', { keyPath: 'id' });
            };

            request.onsuccess = event => {
                this.db = event.target.result;
                console.log('IndexedDB initialized successfully');
                resolve();
            };
        });
    },

    saveNote: function(note) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['notes'], 'readwrite');
            const notesStore = transaction.objectStore('notes');
            const request = notesStore.put(note);

            request.onsuccess = () => resolve(note);
            request.onerror = event => reject(event.target.error);
        });
    },

    getAllNotes: function() {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['notes'], 'readonly');
            const notesStore = transaction.objectStore('notes');
            const request = notesStore.getAll();

            request.onsuccess = () => resolve(request.result);
            request.onerror = event => reject(event.target.error);
        });
    },

    getNoteById: function(id) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['notes'], 'readonly');
            const notesStore = transaction.objectStore('notes');
            const request = notesStore.get(id);

            request.onsuccess = () => resolve(request.result);
            request.onerror = event => reject(event.target.error);
        });
    },

    // More methods for CRUD operations...

    // Methods for managing sync queue
    addToSyncQueue: function(noteId) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['syncQueue'], 'readwrite');
            const syncStore = transaction.objectStore('syncQueue');
            const request = syncStore.put({ id: noteId, timestamp: Date.now() });

            request.onsuccess = () => resolve(noteId);
            request.onerror = event => reject(event.target.error);
        });
    },

    getSyncQueue: function() {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['syncQueue'], 'readonly');
            const syncStore = transaction.objectStore('syncQueue');
            const request = syncStore.getAll();

            request.onsuccess = () => resolve(request.result.map(item => item.id));
            request.onerror = event => reject(event.target.error);
        });
    },

    removeFromSyncQueue: function(noteId) {
        return new Promise((resolve, reject) => {
            const transaction = this.db.transaction(['syncQueue'], 'readwrite');
            const syncStore = transaction.objectStore('syncQueue');
            const request = syncStore.delete(noteId);

            request.onsuccess = () => resolve(noteId);
            request.onerror = event => reject(event.target.error);
        });
    }
};

export default dbManager;