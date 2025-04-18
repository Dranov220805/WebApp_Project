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
            const username = $('#email-input').val();
            const password = $('#password-input').val();

            fetch('/log/login', {
                method: 'POST',
                body: JSON.stringify({
                    username,
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

    redirectToRolePage = () => {
        // Future: Add logic based on roleId if needed
    }
}



// Configuration
const MAX_IDLE_TIME = 30 * 60 * 1000; // 30 minutes
const REFRESH_THRESHOLD = 28 * 60 * 1000; // Trigger refresh after 28 minutes of no click

let idleTimeout;
let refreshTimeout;
let lastClickTime = Date.now();

// Refresh the access token
function refreshToken() {
    fetch('/auth/refresh-token', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Authorization': 'Bearer ' + localStorage.getItem('accessToken')
        },
        body: JSON.stringify({})
    })
        .then(response => {
            if (response.ok) {
                console.log('Access token refreshed');
                lastClickTime = Date.now();
                resetTimers(); // restart the countdowns
            } else {
                console.log('Session expired, redirecting to login');
                window.location.href = '/';
            }
        })
        .catch(err => console.error('Error refreshing token:', err));
}

// Handle session expiration due to inactivity
function handleIdleTimeout() {
    console.log('User has been idle too long. Logging out...');
    fetch('/log/logout', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json'
        }
    })
        .then(() => window.location.href = '/')
        .catch(err => console.error('Error logging out:', err));
}

// Reset all timers (called only on click)
function resetTimers() {
    clearTimeout(idleTimeout);
    clearTimeout(refreshTimeout);

    lastClickTime = Date.now();

    // Logout after 30 minutes of no click
    idleTimeout = setTimeout(handleIdleTimeout, MAX_IDLE_TIME);

    // Refresh token after 28 minutes of no click
    refreshTimeout = setTimeout(() => {
        const timeSinceLastClick = Date.now() - lastClickTime;
        if (timeSinceLastClick >= REFRESH_THRESHOLD) {
            refreshToken();
        }
    }, REFRESH_THRESHOLD);
}

// Only count clicks as activity
document.addEventListener('click', resetTimers);

// Initialize timers on page load
resetTimers();




// // Timer and activity monitoring
// let idleTimeout;
// const MAX_IDLE_TIME = 30 * 60 * 1000; // 30 minutes in milliseconds
// const REFRESH_INTERVAL = 5 * 60 * 1000; // Refresh token every 5 minutes
//
// // Function to refresh the access token
// function refreshToken() {
//     fetch('/auth/refresh-token', {
//         method: 'POST',
//         headers: {
//             'Content-Type': 'application/json',
//             'Authorization': 'Bearer ' + localStorage.getItem('accessToken') // or cookies
//         },
//         body: JSON.stringify({}) // body can be empty, just to trigger token refresh
//     })
//         .then(response => {
//             if (response.ok) {
//                 // If the token refresh is successful, reset the idle timer
//                 console.log('Access token refreshed');
//             } else {
//                 // If the refresh failed (e.g., session expired), handle it
//                 console.log('Session expired, redirecting to login');
//                 window.location.href = '/';
//             }
//         })
//         .catch(err => console.error('Error refreshing token:', err));
// }
//
// // Function to reset idle timer on user activity
// function resetIdleTimer() {
//     clearTimeout(idleTimeout);
//     idleTimeout = setTimeout(handleIdleTimeout, MAX_IDLE_TIME);
//     // Refresh the token periodically while the user is active
//     setInterval(refreshToken, REFRESH_INTERVAL);
// }
//
// // Handle user inactivity (session expiration)
// function handleIdleTimeout() {
//     console.log('User is idle for too long. Logging out...');
//     fetch('/log/logout', {
//         method: 'GET',
//         headers: {
//             'Content-Type': 'application/json'
//         }
//     }).then(response => {
//         window.location.href = '/'; // Redirect to login page after session expiration
//     }).catch(err => console.error('Error logging out:', err));
// }
//
// // Set up event listeners for user activity (e.g., mouse movement, key press)
// document.addEventListener('mousemove', resetIdleTimer);
// document.addEventListener('keypress', resetIdleTimer);
// document.addEventListener('click', resetIdleTimer);
// document.addEventListener('scroll', resetIdleTimer);
//
// // Initialize the idle timer when the page loads
// resetIdleTimer();

export default new Auth();
