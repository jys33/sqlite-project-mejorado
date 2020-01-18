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