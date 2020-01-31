document.addEventListener('DOMContentLoaded', function () {
    if (document.querySelector('.alert')) {
        fadeOut(document.querySelector('.alert'));
    }
});

function fadeOut(el) {
    setTimeout(function () { el.style.display = 'none'; }, 15000);
}

/**
 * 
 * @param {Genera contraseñas aleatorias} length 
 */
function generate(length = 12) {
    var uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var lowercase = 'abcdefghijklmnopqrstuvwxyz';
    var numbers = '0123456789';
    var symbols = '!"#$%&\'()*+,-./:;<=>?@^[\\]^_`{|}~';
    var all = uppercase + lowercase + numbers + symbols;
    var password = '';
    for (var index = 0; index < length; index++) {
        var character = Math.floor(Math.random() * all.length);
        password += all.substring(character, character + 1);
    }
    return password;
}

function showLoginPassword() {
    let pwd = document.getElementById("login-password");
    if (pwd.type === "password") {
        pwd.type = "text";
    } else {
        pwd.type = "password";
    }
}

function showRegisterPasswords() {
    let pwd1 = document.getElementById("register-password");
    let pwd2 = document.getElementById("register-confirm-password");
    if (pwd1.type === "password" && pwd2.type === "password") {
        pwd1.type = pwd2.type = "text";
    } else {
        pwd1.type = pwd2.type = "password";
    }
}