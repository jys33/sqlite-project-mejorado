let strongPasswordMessage = 'Elige una contraseña más segura. Prueba con una combinación de letras, números y símbolos.';

document.addEventListener('DOMContentLoaded', function () {

    // Login form
    let login = new FormValidator('login', [
        {
            name: 'user_name',
            display: 'usuario',
            rules: 'required|max_length[50]'
        }, {
            name: 'password',
            display: 'contraseña',
            rules: 'required'
        }], function (errors, evt) {
            if (errors.length > 0) {
                let N = errors.length;
                for (let i = 0; i < N; i++) {
                    errors[i].element.classList.add('is-invalid');
                    errors[i].element.nextElementSibling.innerHTML = errors[i].message;
                }
                errors[0].element.focus();
                evt.preventDefault();
            } else {
                disabledBtn();
            }
        });

    // Register form
    let register = new FormValidator('register', [
        {
            name: 'last_name',
            display: 'apellido',
            rules: 'required|alpha|min_length[3]|max_length[50]'
        }, {
            name: 'first_name',
            display: 'nombre',
            rules: 'required|alpha|min_length[3]|max_length[50]'
        }, {
            name: 'user_email',
            display: 'correo electrónico',
            rules: 'required|valid_email|max_length[50]',
        }, {
            name: 'user_name',
            display: 'usuario',
            rules: 'required|min_length[3]|max_length[50]|alpha_dash',
        }, {
            name: 'password',
            display: 'contraseña',
            rules: 'required|min_length[8]|max_length[50]|callback_check_password'
        }, {
            name: 'confirm_password',
            display: 'confirmar contraseña',
            rules: 'required|matches[password]|max_length[50]'
        }], function (errors, evt) {
            if (errors.length > 0) {
                let N = errors.length;
                for (let i = 0; i < N; i++) {
                    errors[i].element.classList.add('is-invalid');
                    errors[i].element.nextElementSibling.innerHTML = errors[i].message;
                }
                errors[0].element.focus();
                evt.preventDefault();
            } else {
                disabledBtn();
            }
        });
    register.registerCallback('check_password', function (value) {
        if (passwordIsStrong(value)) {
            return true;
        }

        return false;
    })
        .setMessage('check_password', strongPasswordMessage);

    // Forgot password
    let forgot_password = new FormValidator('forgot_password', [
        {
            name: 'user_email',
            display: 'correo electrónico',
            rules: 'required|valid_email|max_length[50]'
        }], function (errors, evt) {
            if (errors.length > 0) {
                let N = errors.length;
                for (let i = 0; i < N; i++) {
                    errors[i].element.classList.add('is-invalid');
                    errors[i].element.nextElementSibling.innerHTML = errors[i].message;
                }
                errors[0].element.focus();

                evt.preventDefault();
            } else {
                disabledBtn();
            }
        });

    // Reset password
    let reset_password = new FormValidator('reset_password', [
        {
            name: 'new_password',
            display: 'contraseña',
            rules: 'required|min_length[8]|max_length[50]|callback_check_password'
        }, {
            name: 'confirm_new_password',
            display: 'confirmación',
            rules: 'required|matches[new_password]|max_length[50]'
        }], function (errors, evt) {
            if (errors.length > 0) {
                let N = errors.length;
                for (let i = 0; i < N; i++) {
                    errors[i].element.classList.add('is-invalid');
                    errors[i].element.nextElementSibling.innerHTML = errors[i].message;
                }
                errors[0].element.focus();
                evt.preventDefault();
            } else {
                disabledBtn();
            }
        });

    reset_password.registerCallback('check_password', function (value) {
        if (passwordIsStrong(value)) {
            return true;
        }

        return false;
    })
        .setMessage('check_password', strongPasswordMessage);

    // Change password
    let change_password = new FormValidator('change_password', [
        {
            name: 'current_password',
            display: 'contraseña actual',
            rules: 'required'
        }, {
            name: 'new_password',
            display: 'contraseña',
            rules: 'required|min_length[8]|max_length[50]|callback_check_password'
        }, {
            name: 'confirm_new_password',
            display: 'confirmación',
            rules: 'required|matches[new_password]|max_length[50]'
        }], function (errors, evt) {
            if (errors.length > 0) {
                let N = errors.length;
                for (let i = 0; i < N; i++) {
                    errors[i].element.classList.add('is-invalid');
                    errors[i].element.nextElementSibling.innerHTML = errors[i].message;
                }
                errors[0].element.focus();
                evt.preventDefault();
            } else {
                disabledBtn();
            }
        });

    change_password.registerCallback('check_password', function (value) {
        if (passwordIsStrong(value)) {
            return true;
        }

        return false;
    })
        .setMessage('check_password', strongPasswordMessage);

    // Registrar  asociado
    let register_asociado = new FormValidator('register_asociado', [
        {
            name: 'apellido',
            display: 'apellido',
            rules: 'required|alpha|min_length[3]|max_length[50]'
        }, {
            name: 'nombre',
            display: 'nombre',
            rules: 'required|alpha|min_length[3]|max_length[50]'
        }, {
            name: 'nro_cuil',
            display: 'número de cuil',
            rules: 'required|numeric|min_length[11]|max_length[11]',
        }, {
            name: 'tipo_documento',
            display: 'tipo de documento',
            rules: 'required',
        }, {
            name: 'nro_documento',
            display: 'número de documento',
            rules: 'required|numeric|min_length[8]|max_length[8]',
        }, {
            name: 'fech_nacimiento',
            display: 'fecha de nacimiento',
            rules: 'required'
        }, {
            name: 'categoria',
            display: 'categoría',
            rules: 'required'
        }, {
            name: 'email',
            display: 'correo electrónico',
            rules: 'required|valid_email|max_length[50]'
        }, {
            name: 'nro_tel_movil',
            display: 'teléfono móvil',
            rules: 'required|numeric'
        }, {
            name: 'domicilio',
            display: 'domicilio',
            rules: 'required'
        }, {
            name: 'provincia',
            display: 'provincia',
            rules: 'required'
        }, {
            name: 'localidad',
            display: 'localidad',
            rules: 'required'
        }], function (errors, evt) {
            if (errors.length > 0) {
                let N = errors.length;
                for (let i = 0; i < N; i++) {
                    errors[i].element.classList.add('is-invalid');
                    errors[i].element.nextElementSibling.innerHTML = errors[i].message;
                }
                errors[0].element.focus();
                evt.preventDefault();
            } else {
                disabledBtn();
            }
        });
});