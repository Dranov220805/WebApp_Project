class Auth {
    constructor() {}

    togglePassword() {
        const togglePassword = document.querySelector('#toggle-password');
        const password = document.querySelector('#password-input');

        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle the icon class
        togglePassword.classList.toggle('fa-eye');
        togglePassword.classList.toggle('fa-eye-slash');
    }

    // Show toast message with manual close
    showLoginToast(message, type = 'success', duration = 3000) {
        const toast = document.getElementById("toast");
        const messageElement = document.getElementById("toast-message");
        const closeBtn = document.getElementById("toast-close");

        // Set message and toast class based on type
        messageElement.innerText = message;
        toast.classList.remove("d-none", "bg-success", "bg-danger"); // Reset classes
        toast.classList.add(`bg-${type}`);

        // Show toast
        toast.classList.remove("d-none");

        // Auto-hide after duration
        const hideTimeout = setTimeout(() => {
            toast.classList.add("d-none");
        }, duration);

        // Allow manual close
        closeBtn.onclick = () => {
            toast.classList.add("d-none");
            clearTimeout(hideTimeout); // Clear the auto-hide timer
        };
    }

    // Handle login logic
    checkLogin = () => {
        $('#login-button').click(() => {
            const email = $('#email-input').val();
            const password = $('#password-input').val();

            fetch('/log/login', {
                method: 'POST',
                body: JSON.stringify({
                    email,
                    password
                })
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    const { roleId, userName, email, message, status } = data;

                    if (status === true) {

                        // Show success toast message
                        this.showLoginToast(message, 'success');

                        setTimeout(() => {
                            // Redirect user based on role
                            if (String(roleId) === '1') {
                                window.location.href = '/home';
                            } else if (String(roleId) === '2') {
                                window.location.href = '/admin-dashboard';
                            }
                        }, 1000);
                    } else {
                        // Show error toast message
                        this.showLoginToast(message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Login error:', error);
                    // Show error toast for network issues
                    this.showLoginToast('Something went wrong. Please try again later.', 'danger');
                });
        });
    }

}

// === Constants ===
const IDLE_LIMIT = 3 * 60 * 1000; // 30 minutes
const WARNING_BEFORE_LOGOUT = 2 * 60 * 1000; // Warn at 25 minutes
const REFRESH_THRESHOLD = IDLE_LIMIT - 1 * 60 * 1000; // Refresh 1 minute before logout
const RECENT_ACTIVITY_WINDOW = 1 * 1000; // Must be active in the last 30 seconds
const HEARTBEAT_INTERVAL = 1 * 60 * 1000; // Ping server every 1 minute

// === State ===
let idleTimeout, refreshTimeout, warningTimer, heartbeatInterval, countdownInterval = null;
let lastClickTime = Date.now();

// === Event Handlers ===
function handleClickActivity() {
    lastClickTime = Date.now();
    resetTimers();
}

// === Logout Function ===
function handleIdleTimeout() {
    console.log('User idle too long. Logging out...');
    fetch('/log/logout')
        .then(() => window.location.href = '/')
        .catch(err => console.error('Logout error:', err));
}

// === Refresh Session Logic ===
function refreshSessionIfRecentlyActive() {
    const now = Date.now();
    const timeSinceClick = now - lastClickTime;

    if (timeSinceClick <= RECENT_ACTIVITY_WINDOW) {
        refreshToken().then(success => {
            if (success) resetTimers();
        });
    } else {
        console.log('User inactive recently. Skipping token refresh.');
    }
}

function refreshToken() {
    return fetch('/auth/refresh-token', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
    })
        .then(res => {
            if (res.ok) {
                console.log('Token refreshed');
                lastClickTime = Date.now();
                return true;
            } else {
                console.log('Session expired');
                window.location.href = '/';
                return false;
            }
        })
        .catch(err => {
            console.error('Refresh token failed:', err);
            return false;
        });
}

// === Countdown Warning ===
function showCountdownWarning() {
    let countdown = 5;
    if (countdownInterval) clearInterval(countdownInterval);

    countdownInterval = setInterval(() => {
        if (countdown <= 0) {
            clearInterval(countdownInterval);
            return;
        }
        this.showLoginToast(`You will be logged out in 1 minute(s) if inactive.`, 'danger');
        countdown--;
    }, 60000);
}

// === Reset Timers ===
function resetTimers() {
    clearTimeout(idleTimeout);
    clearTimeout(refreshTimeout);
    clearTimeout(warningTimer);
    if (countdownInterval) clearInterval(countdownInterval);

    idleTimeout = setTimeout(handleIdleTimeout, IDLE_LIMIT);
    refreshTimeout = setTimeout(refreshSessionIfRecentlyActive, REFRESH_THRESHOLD);
    warningTimer = setTimeout(showCountdownWarning, IDLE_LIMIT - WARNING_BEFORE_LOGOUT);
}

// === Heartbeat Ping ===
function sendHeartbeat() {
    fetch('/auth/heartbeat')
        .then(res => res.json())
        .then(data => {
            if (data.sessionExpired) {
                window.location.href = '/logout';
            }
            if (data.showWarning) {
                this.showLoginToast('Session will expire soon. Please stay active.', 'danger');
            }
        })
        .catch(err => console.error('Heartbeat failed:', err));
}

// === Event Binding & Initialization ===
['click'].forEach(event => {
    document.addEventListener(event, handleClickActivity);
});

resetTimers();
heartbeatInterval = setInterval(sendHeartbeat, HEARTBEAT_INTERVAL);


// // Constants
// const IDLE_LIMIT = 3 * 60 * 1000; // 30 min
// const WARNING_BEFORE_LOGOUT = 2 * 60 * 1000; // Show countdown at 25 min
// const REFRESH_THRESHOLD = 2 * 60 * 1000; // Refresh token if recently active
// const RECENT_ACTIVITY_WINDOW = 30 * 1000; // 30 sec
// const HEARTBEAT_INTERVAL = 1 * 60 * 1000; // Ping server every 5 min
//
// // State variables
// let idleTimeout, refreshTimeout, warningTimer, heartbeatInterval;
// let lastClickTime = Date.now();
// let countdownInterval = null;
//
// // Handle click or user activity
// function handleClickActivity() {
//     lastClickTime = Date.now();
//     resetTimers();
// }
//
// // Logout user
// function handleIdleTimeout() {
//     console.log('User idle too long. Logging out...');
//     fetch('/log/logout')
//         .then(() => window.location.href = '/')
//         .catch(err => console.error('Logout error:', err));
// }
//
// // Refresh token if user was recently active
// function refreshTokenIfRecentlyActive() {
//     const now = Date.now();
//     const timeSinceClick = now - lastClickTime;
//
//     if (timeSinceClick <= RECENT_ACTIVITY_WINDOW) {
//         refreshToken();
//     } else {
//         console.log('User inactive recently. Skipping refresh.');
//     }
// }
//
// // Show countdown toast warning
// function showCountdownWarning() {
//     let countdown = 5;
//
//     countdownInterval = setInterval(() => {
//         if (countdown <= 0) {
//             clearInterval(countdownInterval);
//             return;
//         }
//         auth.showLoginToast(`You will be logged out in ${countdown} minute(s) if inactive.`, 'danger', 60000);
//         countdown--;
//     }, 60000);
// }
//
// // Reset all timeouts
// function resetTimers() {
//     clearTimeout(idleTimeout);
//     clearTimeout(refreshTimeout);
//     clearTimeout(warningTimer);
//     if (countdownInterval) clearInterval(countdownInterval);
//
//     idleTimeout = setTimeout(handleIdleTimeout, IDLE_LIMIT);
//     refreshTimeout = setTimeout(refreshTokenIfRecentlyActive, REFRESH_THRESHOLD);
//     warningTimer = setTimeout(showCountdownWarning, IDLE_LIMIT - WARNING_BEFORE_LOGOUT);
// }
//
// // Heartbeat to keep session alive
// function sendHeartbeat() {
//     fetch('/auth/heartbeat')
//         .then(res => res.json())
//         .then(data => {
//             if (data.sessionExpired) {
//                 window.location.href = '/logout';
//             }
//         })
//         .catch(err => console.error('Heartbeat failed:', err));
// }
//
// // Start everything
// ['click', 'mousemove', 'keydown'].forEach(event => {
//     document.addEventListener(event, handleClickActivity);
// });
//
// resetTimers();
// heartbeatInterval = setInterval(sendHeartbeat, HEARTBEAT_INTERVAL);


// Configuration
// const MAX_IDLE_TIME = 30 * 60 * 1000;           // 30 minutes total session
// const REFRESH_THRESHOLD = 2 * 60 * 1000;       // Check at 2 minutes
// const RECENT_ACTIVITY_WINDOW =  1 * 30 * 1000;      // Must click within last 1 minute
//
// let idleTimeout;
// let refreshTimeout;
// let lastClickTime = Date.now();
//
// function refreshToken() {
//     fetch('/auth/refresh-token', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
//         },
//         body: JSON.stringify({})
//     })
//         .then(response => {
//             if (response.ok) {
//                 console.log('Access token refreshed');
//                 lastClickTime = Date.now();
//                 resetTimers();
//             } else {
//                 console.log('Session expired, redirecting to login');
//                 window.location.href = '/';
//             }
//         })
//         .catch(err => console.error('Error refreshing token:', err));
// }
//
// function handleIdleTimeout() {
//     console.log('User idle too long. Logging out...');
//     fetch('/log/logout', {
//         method: 'GET',
//         headers: { 'Content-Type': 'application/json' }
//     })
//         .then(() => window.location.href = '/')
//         .catch(err => console.error('Error logging out:', err));
// }
//
// function refreshTokenIfRecentlyActive() {
//     const now = Date.now();
//     const timeSinceClick = now - lastClickTime;
//     console.log(timeSinceClick);
//
//     if (timeSinceClick <= RECENT_ACTIVITY_WINDOW) {
//         console.log('Recently active, attempting token refresh...');
//         refreshToken();
//     } else {
//         console.log('Not active recently. No token refresh.');
//     }
// }
//
// function resetTimers() {
//     clearTimeout(idleTimeout);
//     clearTimeout(refreshTimeout);
//
//     idleTimeout = setTimeout(handleIdleTimeout, MAX_IDLE_TIME);
//     console.log('idle timeout ' + idleTimeout);
//
//     // At 1 min (or whatever REFRESH_THRESHOLD), check if user was recently active
//     refreshTimeout = setTimeout(refreshTokenIfRecentlyActive, REFRESH_THRESHOLD);
//     console.log('refresh timeout ' + refreshTimeout);
// }
//
// function handleClickActivity() {
//     lastClickTime = Date.now();
//     resetTimers(); // refresh the timers and potentially allow refresh later
// }
//
// document.addEventListener('click', handleClickActivity);
//
// // Initial start
// resetTimers();

export default new Auth();
