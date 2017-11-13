<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once dirname(dirname(__FILE__)) . "/colibri_creador/Campos.class.php";
require_once dirname(dirname(__FILE__)) . "/colibri_creador/Formulario.class.php";


ObjetoDatos::getInstancia()->autocommit(false);
ObjetoDatos::getInstancia()->begin_transaction();

$Formulario = new Formulario(getdate()['year'] . "/" . getdate()['mon'] . "/" . getdate()['mday']);

$indice = 1;
$campoActual = json_decode(stripslashes(filter_input(INPUT_POST, "campoId" . $indice)));

while (isset($campoActual)) {
    $tipo = $campoActual->tipoCampo;

    if ($tipo === "CampoTexto") {
        $CampoTexto = new CampoTexto();

        $CampoTexto->setDescripcion($campoActual->descripcion);
        $CampoTexto->setEsObligatorio($campoActual->obligatorio);
        $CampoTexto->setPista($campoActual->pista);
        $CampoTexto->setPosicion($indice);
        $CampoTexto->setTitulo($campoActual->titulo);

        $Formulario->agregarCampo($CampoTexto);
    } else if ($tipo === "AreaTexto") {
        $AreaTexto = new AreaTexto();

        $AreaTexto->setDescripcion($campoActual->descripcion);
        $AreaTexto->setEsObligatorio($campoActual->obligatorio);
        $AreaTexto->setLimiteCaracteres($campoActual->limiteCaracteres);
        $AreaTexto->setPosicion($indice);
        $AreaTexto->setTitulo($campoActual->titulo);
    } else if ($tipo === "ListaDesplegable") {
        $ListaDesplegable = new ListaDesplegable();

        $ListaDesplegable->setDescripcion($campoActual->descripcion);
        $ListaDesplegable->setEsObligatorio($campoActual->obligatorio);
        $ListaDesplegable->setPosicion($indice);
        $ListaDesplegable->setTitulo($campoActual->titulo);

        foreach ($campoActual->opciones as $opcion) { // VERIFICAR QUE FUNCIONA BIEN.
            $ListaDesplegable->agregarOpcion($opcion);
        }

        $Formulario->agregarCampo($ListaDesplegable);
    }

    $indice = $indice + 1;
    $campoActual = json_decode(stripslashes(filter_input(INPUT_POST, "campoId" . $indice)));
}

$rolesDestino = (array) filter_input(INPUT_POST, "rolesDestino");

$Formulario->setDescripcion(filter_input(INPUT_POST, "descripcion"));
$Formulario->setEmailReceptor(filter_input(INPUT_POST, "destinatario"));
$Formulario->setFechaInicio(filter_input(INPUT_POST, "fechaApertura"));
$Formulario->setFechaFin(filter_input(INPUT_POST, "fechaCierre"));
$Formulario->setTitulo(filter_input(INPUT_POST, "titulo"));

/* Se obtienen los roles de la variable rolesDestinatarios de POST */
foreach ($rolesDestino as $idrol) {
    $Formulario->agregarDestinatario($idrol);
}

