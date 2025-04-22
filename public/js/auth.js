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
    showLoginToast(message, type = 'success', duration = 2000) {
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
const IDLE_LIMIT = 30 * 60 * 1000; // 30 minutes
const WARNING_BEFORE_LOGOUT = 25 * 60 * 1000; // Warn at 25 minutes
const REFRESH_THRESHOLD = IDLE_LIMIT - 1 * 60 * 1000; // Refresh 1 minute before logout
const RECENT_ACTIVITY_WINDOW = 5 * 1000; // Must be active in the last 5 minute
const HEARTBEAT_INTERVAL = 5 * 60 * 1000; // Ping server every 5 minutes

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
        .then(() => window.location.href = '/log/login')
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
        console.log('You will be logged out in the next 5 minute(s) if inactive.');
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
                console.log('Session will expire soon. Please stay active.');
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

export default new Auth();
