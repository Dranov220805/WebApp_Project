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
                // Notes Store: Stores the actual note data
                if (!this.db.objectStoreNames.contains('notes')) {
                    const notesStore = this.db.createObjectStore('notes', { keyPath: 'id' });
                    notesStore.createIndex('title', 'title', { unique: false });
                    notesStore.createIndex('content', 'content', { unique: false });
                    notesStore.createIndex('lastModified', 'lastModified', { unique: false });
                    notesStore.createIndex('syncStatus', 'syncStatus', { unique: false }); // 'synced', 'pending_create', 'pending_update', 'pending_delete'
                    notesStore.createIndex('isPinned', 'isPinned', { unique: false });
                }

                // Sync Queue: Tracks which notes need which operations
                if (!this.db.objectStoreNames.contains('syncQueue')) {
                    const syncQueueStore = this.db.createObjectStore('syncQueue', { keyPath: 'noteId' });
                    syncQueueStore.createIndex('operation', 'operation', { unique: false }); // 'create', 'update', 'delete'
                }
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
            if (!this.db) {
                reject('DB not initialized');
                return;
            }
            const transaction = this.db.transaction(['notes'], 'readwrite');
            const notesStore = transaction.objectStore('notes');
            // Ensure lastModified is set
            note.lastModified = note.lastModified || Date.now();
            const request = notesStore.put(note);

            request.onsuccess = () => resolve(note);
            request.onerror = event => reject(event.target.error);
        });
    },

    getAllNotes: function() {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                reject('DB not initialized');
                return;
            }
            const transaction = this.db.transaction(['notes'], 'readonly');
            const notesStore = transaction.objectStore('notes');
            // Filter out notes marked for deletion if you implement soft delete that way
            const request = notesStore.getAll();

            request.onsuccess = () => {
                // Filter out notes marked as 'pending_delete' if they shouldn't be displayed
                const notes = request.result.filter(note => note.syncStatus !== 'pending_delete');
                resolve(notes);
            }
            request.onerror = event => reject(event.target.error);
        });
    },

    getNoteById: function(id) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                reject('DB not initialized');
                return;
            }
            const transaction = this.db.transaction(['notes'], 'readonly');
            const notesStore = transaction.objectStore('notes');
            const request = notesStore.get(id);

            request.onsuccess = () => resolve(request.result);
            request.onerror = event => reject(event.target.error);
        });
    },

    deleteNote: function (noteId) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                reject('DB not initialized');
                return;
            }
            const transaction = this.db.transaction(['notes'], 'readwrite');
            const notesStore = transaction.objectStore('notes');
            const request = notesStore.delete(noteId);

            request.onsuccess = () => resolve();
            request.onerror = event => reject(event.target.error);
        });
    },

    // Sync Queue Management
    addToSyncQueue: function (noteId, operation) { // operation: 'create', 'update', 'delete'
        return new Promise((resolve, reject) => {
            if (!this.db) {
                reject('DB not initialized');
                return;
            }
            const transaction = this.db.transaction(['syncQueue'], 'readwrite');
            const syncStore = transaction.objectStore('syncQueue');
            const request = syncStore.put({ noteId: noteId, operation: operation, timestamp: Date.now() });

            request.onsuccess = () => resolve({ noteId, operation });
            request.onerror = event => reject(event.target.error);
        });
    },

    getSyncQueue: function () {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                reject('DB not initialized');
                return;
            }
            const transaction = this.db.transaction(['syncQueue'], 'readonly');
            const syncStore = transaction.objectStore('syncQueue');
            const request = syncStore.getAll(); // Returns array of {noteId, operation, timestamp}

            request.onsuccess = () => resolve(request.result || []);
            request.onerror = event => reject(event.target.error);
        });
    },

    removeFromSyncQueue: function (noteId) {
        return new Promise((resolve, reject) => {
            if (!this.db) {
                reject('DB not initialized');
                return;
            }
            const transaction = this.db.transaction(['syncQueue'], 'readwrite');
            const syncStore = transaction.objectStore('syncQueue');
            const request = syncStore.delete(noteId);

            request.onsuccess = () => resolve(noteId);
            request.onerror = event => reject(event.target.error);
        });
    },

    // Helper to generate UUID for offline notes
    generateUUID: function () {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function (c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
};

export default dbManager;