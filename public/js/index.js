import site from './site.js';
// import log from "./log.js";
import auth from './auth.js';
import reg from './reg.js';
// import note from './note.js';

// notes.loadNotes(); ← you can still call methods on the exported instance

import homeUser from './home-user.js';

// site.index();
// site.ajaxTest();
site.closeToast();
site.translateItem();

window.toggleConfirmPassword = () => {
    reg.toggleConfirmPassword();
}

// log
window.togglePassword = () => {
    auth.togglePassword();
}

window.checkLogin = () => {
    auth.checkLogin();
}

window.refreshToken = () => {
    auth.refreshToken();
}

window.resetIdleTimer = () => {
    auth.resetIdleTimer();
}

window.handleIdleTimeout = () => {
    auth.handleIdleTimeout();
}

// window.refreshNotes = () => {
//     note.loadNotes();
// }
//
// window.showToast = () => {
//     note.showToast();
// }
