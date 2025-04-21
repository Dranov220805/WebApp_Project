class UserPreferences {
    constructor() {}

    // toggleConfirmPassword() {
    //     const toggleButton = document.getElementById('toggle-dark-mode');
    //     const body = document.body;
    //
    //     // Load saved theme
    //     if (localStorage.getItem('theme') === 'dark') {
    //         body.classList.add('dark-mode');
    //     }
    //
    //     toggleButton.addEventListener('click', () => {
    //         body.classList.toggle('dark-mode');
    //         const isDark = body.classList.contains('dark-mode');
    //         localStorage.setItem('theme', isDark ? 'dark' : 'light');
    //     });
    // }

}

export default new UserPreferences();