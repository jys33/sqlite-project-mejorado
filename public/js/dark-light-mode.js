// Dark/Light Mode
if(localStorage.dark === 'true'){
    if(querySelector('.dark-switcher') && querySelector('.theme-icon'))
        darkLightMode();
}

document.addEventListener('DOMContentLoaded', function () {
    if(querySelector('.dark-switcher') && querySelector('.theme-icon'))
        querySelector('.dark-switcher').onclick = darkLightMode;
})

function darkLightMode() {
    var themeIcon, alt;
    if(querySelector('.theme-icon').getAttribute('src') === 'img/moon.svg'){
        localStorage.dark = true;
        themeIcon = 'img/sun.svg';
        alt = 'Light';
    } else{
        localStorage.dark = false;
        themeIcon = 'img/moon.svg';
        alt = 'Dark';
    }
    document.body.classList.toggle('dark');
    if(querySelector('.table')) querySelector('.table').classList.toggle('table-dark');
    // querySelector('.navbar').classList.toggle('navbar-dark');
    // querySelector('.navbar').classList.toggle('navbar-light');
    querySelector('.theme-icon').setAttribute('src', themeIcon);
    querySelector('.theme-icon').setAttribute('alt', alt + ' Mode');
}

//
function querySelector(el){
	return document.querySelector(el);
}