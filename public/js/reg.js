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

    // Hanlde register logic
    checkRegister = () => {
        $('#register-button').click(() => {
            const email = $('#email-input').val();
            const username = $('#username-input').val();
            const password = $('#password-input').val();
            const confirmPassword = $('#password-input-confirm').val();

            const hasUppercase = /[A-Z]/.test(password);
            const hasLowercase = /[a-z]/.test(password);
            const hasNumber = /\d/.test(password);

            // Check if all fields are filled
            if (!username || !password || !email || !confirmPassword) {
                this.showRegisterToast('Please fill in all fields.', 'warning');
                return;
            }

            // Check email format
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
                this.showRegisterToast(
                    'Password length must be more than 8 characters',
                    'warning'
                );
                return;
            }

            // If all checks pass, continue with registration
            fetch('reg/register', {
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
                // .then(response => response.json())
                .then(response => {
                    console.log(response); // Log the response to inspect it

                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }

                    // Check if content type is JSON
                    const contentType = response.headers.get('Content-Type');
                    if (contentType && contentType.includes('application/json')) {
                        return response.json(); // Parse as JSON if content type is correct
                    } else {
                        throw new Error('Response is not JSON');
                    }
                })
                .then(data => {
                    console.log(data);
                    const { accessToken, roleId, userName, email, message, status } = data;

                    if (status === true) {
                        sessionStorage.setItem('accessToken', accessToken);

                        this.showRegisterToast(message, 'success');

                        setTimeout(() => {
                            if (String(roleId) === '1') {
                                window.location.href = '/home';
                            }
                        }, 1000);
                    } else {
                        this.showRegisterToast(message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Register error:', error);
                    this.showRegisterToast('Something went wrong. Please try again later', 'warning');
                })
        });
    };

}

export default new Reg();