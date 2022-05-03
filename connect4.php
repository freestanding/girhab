<link rel="stylesheet" href="style.css">
<?php

if (!isset($_REQUEST["accio"])) {
    
?>
    <h3>Connecta 4 on-line</h3>
    <h4>Crear partida</h4>
    <form action="connect4.php">
        <label for="jugador1">Jugador 1 (master): </label>
        <input type="text" name="jugador1">
        <input type="hidden" name="accio" value="crear_partida">
        <input type="submit" value="Crear partida">
    </form>
    <h4>Buscar partida</h4>
    <form action="connect4.php">
        <label for="jugador2">Jugador 2 (convidat): </label>
        <input type="text" name="jugador2">
        <input type="hidden" name="accio" value="buscar_partida">
        <input type="submit" value="Buscar partida">
    </form>
<?php
} else if ($_REQUEST["accio"] == "crear_partida") {
    
    
    $con = mysqli_connect("localhost", "admin", "admin", "connect4") or exit(mysqli_connect_error());
    $sql = "INSERT INTO partides VALUES (null, '" . date("Y-m-d") . "','" . $_REQUEST["jugador1"] . "',null,0,1)";
    echo $sql . "<br>";
    $result = mysqli_query($con, $sql) or exit(mysqli_error($con));
    $id = mysqli_insert_id($con);
?>
    <h3>Partida creada num <?php echo $id ?></h3>
    <h4>Esperant contrincant...</h4>
    <script>
        setTimeout(() => {
            window.location = "connect4.php?contador=1&accio=comprovar_partida&jugador=1&partida=<?php echo $id ?>";
        }, 1000);
    </script>
<?php
} else if ($_REQUEST["accio"] == "comprovar_partida") { 
    $contador = $_REQUEST["contador"] + 1;
    $jugador = $_REQUEST["jugador"];
    $partida = $_REQUEST["partida"];
?>
    <h3>Partida creada num <?php echo $partida ?></h3>
    <?php
    $con = mysqli_connect("localhost", "admin", "admin", "connect4") or exit(mysqli_connect_error());
    $sql = "SELECT * FROM partides WHERE id_partida = $partida";
    $result = mysqli_query($con, $sql) or exit(mysqli_error($con));
    $reg = mysqli_fetch_array($result);
    if ($reg["nom_jugador2"] != "") {
        
    ?>
        <h4>Tenim contrincant!!!</h4>
        <?php

        
        ?>
        <script>
            setTimeout(() => {
                window.location = "connect4.php?accio=moviment_partida&jugador=1&partida=<?php echo $partida ?>";
            }, 1000);
        </script>
    <?php
    } else {
    ?>
        <h4>Esperant contrincant (<?php echo $contador ?>)...</h4>
        <script>
            setTimeout(() => {
                window.location = "connect4.php?contador=<?php echo $contador ?>&accio=comprovar_partida&jugador=1&partida=<?php echo $partida ?>";
            }, 1000);
        </script>
    <?php
    }
} else if ($_REQUEST["accio"] == "enviar_moviment") {
    
    
    
    $jugador = $_REQUEST["jugador"];
    $partida = $_REQUEST["partida"];
    $columna = $_REQUEST["columna"];
    $con = mysqli_connect("localhost", "admin", "admin", "connect4") or exit(mysqli_connect_error());
    
    $sql = "INSERT INTO moviments VALUES (NULL,'" . date("H:i:s") . "',NULL,$jugador,$columna,$partida)";
    
    $result = mysqli_query($con, $sql) or exit(mysqli_error($con));
    
    $sql = "UPDATE partides SET torn = IF(torn = 1,2,1) WHERE id_partida = $partida";
    $result = mysqli_query($con, $sql) or exit(mysqli_error($con));
    echo '<style>h3{font-size:40px;}</style>';

    ?>
    <h3>Connecta 4</h3>
    <?php pintar_taulell($partida);


    if (no_hi_ha_guanyador($partida)) {
    }else {
        $sql = "SELECT * FROM partides WHERE id_partida = $partida;";
        $result = mysqli_query($con, $sql) or exit(mysqli_error($con));
        $reg = mysqli_fetch_array($result); 
        $f = $reg["guanyador"];
            echo "<script>
            alert('El jugador $f guanya la partida');
            window.location.replace('connect4.php');
            </script>";
    }

    ?>

    <h4>Moviment gravat. Esperant moviment del jugador <?php echo $jugador == 1 ? "2" : "1" ?></h4>

    <script>
        setTimeout(() => {
            window.location = "connect4.php?accio=moviment_partida&jugador=<?php echo $jugador ?>&partida=<?php echo $partida ?>";
        }, 2000);
    </script>
<?php

} else if ($_REQUEST["accio"] == "buscar_partida") {
    
?>
    <h3>Partides disponibles</h3>
    <?php
    
    $con = mysqli_connect("localhost", "admin", "admin", "connect4") or exit(mysqli_connect_error());
    $sql = "SELECT * FROM partides WHERE ISNULL(nom_jugador2);";
    $result = mysqli_query($con, $sql) or exit(mysqli_error($con));
    while ($reg = mysqli_fetch_array($result)) {
    ?>
        <span>Partida <?php echo $reg["id_partida"] ?> creada el <?php $reg["data"] ?> per <?php echo $reg["nom_jugador1"] ?></span>
        <a href="connect4.php?partida=<?php echo $reg["id_partida"] ?>&accio=connectar_a_partida&jugador2=<?php echo $_REQUEST["jugador2"] ?>">Connectar a partida</a>
    <?php
    }
} else if ($_REQUEST["accio"] == "connectar_a_partida") {
    
    
    $con = mysqli_connect("localhost", "admin", "admin", "connect4") or exit(mysqli_connect_error());
    
    $jugador2 = $_REQUEST["jugador2"];
    $sql = "UPDATE partides SET nom_jugador2 = '$jugador2' WHERE id_partida=" . $_REQUEST["partida"];
    $result = mysqli_query($con, $sql) or exit(mysqli_error($con));
    ?>
    <h3>CONNECTAT A LA PARTIDA <?php echo $_REQUEST["partida"] ?></h3>
    <h4>Esperant moviment del jugador 1...</h4>
    

    <script>
        setTimeout(() => {
            window.location = "connect4.php?accio=moviment_partida&jugador=2&partida=<?php echo $_REQUEST["partida"] ?>";
        }, 2000);
    </script>
    <?php
} else if ($_REQUEST["accio"] == "moviment_partida") {
    $jugador = $_REQUEST["jugador"];
    $partida = $_REQUEST["partida"];

    $con = mysqli_connect("localhost", "admin", "admin", "connect4") or exit(mysqli_connect_error());
    $sql1 = "SELECT * FROM partides WHERE id_partida = $partida";
    $result1 = mysqli_query($con, $sql1) or exit(mysqli_error($con));
    $reg1 = mysqli_fetch_array($result1);
    $ganador = $reg1["guanyador"];
    if($ganador != 0){
        $sql = "SELECT * FROM partides WHERE id_partida = $partida;";
        $result = mysqli_query($con, $sql) or exit(mysqli_error($con));
        $reg = mysqli_fetch_array($result); 
        $f = $reg["guanyador"];
        echo "<script>
        alert('El jugador $f guanya la partida');
        window.location.replace('connect4.php');
        </script>";
    }

    $con = mysqli_connect("localhost", "admin", "admin", "connect4") or exit(mysqli_connect_error());
    $sql = "SELECT * FROM partides WHERE id_partida = $partida;";
    $result = mysqli_query($con, $sql) or exit(mysqli_error($con));
    $reg = mysqli_fetch_array($result);
    if ($reg["torn"] == $jugador) {
        
        
        echo '<style>h3{font-size:40px;}</style>';
        echo "<h3>Connecta 4</h3>";
        pintar_taulell($partida);
        
    ?>
        <form action="connect4.php" style="text-align: center;">
            <div class="form" style=display:inline-flex;>
                <input type="hidden" name="jugador" value="<?php echo $jugador ?>">
                <input type="hidden" name="partida" value="<?php echo $partida ?>">
                <input type="hidden" name="accio" value="enviar_moviment">
                <div class="cont">
                    <input type="submit" id="num" name="columna" value="1">
                </div>
                <div class="cont">
                    <input type="submit" id="num" name="columna" value="2">
                </div>
                <div class="cont">
                    <input type="submit" id="num" name="columna" value="3">
                </div>
                <div class="cont">
                    <input type="submit" id="num" name="columna" value="4">
                </div>
                <div class="cont">
                    <input type="submit" id="num" name="columna" value="5">
                </div>
                <div class="cont">
                    <input type="submit" id="num" name="columna" value="6">
                </div>
                <div class="cont">
                    <input type="submit" id="num" name="columna" value="7">
                </div>
            </div>
        </form>
    <?php
    } else {
        echo '<style>h3{font-size:40px;}</style>';
    ?>

        <h3>Esperant moviment del jugador <?php echo $jugador == 1 ? "2" : "1" ?>...</h3>
        <?php
        pintar_taulell($partida);
        ?>
        <script>
            setTimeout(() => {
                window.location = "connect4.php?accio=moviment_partida&jugador=<?php echo $jugador ?>&partida=<?php echo $partida ?>";
            }, 1000);
        </script>
<?php
    }
}

