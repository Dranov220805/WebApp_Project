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

    showRegisterToast(message, type = 'success', duration = 3000) {
        const toast = document.getElementById("toast");
        const messageElement = document.getElementById("toast-message");
        const closeBtn = document.getElementById("toast-close");

        messageElement.innerText = message;
        toast.classList.remove("d-none", "bg-success", "bg-danger");
        toast.classList.add(`bg-${type}`);

        // Show toast
        toast.classList.remove(`d-none`);

        // Auto hide after duration
        const hideTimeout = setTimeout(() => {
            toast.classList.add("d-none");
        }, duration);

        // Manual close
        closeBtn.onclick = () => {
            toast.classList.remove("d-none");
            clearTimeout(hideTimeout);
        };
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

            if (!username || !password || !email || !confirmPassword) {
                this.showRegisterToast('Please fill in all fields.', 'warning');
                return;
            }

            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(email)) {
                this.showRegisterToast('Please enter a valid email address.', 'warning');
                return;
            }

            if (password !== confirmPassword) {
                this.showRegisterToast('Passwords do not match!', 'warning');
                return;
            }

            if (!hasUppercase || !hasLowercase || !hasNumber) {
                this.showRegisterToast(
                    'Password must contain at least one uppercase letter, lowercase letters, and numbers.',
                    'warning'
                );
                return;
            }

            if (password.length < 8) {
                this.showRegisterToast('Password length must be more than 8 characters', 'warning');
                return;
            }

            // Register POST
            fetch('register', {
                method: 'POST',
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({
                    username,
                    password,
                    email
                })
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok during registration');
                    }
                    return response.json();
                })
                .then(data => {
                    const { status, message } = data;

                    if (status === true) {
                        if (message === "Email already exists") {
                            this.showRegisterToast('Email already exists!', 'warning');
                        } else {
                            this.showRegisterToast('Registration successful! Logging you in...', 'success');

                            // Proceed to login if registration is successful
                            return fetch('/log/login', {
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
                                        // this.showLoginToast(message, 'success');

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
                                        // this.showLoginToast(message, 'danger');
                                    }
                                })
                                .catch(error => {
                                    console.error('Login error:', error);
                                    // Show error toast for network issues
                                    this.showLoginToast('Something went wrong. Please try again later.', 'danger');
                                });;
                        }
                    } else {
                        throw new Error(message || 'Registration failed');
                    }
                })
                // .then(loginResponse => {
                //     if (!loginResponse.ok) {
                //         throw new Error('Login after registration failed');
                //     }
                //     return loginResponse.json();
                // })
                // .then(loginData => {
                //     const { accessToken, message, status } = loginData;
                //
                //     if (status === true) {
                //         this.showRegisterToast('Logged in successfully!', 'success');
                //
                //         sessionStorage.setItem('accessToken', accessToken);
                //
                //         // Redirect user
                //         setTimeout(() => {
                //             window.location.href = '/home';
                //         }, 1000);
                //     } else {
                //         throw new Error(message || 'Login failed');
                //     }
                // })
                .catch(error => {
                    console.error('Register/Login error:', error);
                    this.showRegisterToast(error.message || 'Something went wrong. Please try again.', 'danger');
                });
        });
    };

}

export default new Reg();