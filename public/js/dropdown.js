'use strict';

let req;
let listaDeProvincias;
let listaDeLocalidades;

/**
 * if(typeof var !== 'undefined')
 * if(var)
 * if(var !== null)
 */

document.addEventListener("DOMContentLoaded", function () {
    if (document.getElementById("provincia") && document.getElementById("localidad")) {
        listaDeProvincias = document.getElementById("provincia");
        listaDeLocalidades = document.getElementById("localidad");
        listaDeProvincias.onchange = onChange;
    }
});

function onChange() {
    if (listaDeProvincias.selectedIndex != 0) {
        listaDeLocalidades.removeAttribute("disabled");
        // Obtener el valor de la opción seleccionada
        let id_provincia = parseInt(listaDeProvincias.options[listaDeProvincias.selectedIndex].value);
        // rango de números permitidos 1-24 (ID en la tabla provincias)
        if (Number.isInteger(id_provincia) && id_provincia > 0 && id_provincia < 25) {
            let parameters = "id_provincia=" + encodeURIComponent(id_provincia);
            makeAjaxCall("POST", "server_response.php?nocache=", mostrarLocalidades, parameters);
        } else {
            listaDeLocalidades.setAttribute("disabled", true);
            listaDeLocalidades.options.length = 0;//elimina todos los elementos options
            return;
        }
    } else {
        listaDeLocalidades.setAttribute("disabled", true);
        listaDeLocalidades.options.length = 0;//elimina todos los elementos options
        return;
    }
}

function mostrarLocalidades() {
    listaDeLocalidades.options.length = 0;
    defaultSelectOptions(listaDeLocalidades, "Seleccione una localidad");
    let newOption;
    // No podremos parsear si la respuesta es un echo de error
    try {
        JSON.parse(req.responseText).forEach(el => {
            newOption = document.createElement("option");
            newOption.value = el.id_localidad;
            newOption.text = `${el.nombre} (${el.cp})`;
            // add the new option 
            try {
                // this will fail in DOM browsers but is needed for IE
                listaDeLocalidades.add(newOption);
            } catch (e) {
                listaDeLocalidades.appendChild(newOption);
            }
        });
    } catch (e) {
        console.log(req.responseText); return;
    }
}

function getLocalities() {
    listaDeLocalidades.innerHTML = req.responseText;
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

function defaultSelectOptions(list, msg) {
    list.options[0] = new Option(msg);
    list.options[0].setAttribute("value", "");
    list.options[0].setAttribute("disabled", true);
    list.options[0].setAttribute("selected", true);
}