function pintar_taulell($partida)
{
    $con = mysqli_connect("localhost", "admin", "admin", "connect4") or exit(mysqli_connect_error());
    $sql = "SELECT * FROM moviments WHERE id_partida=$partida";
    $result = mysqli_query($con, $sql) or exit(mysqli_error($con));
    $taulell = [
        [0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0]
    ];
    while ($reg = mysqli_fetch_array($result)) {
        $num_col = $reg["columna_moviment"];
        $jugador = $reg["jugador"];
        $num_col--; 
        $c = 5;
        while ($taulell[$c][$num_col] != 0) {
            $c--;
        }
        
        $taulell[$c][$num_col] = $jugador;
    }
    
   
    for ($t = 0; $t < 6; $t++) {
        echo '<div style="display:flex;">';
        for ($tt = 0; $tt < 7; $tt++) {
            if ($taulell[$t][$tt] == 1) {
                echo ' ▍' . $taulell[$t][$tt];
            } else if ($taulell[$t][$tt] == 2) {
                echo ' ▍' . $taulell[$t][$tt] ;
            } else {
                echo ' ▍' . $taulell[$t][$tt];
            }
        }                
        echo '▐';
        echo "</div>";
    }
    echo "<br>";
}

function no_hi_ha_guanyador($partida)
{
    $con = mysqli_connect("localhost", "admin", "admin", "connect4") or exit(mysqli_connect_error());
    $sql = "SELECT * FROM moviments WHERE id_partida=$partida";
    $result = mysqli_query($con, $sql) or exit(mysqli_error($con));
    $taulell = [
        [0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0],
        [0, 0, 0, 0, 0, 0, 0]
    ];
    while ($reg = mysqli_fetch_array($result)) {
        $num_col = $reg["columna_moviment"];
        $jugador = $reg["jugador"];
        $num_col--; 
        $c = 5;
        while ($taulell[$c][$num_col] != 0) {
            $c--;
        }
        $taulell[$c][$num_col] = $jugador;
    }
          
    for ($t = 0; $t < 6; $t++) {
        $n_uns = 0;
        for ($tt = 0; $tt < 7; $tt++) {
            if ($taulell[$t][$tt] == 1) {
                $n_uns++;
                if ($n_uns == 4) {
                    $sql2 = "UPDATE partides SET guanyador = 1 WHERE id_partida = $partida";
                    $result = mysqli_query($con, $sql2) or exit(mysqli_error($con));
                    return false;
                }
            } else
                $n_uns = 0;
        }
    }
    
    for ($t = 0; $t < 6; $t++) {
        $n_uns = 0;
        for ($tt = 0; $tt < 7; $tt++) {
            if ($taulell[$t][$tt] == 2) {
                $n_uns++;
                if ($n_uns == 4) {
                    $sql2 = "UPDATE partides SET guanyador = 2 WHERE id_partida = $partida";
                    $result = mysqli_query($con, $sql2) or exit(mysqli_error($con));
                    return false;
                }
            } else
                $n_uns = 0;
        }
    }
    
    for ($t = 0; $t < 6; $t++) {
        $n_unsc = 0;
        for ($tt = 0; $tt < 6; $tt++) {
            if ($taulell[$tt][$t] == 1) {
                $n_unsc++;
                if ($n_unsc == 4) {
                    $sql2 = "UPDATE partides SET guanyador = 1 WHERE id_partida = $partida";
                    $result = mysqli_query($con, $sql2) or exit(mysqli_error($con));
                    return false;
                }
            } else
                $n_unsc = 0;
        }
    }
    
    for ($t = 0; $t < 7; $t++) {
        $n_dos = 0;
        for ($tt = 0; $tt < 6; $tt++) {
            if ($taulell[$tt][$t] == 2) {
                $n_dos++;
                if ($n_dos == 4){
                    $sql2 = "UPDATE partides SET guanyador = 2 WHERE id_partida = $partida";
                    $result = mysqli_query($con, $sql2) or exit(mysqli_error($con));
                    return false;
                }
            } else
                $n_dos = 0;
        }
    }
    

    for ($t = -3; $t < 3; $t++) { 
        $n_uns = 0;
        for ($tt = 0; $tt < 7; $tt++) {       
            if (($t + $tt) >= 0 && ($t + $tt) < 6 && $tt >= 0 && $tt < 7) {
                if (($taulell[$t + $tt][$tt]) == 1) { 
                    $n_uns++;
                    if ($n_uns >= 4) {
                        $sql2 = "UPDATE partides SET guanyador = 1 WHERE id_partida = $partida";
                    $result = mysqli_query($con, $sql2) or exit(mysqli_error($con));
                    return false;
                    }
                } else {
                    $n_uns = 0;
                }
            }
        }
    }
    
    for ($t = -3; $t < 3; $t++) {
        $n_dos = 0;
        for ($tt = 0; $tt < 7; $tt++) {


            if (($t + $tt) >= 0 && ($t + $tt) < 6 && $tt >= 0 && $tt < 7) {

                if ($taulell[$t + $tt][$tt] == 2) {
                    $n_dos++;
                    if ($n_dos >= 4) {
                        $sql2 = "UPDATE partides SET guanyador = 2 WHERE id_partida = $partida";
                    $result = mysqli_query($con, $sql2) or exit(mysqli_error($con));
                    return false;
                    }
                } else {
                    $n_dos = 0;
                }
            }
        }
    }


    for ($t = 3; $t < 10; $t++) {
        $n_uns = 0;
        for ($tt = 0; $tt < 7; $tt++) {
            if (($t - $tt) >= 0 && ($t - $tt) < 6 && $tt >= 0 && $tt < 7) {
                if ($taulell[$t - $tt][$tt] == 1) {
                    $n_uns++;
                    if ($n_uns == 4) {
                        $sql2 = "UPDATE partides SET guanyador = 1 WHERE id_partida = $partida";
                    $result = mysqli_query($con, $sql2) or exit(mysqli_error($con));
                    return false;
                    }
                }
            } else {
                $n_uns = 0;
            }
        }
    }
    
    for ($t = 3; $t < 7; $t++) {
        $n_doss = 0;
        for ($tt = 0; $tt < 7; $tt++) {
            if (($t - $tt) >= 0 && ($t - $tt) < 6 && $tt >= 0 && $tt < 7) {
                if ($taulell[$t - $tt][$tt] == 2) {
                    $n_doss++;
                    if ($n_doss == 4) {
                        $sql2 = "UPDATE partides SET guanyador = 2 WHERE id_partida = $partida";
                    $result = mysqli_query($con, $sql2) or exit(mysqli_error($con));
                    return false;
                    }
                }
            } else {
                $n_doss = 0;
            }
        }
    }

    return true;
}
