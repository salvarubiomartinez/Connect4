<?php

ini_set('display_errors', true);
error_reporting(E_ALL);
require_once 'Serializer.php';
session_start();

//varificamos que la request viene de un jugador y pide tirar en una fila
if (isset($_SESSION["usuario"]) && isset($_REQUEST['fila'])) {
    //si el tablero no existe se inicia la partida
    if (!file_exists('./database/tablero')) {
        restart();
    }

    //cargamos todas las variables
    $usuario = $_SESSION["usuario"];
    $usuarios = Serializer::restore("usuarios");       
    $turno = Serializer::restore("turno");
    $color = $usuarios[$usuario];

    //Verificamos que es el turno del usuario que pide tirar
    if ($color == $turno) {
        //realizamos la tirada en la fila pedida
        $fila = $_REQUEST['fila'] - 1;
        tirada($fila, $color);

        //cambiamos el turno
        if ($turno == "red") {
            $turno = "yellow";
        } else {
            $turno = "red";
        }
        Serializer::save($turno, "turno");
        
        //verificamos si hay un ganador
        $ganador = verficarGanador();
        
        //si hay ganador lo guardamos
        if ($ganador) {
            Serializer::save($usuario, "ganador");
        }
    }
}

//función de la tirada
function tirada($fila, $color) {
    $GLOBALS['tablero'] = Serializer::restore("tablero");
    for ($x = 5; $x > -1; $x = $x - 1) {
        if ($GLOBALS['tablero'][$x][$fila] == "") {
            $GLOBALS['tablero'][$x][$fila] = $color;
            $x = 0;
        }
    }
    Serializer::save($GLOBALS['tablero'], "tablero");
}

//función para crear el tablero
function restart() {
    $obj = array();
    for ($y = 0; $y < 6; $y++) {
        $columnas = array();
        for ($x = 0; $x < 7; $x++) {
            array_push($columnas, "");
        }
        array_push($obj, $columnas);
    }
    Serializer::save($obj, "tablero");
}

//función que recorre todo el tablero
function verficarGanador() {
    for ($y = 0; $y < 6; $y++) {
        for ($x = 0; $x < 7; $x++) {
            //si la casilla no esta vacía
            if ($GLOBALS['tablero'][$y][$x] != "") {                
                $color = $GLOBALS['tablero'][$y][$x];
                //miramos todas las posiciones de alrededor para buscar una ficha del mismo color
                if (comprobarLaterales($x, $y, $color)) {
                    return true;
                }
            }
        }
    }
    return false;
}

//función que examina todas las posiciones de alrededor para buscar una ficha del mismo color
function comprobarLaterales($x, $y, $color) {
    for ($i = -1; $i < 2; $i++) {
        for ($j = -1; $j < 2; $j++) {
            //nos saltamos la posición de la ficha actual
            if (!($i == 0 && $j == 0)) {
                //los límites del tablero
                if (-1 < ($y + $i) && ($y + $i) < 6 && -1 < ($x + $j) && ($x + $j) < 7) {
                    //comprobamos que la ficha de la posición lateral es del mismo color
                    if ($GLOBALS['tablero'][$y + $i][$x + $j] == $color) {
                        //en caso afirmativo buscamos otra ficha en la misma dirección
                        if (comprobarLateralConDirección(($y + $i), ($x + $j), $color, $i, $j, 2)) {
                            //hay ganador
                            return true;
                        }
                    }
                }
            }
        }
    }
    //si no hay ficha o no es del mismo color no hay ganador
    return false;
}

function comprobarLateralConDirección($y, $x, $color, $i, $j, $contador) {
    //los límites del tablero
    if (-1 < ($y + $i) && ($y + $i) < 6 && -1 < ($x + $j) && ($x + $j) < 7) {
        //si la nueva posición es del mismo color
        if ($GLOBALS['tablero'][$y + $i][$x + $j] == $color) {
            //sumamos al contador
            $contador++;
            //si llegamos a cuatro fichas hay ganador
            if ($contador == 4) {
                return true;
            }
            //si todavía no hay ganador miramos la posición siguiente en la misma dirección
            return comprobarLateralConDirección(($y + $i), ($x + $j), $color, $i, $j, $contador);
        }
    }
    //si no hay ficha o no es del mismo color no hay ganador
    return false;
}

?>