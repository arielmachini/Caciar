<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';

ObjetoDatos::getInstancia()->autocommit(false);
ObjetoDatos::getInstancia()->begin_transaction();

/* Se recupera la variable idusuario del array POST en una variable local: */
$idusuario = $_SESSION['idusuario'];
$nuevoLimite = filter_input(INPUT_POST, limite);
$nuevoLibertad = filter_input(INPUT_POST, libertad);
$sinlimite = filter_input(INPUT_POST, sinlimite);

// Con esta variable se monitorea el éxito o el fallo de la operación.
$transaccionRealizada;

/* Como la columna libertad se representa con un tipo de dato BIT en la base de
  datos, es necesario hacer esta conversión. */
if ($nuevoLibertad === "Sí") {
    $nuevoLibertad = 1; // 1 = Verdadero.
} else {
    $nuevoLibertad = 0; // 0 = Falso.
}

/* Se modifican los detalles del gestor de formularios */
if (isset($sinlimite) || !isset($nuevoLimite) || trim($nuevoLimite) === "") {
    $transaccionRealizada = ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "UPDATE " . Constantes::BD_USERS . ".GESTOR_FORMULARIOS " .
            "SET `limite` = -1, `libertad` = " . $nuevoLibertad . " " .
            "WHERE `idusuario` = " . $idusuario);
} else {
    $transaccionRealizada = ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "UPDATE " . Constantes::BD_USERS . ".GESTOR_FORMULARIOS " .
            "SET `limite` = " . $nuevoLimite . ", `libertad` = " . $nuevoLibertad . " " .
            "WHERE `idusuario` = " . $idusuario);
}

ObjetoDatos::getInstancia()->commit();

if ($transaccionRealizada === 0) {
    ?>
    Se produjo un error al ejecutar la operación:<br/>
    <strong>Mensaje:</strong> <?= mysqli_error(ObjetoDatos::getInstancia()) ?><br/>
    <strong>Código:</strong> <?= mysqli_errno(ObjetoDatos::getInstancia()) ?>
<?php } else { ?>
    <script type="text/javascript">
        window.open("modificacion_gestor_exito.php", "_self");
    </script>
    <?php
}
?>