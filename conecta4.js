
var bucle;
var reload = false;
//función base de peticiones AJAX
function send(url, params, callback) {
    var datos = new XMLHttpRequest();
    datos.open("POST", url, true);
    datos.onreadystatechange = function () {
        if (datos.readyState == 4 && datos.status == 200) {
            callback(datos.responseText);
        }
        ;
    };
    datos.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    datos.send(params);
}
//Al cargar...
window.onload = function () {
    //iniciamos los eventos de los botones
    var el = document.getElementsByClassName("boton");
    for (var i = 0; i < el.length; i++) {
        el[i].onclick = tirar;
    }
    //si han iniciado partida dibujamos el tablero
    var tablero = document.getElementById("tablero");
    if (tablero != null) {
        refrescar();
    }
    //evitamos que al apretar F5 se reinicie la partida
    window.onkeydown = function (event) {
        if (event.keyCode == 116) {
            reload = true;
        }
    };
};

//al salir enviamos una petición de salida al servidor
window.onbeforeunload = function (e) {
    e = e || window.event;
    var tablero = document.getElementById("tablero");
    if (tablero != null && reload == false) {
        clearTimeout(bucle);
        salir();
        return "si confirma saldrá del juego";
    }
}

//función de petición AJAX para tirar
function tirar(elEvento) {
    var esdeveniment = elEvento || window.event;
    var url = "conecta4.php";
    var params = "fila=" + esdeveniment.target.id;
    var callback = function (data) {
        document.getElementById("mensaje").innerHTML = data;
    };
    send(url, params, callback);    
}

//función de petición AJAX para salir del juego
function salir() {
    var url = "login.php";
    var params = "opcion=salir";
    var callback = function () {
    };
    send(url, params, callback);
}

//función de petición AJAX para dibujar el tablero
function GetTablero() {
    var url = "GetTablero.php";
    var params = "";
    var callback = function (data) {
        document.getElementById("tablero").innerHTML = data;
    };
    send(url, params, callback);
}

//función para refrescar continuamente el tablero
function refrescar() {
    GetTablero();
    bucle = setTimeout(refrescar, 500);
}