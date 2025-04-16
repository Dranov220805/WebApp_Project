class Log{
    constructor(){}

    togglePassword() {
        const togglePassword =
            document.querySelector('#toggle-password');

        const password =
            document.querySelector('#password-input');

        // Toggle the type attribute
        const type = password.getAttribute(
            'type') === 'password' ? 'text' : 'password';
        password.setAttribute('type', type);

        // Toggle the eye icon
        if (togglePassword.src.match(
            "/public/img/icon/hide.png")) {
            togglePassword.src =
                "/public/img/icon/view.png";
        } else {
            togglePassword.src =
                "/public/img/icon/hide.png";
        }
    }

    enableButton(){
        if (document.getElementById("id-input").value === "" || document.getElementById("password-input").value === "") {
            document.getElementById('login-button').disabled = true;
            document.getElementById('login-button').style = "background-color: #CCC"
        } else {
            document.getElementById('login-button').disabled = false;
            document.getElementById('login-button').style = "background: linear-gradient(to right, $gradient-color-3 0%, $gradient-color-4 30%, $gradient-color-5 100%);"
        }
    }

    checkLogin = () => {
        $('#login-button').click(() => {
            let username = $('#id-input').val();
            let password = $('#password-input').val();

            fetch('/log/login', {
                method: 'POST',
                body: JSON.stringify({
                    username,
                    password
                })
            })
                .then(response => response.json())
                .then(async (data) => {
                    // console.log(data)
                    let accessToken = data['accessToken'];
                    let roleId = data['roleId'];
                    let message = data['message'];
                    $('#toast-message').html(message);

                    if(data['status'] === true){
                        $('#toast').removeClass('d-none').addClass('bg-success');

                        let timerId = await new Promise(resolve => setTimeout(() => {
                            $('#toast').addClass('d-none').removeClass('bg-success');
                            resolve();
                        }, 100))
                        clearTimeout(timerId);
                    } else {
                        $('#toast').removeClass('d-none').addClass('bg-danger');

                        let timerId = await new Promise(resolve => setTimeout(() => {
                            $('#toast').addClass('d-none').removeClass('bg-danger');
                            resolve();
                        }, 1))
                        clearTimeout(timerId);
                    }

                    return roleId
                })
                .then(roleId => {
                    console.log(roleId);
                    if (String(roleId) === '1') {
                        window.location = '/home-user';
                    }
                })
                .catch(error => {
                    console.log(error)
                })
                .finally(() => {

                })
        })
    }

    redirectToRolePage = () => {

    }
}

export default new Log;
