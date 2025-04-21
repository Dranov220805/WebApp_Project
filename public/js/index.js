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