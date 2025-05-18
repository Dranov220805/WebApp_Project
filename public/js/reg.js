class Reg {
    constructor() {}

    toggleConfirmPassword() {
        const togglePassword = document.querySelector('#toggle-password-confirm');
        const password = document.querySelector('#password-input-confirm');

        const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle the icon class
        togglePassword.classList.toggle('fa-eye');
        togglePassword.classList.toggle('fa-eye-slash');
    }

    showRegisterToast(message, type = 'success', duration = 1000) {
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

    checkRegister = () => {
        $('#register-button').click(() => {
            const email = $('#email-input').val();
            const username = $('#username-input').val();
            const password = $('#password-input').val();
            const confirmPassword = $('#password-input-confirm').val();

            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /\d/.test(password);

            const overlay = document.getElementById('overlay-loading');
            if (overlay) overlay.classList.remove('d-none');

            // Validation checks
            if (!username || !password || !email || !confirmPassword) {
                this.showRegisterToast('Please fill in all fields.', 'warning');
                if (overlay) overlay.classList.add('d-none');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                this.showRegisterToast('Please enter a valid email address.', 'warning');
                if (overlay) overlay.classList.add('d-none');
                return;
            }

            if (password !== confirmPassword) {
                this.showRegisterToast('Passwords do not match!', 'warning');
                if (overlay) overlay.classList.add('d-none');
                return;
            }

            if (!hasUppercase || !hasLowercase || !hasNumber) {
                this.showRegisterToast(
                    'Password must contain at least one uppercase letter, lowercase letters, and numbers.',
                    'warning');
                if (overlay) overlay.classList.add('d-none');
                return;
            }

            if (password.length < 8) {
                this.showRegisterToast(
                    'Password length must be more than 8 characters',
                    'warning');
                if (overlay) overlay.classList.add('d-none');
                return;
            }

            // Send registration request
            fetch('/reg/register', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ username, password, email })
            })
                .then(response => response.json())
                .then(data => {
                    const { status, message } = data;

                    if (status === true) {
                        this.showRegisterToast('Registration successful! Logging you in...', 'success');

                        // Auto-login - will set the JWT cookie on the server side
                        return fetch('/log/login', {
                            method: 'POST',
                            headers: {
                                "Content-Type": "application/json"
                            },
                            body: JSON.stringify({ email, password })
                        })
                            .then(response => response.json())
                            .then(loginData => {
                                if (loginData.status === true) {
                                    // Store token in localStorage for client-side use if needed
                                    localStorage.setItem('token', loginData.token);

                                    this.showRegisterToast(loginData.message, 'success');

                                    // Redirect after a short delay - cookie is automatically sent
                                    setTimeout(() => {
                                        window.location.href = '/home';
                                    }, 1000);
                                } else {
                                    throw new Error(loginData.message || 'Login after registration failed');
                                }
                            });
                    } else {
                        if (message === "Email already exists") {
                            this.showRegisterToast('Email already exists!', 'warning');
                            $('#email-input').val('');
                            $('#username-input').val('');
                        } else {
                            throw new Error(message || 'Registration failed');
                        }
                    }
                })
                .catch(error => {
                    console.error('Register/Login error:', error);
                    this.showRegisterToast(error.message || 'Something went wrong. Please try again.', 'danger');
                })
                .finally(() => {
                    if (overlay) overlay.classList.add('d-none');
                });
        });
    };

}

export default new Reg();