//https://cheatsheetseries.owasp.org/cheatsheets/AJAX_Security_Cheat_Sheet.html
'use strict';

let req;

document.addEventListener("DOMContentLoaded", function () {

    if (document.getElementById("provincia")) {
        loadProvinces();
        document.getElementById("provincia").onchange = onChange;
    }
});

function loadProvinces() {
    makeAjaxCall("POST", "../json/provincias.json?nocache=", showProvinces, null);
}

function onChange() {
    // Obtener la referencia a la lista
    let listOfProvinces = document.getElementById("provincia");

    if (listOfProvinces !== null && listOfProvinces.selectedIndex != 0) {
        document.getElementById("localidad").removeAttribute("disabled");
        // Obtener el valor de la opción seleccionada
        let val = listOfProvinces.options[listOfProvinces.selectedIndex].value;

        let regexp = /^[A-Z]$/;
        if (regexp.test(val) && val.length == 1) {
            // let parameters = "provincia=" + encodeURIComponent(val) + "&localidad=" + encodeURIComponent(val);
            // makeAjaxCall("POST", "../json/facade.php?nocache=", test, parameters);
            makeAjaxCall("POST", "../json/" + val + ".json?nocache=", showLocalities, null);
        }
    } else {
        document.getElementById("localidad").setAttribute("disabled", true);
        document.getElementById("localidad").options.length = 0;//elimina todos los elementos options
        return;
    }
}

function makeAjaxCall(method, url, callBack, parameters) {
    req = inicializa_xhr();
    req.open(method, url + Math.random(), true);
    req.withCredentials = true;
    req.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    req.onreadystatechange = function () {
        if (req.readyState != 4 || req.status != 200) return;
        callBack();
    };
    req.send(parameters);
}

function inicializa_xhr() {
    if (window.XMLHttpRequest) {
        return new XMLHttpRequest();
    } else if (window.ActiveXObject) {
        return new ActiveXObject("Microsoft.XMLHTTP");
    }
}

function showProvinces() {
    let listOfProvinces = document.getElementById("provincia");
    defaultOptions(listOfProvinces, "Seleccione una provincia");

    let newOption;
    JSON.parse(req.responseText).forEach(el => {
        newOption = document.createElement("option");
        newOption.value = el.codigo_3166_2.substring(3);
        newOption.text = el.nombre;
        // add the new option 
        try {
            // this will fail in DOM browsers but is needed for IE 
            listOfProvinces.add(newOption);
        } catch (e) {
            listOfProvinces.appendChild(newOption);
        }
    });
}

function showLocalities() {
    let listOfLocalities = document.getElementById("localidad");
    listOfLocalities.options.length = 0;
    defaultOptions(listOfLocalities, "Seleccione una localidad");

    let newOption;
    JSON.parse(req.responseText).forEach(el => {
        newOption = document.createElement("option");
        newOption.value = el.id;
        newOption.text = `${el.nombre} (${el.cp})`;
        // add the new option 
        try {
            // this will fail in DOM browsers but is needed for IE
            listOfLocalities.add(newOption);
        } catch (e) {
            listOfLocalities.appendChild(newOption);
        }
    });
}

function defaultOptions(list, msg) {
    list.options[0] = new Option(msg);
    list.options[0].setAttribute("value", "");
    list.options[0].setAttribute("disabled", true);
    list.options[0].setAttribute("selected", true);
}
