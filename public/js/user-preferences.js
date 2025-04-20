class UserPreferences {
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

}

export default new UserPreferences();