if (!isset($_POST["fechaApertura"]) && !isset($_POST["fechaCierre"])) {
    ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "INSERT INTO FORMULARIO(`titulo`, `descripcion`, `emailreceptor`, `cantidadrespuestas`, `estahabilitado`, `fechainicio`, `fechafin`, `fechacreacion`, `creador`) " .
            "VALUES ('{$Formulario->getTitulo()}', '{$Formulario->getDescripcion()}', '{$Formulario->getEmailReceptor()}', {$Formulario->getCantidadRespuestas()}, 0, NULL, NULL, STR_TO_DATE('{$Formulario->getFechaCreacion()}', '%Y/%m/%d'), 3)");
} else if (!isset($_POST["fechaCierre"])) {
    ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "INSERT INTO FORMULARIO(`titulo`, `descripcion`, `emailreceptor`, `cantidadrespuestas`, `estahabilitado`, `fechainicio`, `fechafin`, `fechacreacion`, `creador`) " .
            "VALUES ('{$Formulario->getTitulo()}', '{$Formulario->getDescripcion()}', '{$Formulario->getEmailReceptor()}', {$Formulario->getCantidadRespuestas()}, 0, STR_TO_DATE('{$Formulario->getFechaInicio()}', '%Y/%m/%d'), NULL, STR_TO_DATE('{$Formulario->getFechaCreacion()}', '%Y/%m/%d'), 3)");
} else if (!isset($_POST["fechaApertura"])) {
    ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "INSERT INTO FORMULARIO(`titulo`, `descripcion`, `emailreceptor`, `cantidadrespuestas`, `estahabilitado`, `fechainicio`, `fechafin`, `fechacreacion`, `creador`) " .
            "VALUES ('{$Formulario->getTitulo()}', '{$Formulario->getDescripcion()}', '{$Formulario->getEmailReceptor()}', {$Formulario->getCantidadRespuestas()}, 0, NULL, STR_TO_DATE('{$Formulario->getFechaFin()}', '%Y/%m/%d'), STR_TO_DATE('{$Formulario->getFechaCreacion()}', '%Y/%m/%d'), 3)");
} else {
    ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "INSERT INTO FORMULARIO(`titulo`, `descripcion`, `emailreceptor`, `cantidadrespuestas`, `estahabilitado`, `fechainicio`, `fechafin`, `fechacreacion`, `creador`) " .
            "VALUES ('{$Formulario->getTitulo()}', '{$Formulario->getDescripcion()}', '{$Formulario->getEmailReceptor()}', {$Formulario->getCantidadRespuestas()}, 0, STR_TO_DATE('{$Formulario->getFechaInicio()}', '%Y/%m/%d'), STR_TO_DATE('{$Formulario->getFechaFin()}', '%Y/%m/%d'), STR_TO_DATE('{$Formulario->getFechaCreacion()}', '%Y/%m/%d'), 3)");
}
?>

<p><strong>Error MySQL. Inserción del formulario:</strong></p>
<p>Si no sale ninguno significa que todo salió bien.</p>
<p>Código: <?= mysqli_errno(ObjetoDatos::getInstancia()) ?>, Mensaje: <?= mysqli_error(ObjetoDatos::getInstancia()) ?></p>

<?php
ObjetoDatos::getInstancia()->commit();

/* Ahora, vamos con la inserción de campos a la base de datos */

$resultadoConsulta = ObjetoDatos::getInstancia()->ejecutarQuery("" .
        "SELECT `idformulario` " .
        "FROM FORMULARIO " .
        "WHERE `titulo` = '{$Formulario->getTitulo()}'");
?>
<br/><p><strong>Error MySQL. Obtención del ID del formulario:</strong></p>
<p>Si no sale ninguno significa que todo salió bien.</p>
<p>Código: <?= mysqli_errno(ObjetoDatos::getInstancia()) ?>, Mensaje: <?= mysqli_error(ObjetoDatos::getInstancia()) ?></p>
<?php
$consulta = $resultadoConsulta->fetch_assoc();

foreach ($Formulario->getCampos() as $CampoActual) {
    ObjetoDatos::getInstancia()->ejecutarQuery("INSERT INTO CAMPO(`idformulario`, `titulo`, `descripcion`, `esobligatorio`, `posicion`) " .
            "VALUES ({$consulta['idformulario']}, '{$CampoActual->getTitulo()}', '{$CampoActual->getDescripcion()}', {$CampoActual->esObligatorio()}, {$CampoActual->getPosicion()})");
    ?>
    <br/><p><strong>Error MySQL. Inserción de campos:</strong></p>
    <p>Si no sale ninguno significa que todo salió bien.</p>
    <p>Código: <?= mysqli_errno(ObjetoDatos::getInstancia()) ?>, Mensaje: <?= mysqli_error(ObjetoDatos::getInstancia()) ?></p>
    <?php
    ObjetoDatos::getInstancia()->commit();
    //if ($tipocampo == "Some text")...
}