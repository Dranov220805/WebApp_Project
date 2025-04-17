import site from './site.js';
// import log from "./log.js";
import auth from './auth.js';
import reg from './reg.js';
import homeUser from './home-user.js';

// site.index();
// site.ajaxTest();
site.closeToast();
site.translateItem();

// log
window.enableButton = () => {
    auth.enableButton();
}

window.togglePassword = () => {
    auth.togglePassword();
}

window.toggleConfirmPassword = () => {
    reg.toggleConfirmPassword();
}

window.checkLogin = () => {
    auth.checkLogin()
}

window.refreshToken = () => {
    auth.refreshToken()
}

window.resetIdleTimer = () => {
    auth.resetIdleTimer()
}

window.handleIdleTimeout = () => {
    auth.handleIdleTimeout()
}

window.resetIdleTimer = () => {
    auth.resetIdleTimer()
}

window.LoadDOMContent_NavbarSidebar = () => {
    homeUser.LoadDOMContent_NavbarSidebar();
}