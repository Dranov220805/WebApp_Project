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
                    const { accessToken, roleId, userName, email, message, status } = data;

                    if (status === true) {
                        // Store the access token in session storage for later use
                        sessionStorage.setItem('accessToken', accessToken);

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

// Configuration
const MAX_IDLE_TIME = 30 * 60 * 1000;           // 30 minutes total session
const REFRESH_THRESHOLD = 2 * 60 * 1000;       // Check at 2 minutes
const RECENT_ACTIVITY_WINDOW =  1 * 30 * 1000;      // Must click within last 1 minute

let idleTimeout;
let refreshTimeout;
let lastClickTime = Date.now();

function refreshToken() {
    fetch('/auth/refresh-token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + sessionStorage.getItem('accessToken')
        },
        body: JSON.stringify({})
    })
        .then(response => {
            if (response.ok) {
                console.log('Access token refreshed');
                lastClickTime = Date.now();
                resetTimers();
            } else {
                console.log('Session expired, redirecting to login');
                window.location.href = '/';
            }
        })
        .catch(err => console.error('Error refreshing token:', err));
}

function handleIdleTimeout() {
    console.log('User idle too long. Logging out...');
    fetch('/log/logout', {
        method: 'GET',
        headers: { 'Content-Type': 'application/json' }
    })
        .then(() => window.location.href = '/')
        .catch(err => console.error('Error logging out:', err));
}

function refreshTokenIfRecentlyActive() {
    const now = Date.now();
    const timeSinceClick = now - lastClickTime;
    console.log(timeSinceClick);

    if (timeSinceClick <= RECENT_ACTIVITY_WINDOW) {
        console.log('Recently active, attempting token refresh...');
        refreshToken();
    } else {
        console.log('Not active recently. No token refresh.');
    }
}

function resetTimers() {
    clearTimeout(idleTimeout);
    clearTimeout(refreshTimeout);

    idleTimeout = setTimeout(handleIdleTimeout, MAX_IDLE_TIME);
    console.log('idle timeout ' + idleTimeout);

    // At 1 min (or whatever REFRESH_THRESHOLD), check if user was recently active
    refreshTimeout = setTimeout(refreshTokenIfRecentlyActive, REFRESH_THRESHOLD);
    console.log('refresh timeout ' + refreshTimeout);
}

function handleClickActivity() {
    lastClickTime = Date.now();
    resetTimers(); // refresh the timers and potentially allow refresh later
}

document.addEventListener('click', handleClickActivity);

// Initial start
resetTimers();

// // Reset all timers (called only on click)
// function resetTimers() {
//     clearTimeout(idleTimeout);
//     clearTimeout(refreshTimeout);
//
//     lastClickTime = Date.now();
//
//     // Logout after 30 minutes of no click
//     idleTimeout = setTimeout(handleIdleTimeout, MAX_IDLE_TIME);
//
//     // Refresh token after 28 minutes of no click
//     refreshTimeout = setTimeout(() => {
//         const timeSinceLastClick = Date.now() - lastClickTime;
//         if (timeSinceLastClick >= REFRESH_THRESHOLD) {
//             refreshToken();
//         }
//     }, REFRESH_THRESHOLD);
// }
//
// // Only count clicks as activity
// document.addEventListener('click', resetTimers);
//
// // Initialize timers on page load
// resetTimers();

export default new Auth();
