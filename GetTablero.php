<?php

ini_set('display_errors', true);
error_reporting(E_ALL);
require_once 'Serializer.php';
session_start();

//verificamos que el peticionario es un jugador registrado
if (isset($_SESSION["usuario"])) {
    //cargamos todas las variables
    $tablero = Serializer::restore("tablero");
    $turno = Serializer::restore("turno");
    $usuarios = Serializer::restore("usuarios");
    //si solo hay un jugador enviamos mensaje de espera
    if (count($usuarios) == 1) {
        echo "<h3 >Esperando otro jugador...</h3>";
    } else {
        //si no existe ganador
        if (!file_exists('./database/ganador')) {
            //mostramos de quien es turno
            if ($turno == 'red') {
                $turno = 'rojo';
            } else {
                $turno = 'amarillo';
            }
            echo "<h3 >Turno $turno</h3>";
        } else {
            // si hay ganador enviamos mensaje de ganador y perdedor
            $ganador = Serializer::restore("ganador");
            $usuario = $_SESSION["usuario"];
            if ($usuario == $ganador) {
                echo "<h2>Felicidades $usuario has ganado</h2>";
            } else {
                echo "<h2>$usuario has perdido!</h2>";
            }
            echo '<form action="login.php" method="POST">';
            echo '<input name="opcion"  value="salir" type="text" hidden> ';
            echo '<input value="Salir" type="submit"> ';
            echo '</form>';
            echo '</br>';
        }
    }
    // dibujamos el tablero
    dibuja($tablero);
}
//dibjua una tabla con las posiciones de las fichas
function dibuja($tablero) {   
    echo "<table class='tablero'>";   
    for ($y = 0; $y < 6; $y++) {
        echo "<tr>";
        for ($x = 0; $x < 7; $x++) {
            echo "<td class='ficha' style='background-color:" . $tablero[$y][$x] . "'></td>";
        }
        echo "</tr>";
    }
    echo "</table";
}
