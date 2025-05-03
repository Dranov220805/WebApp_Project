// Import necessary modules
import auth from './auth.js';
import reg from './reg.js';
// import note from './note.js';
import homeUser from './home-user.js';
import homeUserTrash from './home-user-trash.js';
import TrashNotes from "./home-user-trash.js";
import LabelNotes from "./home-user-label.js";

// Site functionality
homeUser.closeToast();
homeUser.translateItem();

// Auth and Registration functionality
window.toggleConfirmPassword = () => {
    reg.toggleConfirmPassword();
};

window.toggleConfirmChangePassword = () => {
    auth.toggleConfirmChangePassword();
}

window.toggleChangePassword = () => {
    auth.toggleChangePassword();
}

window.togglePassword = () => {
    auth.togglePassword();
};

window.handleAvatarUpload = () => {
    homeUser.handleAvatarUpload();
}

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

window.forgotPassword = () => {
    auth.forgotPassword();
}

window.rememberMe = () => {
    auth.rememberMe();
}

window.changePassword = () => {
    auth.changePassword();
}