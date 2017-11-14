<?php
include_once '../lib/ControlAcceso.class.php';
include_once '../modelo/Workflow.class.php';

$WorkflowRoles = new WorkflowRoles();
ObjetoDatos::getInstancia()->autocommit(false);
ObjetoDatos::getInstancia()->begin_transaction();

/* Se guardan las variables del array POST y SESSION en variables locales: */
$idrol;
$idusuario = $_SESSION['idusuario'];
$limite = filter_input(INPUT_POST, limite);
$sinlimite = filter_input(INPUT_POST, sinlimite);
$libertad = filter_input(INPUT_POST, libertad);

// Con esta variable se monitorea el éxito o el fallo de la operación.
$transaccionRealizada;

foreach ($WorkflowRoles->getRoles() as $Rol) {
    if ($Rol->getNombre() === "Gestor de formularios") {
        $idrol = $Rol->getIdRol();
    }
}

/* Primero se actualiza el rol del usuario en el sistema. */
$transaccionRealizada = ObjetoDatos::getInstancia()->ejecutarQuery("" .
        "UPDATE  " . Constantes::BD_USERS . ".USUARIO_ROL " .
        "SET `idrol` = " . $idrol . " " .
        "WHERE `idusuario` = " . $idusuario);

/* Como la columna libertad se representa con un tipo de dato BIT en la base de
  datos, es necesario hacer esta conversión. */
if ($libertad === "Sí") {
    $libertad = 1; // 1 = Verdadero.
} else {
    $libertad = 0; // 0 = Falso.
}

/* Después se agrega el nuevo gestor a la tabla de gestores con sus respectivos
  detalles */
if (isset($sinlimite) || !isset($limite) || trim($limite) === "") {
    $transaccionRealizada = ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "INSERT INTO " . Constantes::BD_USERS . ".GESTOR_FORMULARIOS (`idusuario`, `libertad`) " .
            "VALUES (" . $idusuario . ", " . $libertad . ")");
} else {
    $transaccionRealizada = ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "INSERT INTO " . Constantes::BD_USERS . ".GESTOR_FORMULARIOS " .
            "VALUES (" . $idusuario . ", " . $limite . ", " . $libertad . ")");
}

ObjetoDatos::getInstancia()->commit();

if ($transaccionRealizada === 0) {
    ?>
    Se produjo un error al ejecutar la operación:<br/>
    <strong>Error:</strong> <?= mysqli_error(ObjetoDatos::getInstancia()) ?><br/>
    <strong>Código:</strong><?= mysqli_errno(ObjetoDatos::getInstancia()) ?>
<?php } else { ?>
    <script type="text/javascript">
        window.open("alta_gestor_exito.php", "_self");
    </script>
    <?php
}
?>