// Import necessary modules
import auth from './auth.js';
import reg from './reg.js';
// import note from './note.js';
import homeUser from './home-user.js';

// Site functionality
homeUser.closeToast();
homeUser.translateItem();

// Auth and Registration functionality
window.toggleConfirmPassword = () => {
    reg.toggleConfirmPassword();
};

window.togglePassword = () => {
    auth.togglePassword();
};

window.checkLogin = () => {
    auth.checkLogin();
};

window.checkRegister = () => {
    reg.checkRegister();
};

window.refreshToken = () => {
    auth.refreshToken();
};

window.resetIdleTimer = () => {
    auth.resetIdleTimer();
};

window.handleIdleTimeout = () => {
    auth.handleIdleTimeout();
};

//
// // index.js
// import serviceBroker from '/offline/serviceBroker.js';
// import dbManager from '/offline/dbManager.js';
// import syncManager from '/offline/syncManager.js';
// import uiManager from '/offline/uiManager.js';
//
// // Initialize the application
// async function initApp() {
//     // Initialize UI components
//     uiManager.init();
//
//     // Initialize database
//     await dbManager.init();
//
//     // Register service worker
//     await serviceBroker.register();
//
//     // Setup sync functionality
//     syncManager.init();
//
//     // Check online status
//     uiManager.updateOnlineStatus();
//
//     // Listen for service worker messages
//     serviceBroker.onMessage(message => {
//         console.log('Message from service worker:', message);
//         // Handle specific messages
//         if (message.type === 'SYNC_COMPLETE') {
//             uiManager.showNotification('All notes synchronized!');
//         }
//     });
//
//     console.log('Application initialized');
// }
//
// // Start the application
// document.addEventListener('DOMContentLoaded', initApp);
//
// // Example of creating a note and syncing
// window.createNote = async function(title, content) {
//     const note = {
//         id: Date.now().toString(),
//         title,
//         content,
//         timestamp: new Date().toISOString(),
//         synced: false
//     };
//
//     // Save to local database
//     await dbManager.saveNote(note);
//
//     // Add to UI
//     uiManager.addNoteToUI(note);
//
//     // Try to sync
//     syncManager.syncNote(note.id);
//
//     return note;
// };
//
// // Use the serviceBroker
// serviceBroker.requestSync('custom-sync-tag');

// Optional: You can implement other actions like note refresh and toast display here when needed
// window.refreshNotes = () => {
//     note.loadNotes();
// };
//
// window.showToast = () => {
//     note.showToast();
// };
