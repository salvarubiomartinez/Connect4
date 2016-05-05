<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html>
    <head>
        <meta charset="UTF-8">
        <script src="conecta4.js" type="text/javascript"></script>
        <link href="conecta4.css" rel="stylesheet" type="text/css"/>
        <title></title>
    </head>
    <body class='center'>
        <?php
        session_start();
        if (isset($_SESSION["usuario"])) {
            echo '<h2>' . $_SESSION["usuario"] . "</h2>";
            echo '<table class="botones">';
            echo '<tr>';
            for ($i = 1; $i < 8; $i++) {
                echo "<th><button id='$i' class='boton'>$i</button></th>";
            }
            echo '</tr>';
            echo '</table>';
            echo '<div id="mensaje"></div>';
            echo '<div id="tablero"></div>';
        } else {
            echo '<h1>Bienvenido a Conecta4</h1>';  
            echo '<form action="login.php" method="POST">';
            echo '<input name="opcion"  value="entrar" type="text" hidden> ';
            echo '<input value="Jugar" type="submit"> ';
            echo '</form>';
            if(isset($_GET['error'])){
                $error = $_GET['error'];
                echo "<br/>$error";
            }
        }
        ?>
    </body>
</html>
