<?php

ini_set('display_errors', true);
error_reporting(E_ALL);

require_once 'Serializer.php';
session_start();

//El login permite entrar o salir de la aplicación
if (isset($_REQUEST['opcion'])) {
    $opcion = $_REQUEST['opcion'];
    if ($opcion == 'entrar') {
        entrar();
    }
    //antes de salir verificamos que se trata de un usuario que ha iniciado sesión
    if ($opcion == 'salir' && isset($_SESSION["usuario"])) {
        salir();
    }   
}
//redirigimos a index
header('location: index.php');

//la función de acceso al juego
function entrar() {
    //verificamos la existencia de la carpeta de la base de datos
    if (!file_exists('./database')){
        mkdir('./database');
    }
    
    //leemos o creamos el archivo usuarios
    if (file_exists('./database/usuarios')) {
        $usuarios = Serializer::restore("usuarios");
    } else {
        $usuarios = array();
    }
    //Según el número de usuarios se sigue una opción
    switch (count($usuarios)) {
        //no hay jugadores, se crea el primero
        case 0:
            $usuario = 'Jugador amarillo';
            $usuarios[$usuario] = 'yellow';
            break;
        //Si hay un jugador creamos el del color contrario
        case 1:
            if (isset($usuarios['Jugador amarillo'])) {
                $usuario = 'Jugador rojo';
                $usuarios[$usuario] = 'red';
                $turno = 'yellow';
            } else {
                $usuario = 'Jugador amarillo';
                $usuarios[$usuario] = 'yellow';
                $turno = 'red';
            }

            Serializer::save($turno, "turno");

            break;
        //si ya hay dos jugadores devolvemos error
        case 2:
            header("location: index.php?error=Lo sentimos, pero ya hay dos jugadores. Inténtalo más tarde.");
            exit();
            break;
    }

    //salvamos el archivo de usuarios y entramos en sesión
    Serializer::save($usuarios, "usuarios");
    $_SESSION["usuario"] = $usuario;
}

//la función de salida de la aplicación
function salir() {
    //borramos el usuario del archivo
    if (file_exists('./database/usuarios')) {
        $usuarios = Serializer::restore("usuarios");
        $usuario = $_SESSION["usuario"];
        unset($usuarios[$usuario]);
        Serializer::save($usuarios, "usuarios");
        if (count($usuarios) == 0) {
            unlink('./database/usuarios');
        }
    }
    //borramos todos los archivos de la partida
    if (file_exists('./database/ganador')) {
        unlink('./database/ganador');
    }
    if (file_exists('./database/turno')) {
        unlink('./database/turno');
    }
    if (file_exists('./database/tablero')) {
        unlink('./database/tablero');
    }
    //salimos de sesión
    session_destroy();
}
