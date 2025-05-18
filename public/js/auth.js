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
    showLoginToast(message, type = 'success', duration = 1000) {
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
    }

    // Handle login logic
    checkLogin = () => {
        $('#login-button').click(() => {
            const email = $('#email-input').val();
            const password = $('#password-input').val();

            const overlay = document.getElementById('overlay-loading');
            if (overlay) overlay.classList.remove('d-none');

            fetch('/log/login', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email, password })
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    const { token, message, status } = data;

                    $('#password-input').val('');
                    if (status === true) {
                        this.showLoginToast(message, 'success');
                        // Navigate to home page
                        window.location.href = '/home';
                    } else {
                        this.showLoginToast(message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Login error:', error);
                    this.showLoginToast('Something went wrong. Please try again later.', 'danger');
                })
                .finally(() => {
                    if (overlay) overlay.classList.add('d-none');
                });
        });
    }

    // Add this new method to attach token to fetch requests
    fetchWithAuth(url, options = {}) {
        // Default options
        const defaultOptions = {
            headers: {
                'Content-Type': 'application/json',
                ...this.getAuthHeaders()
            }
        };

        // Merge options
        const mergedOptions = {
            ...defaultOptions,
            ...options,
            headers: {
                ...defaultOptions.headers,
                ...(options.headers || {})
            }
        };

        return fetch(url, mergedOptions);
    }

    // Add this method to make AJAX page loads
    loadPage(url) {
        this.fetchWithAuth(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.text();
            })
            .then(html => {
                document.querySelector('#content').innerHTML = html;
            })
            .catch(error => {
                console.error('Error loading page:', error);
                if (error.message.includes('401')) {
                    // If unauthorized, redirect to login
                    window.location.href = '/log/login';
                }
            });
    }

    // Logout logic
    logout() {
        localStorage.removeItem('token'); // Remove the JWT from localStorage
        window.location.href = '/log/login';
    }

    // Get Authorization headers with JWT
    getAuthHeaders() {
        const token = localStorage.getItem('token');
        return token ? { 'Authorization': `Bearer ${token}` } : {};
    }

    toggleChangePassword() {
        const togglePassword = document.querySelector('#toggle-change-password');
        const password = document.querySelector('#new-password-input');

        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle the icon class
        togglePassword.classList.toggle('fa-eye');
        togglePassword.classList.toggle('fa-eye-slash');
    }

    toggleConfirmChangePassword() {
        const togglePassword = document.querySelector('#toggle-change-password-confirm');
        const password = document.querySelector('#confirm-new-password-input');

        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle the icon class
        togglePassword.classList.toggle('fa-eye');
        togglePassword.classList.toggle('fa-eye-slash');
    }

    changePassword() {
        $('#post-change-password-btn').click(() => {
            const newPassword = $('#new-password-input').val();
            const confirmNewPassword = $('#confirm-new-password-input').val();
            const currentPassword = $('#current-password-input').val(); // optional

            const hasUppercase = /[A-Z]/.test(newPassword);
            const hasLowercase = /[a-z]/.test(newPassword);
            const hasNumber = /\d/.test(newPassword);

            if (newPassword !== confirmNewPassword) {
                this.showLoginToast('Passwords do not match!', 'warning');
                return;
            }

            if (!hasUppercase || !hasLowercase || !hasNumber) {
                this.showLoginToast(
                    'Password must contain at least one uppercase letter, lowercase letter, and a number.',
                    'warning');
                return;
            }

            if (newPassword.length < 8) {
                this.showLoginToast(
                    'Password length must be at least 8 characters.',
                    'warning');
                return;
            }

            const overlay = document.getElementById('overlay-loading');
            if (overlay) overlay.classList.remove('d-none');

            fetch('/log/change-password', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    currentPassword,
                    newPassword
                })
            })
                .then(res => res.json())
                .then(data => {
                    if (data['status'] === true) {
                        this.showLoginToast('Password changed successfully!', 'success');
                        $('#changePasswordModal').modal('hide');
                    } else {
                        this.showLoginToast(data.message || 'Failed to change password.', 'danger');
                    }
                })
                .catch(err => {
                    console.error(err);
                    this.showLoginToast('Something went wrong.', 'danger');
                })
                .finally(() => {
                    if (overlay) overlay.classList.add('d-none');
                });
        });
    }

    forgotPassword() {
        $('#reset-password-button').click(() => {
            const email = $('#reset-email-input').val();

            const overlay = document.getElementById('overlay-loading');
            if (overlay) overlay.classList.remove('d-none');

            fetch('/auth/forgot', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ email })
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    const { status, message } = data;

                    if (status === true) {
                        this.showLoginToast(message, 'success');
                    } else {
                        this.showLoginToast(message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Login error:', error);
                    this.showLoginToast('Something went wrong. Please try again later.', 'danger');
                })
                .finally(() => {
                    if (overlay) overlay.classList.add('d-none');
                });
        });
    }

    // Refresh token logic (optional)
    refreshToken() {
        return fetch('/auth/refresh-token', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                ...this.getAuthHeaders()
            }
        })
            .then(res => res.json())
            .then(data => {
                if (data.status === true) {
                    localStorage.setItem('token', data.token); // Update the token
                    return true;
                } else {
                    console.error('Failed to refresh token:', data.message);
                    return false;
                }
            })
            .catch(err => {
                console.error('Refresh token error:', err);
                return false;
            });
    }
}

export default new Auth();
