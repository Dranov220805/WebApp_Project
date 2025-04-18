// syncManager.js
import serviceBroker from './serviceBroker.js';
import dbManager from './dbManager.js';

const syncManager = {
    init: function() {
        // Setup listeners for online/offline events
        window.addEventListener('online', this.onOnline.bind(this));

        // Check if already online
        if (navigator.onLine) {
            this.onOnline();
        }
    },

    onOnline: function() {
        console.log('Back online, syncing data...');
        this.syncAllNotes();
    },

    syncNote: async function(noteId) {
        // First, add to sync queue
        await dbManager.addToSyncQueue(noteId);

        // If online, try to sync immediately
        if (navigator.onLine) {
            this.syncAllNotes();
        } else {
            // Register for background sync
            serviceBroker.requestSync('sync-notes');
        }
    },

    syncAllNotes: async function() {
        try {
            // Get all notes in the sync queue
            const noteIds = await dbManager.getSyncQueue();

            if (noteIds.length === 0) {
                console.log('No notes to sync');
                return;
            }

            console.log(`Syncing ${noteIds.length} notes`);

            // Process each note
            for (const noteId of noteIds) {
                const note = await dbManager.getNoteById(noteId);

                if (!note) {
                    console.warn(`Note ${noteId} not found, removing from sync queue`);
                    await dbManager.removeFromSyncQueue(noteId);
                    continue;
                }

                try {
                    // Send to server
                    const response = await fetch('/api/notes', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify(note)
                    });

                    if (response.ok) {
                        // Mark as synced
                        note.synced = true;
                        await dbManager.saveNote(note);
                        await dbManager.removeFromSyncQueue(noteId);
                        console.log(`Note ${noteId} synced successfully`);
                    } else {
                        console.error(`Failed to sync note ${noteId}: ${response.statusText}`);
                    }
                } catch (error) {
                    console.error(`Error syncing note ${noteId}:`, error);
                    // Will try again later
                }
            }
        } catch (error) {
            console.error('Error during sync:', error);
        }
    }
};

export default syncManager;