<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

require_once dirname(dirname(__FILE__)) . "/colibri_creador/Campos.class.php";
require_once dirname(dirname(__FILE__)) . "/colibri_creador/Formulario.class.php";


ObjetoDatos::getInstancia()->autocommit(false);
ObjetoDatos::getInstancia()->begin_transaction();

$Formulario = new Formulario(getdate()['year'] . "-" . getdate()['mon'] . "-" . getdate()['mday']);

$indice = 1;
$campoActual = json_decode(stripslashes(filter_input(INPUT_POST, "campoId" . $indice)));

while (true) {
    if (!isset($campoActual)) {
        break;
    }

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
$Formulario->setFechaInicio(filter_input(INPUT_POST, "fechaInicio"));
$Formulario->setFechaFin(filter_input(INPUT_POST, "fechaFin"));
$Formulario->setTitulo(filter_input(INPUT_POST, "titulo"));

/* Se obtienen los roles de la variable rolesDestinatarios de POST */
foreach ($rolesDestino as $idrol) {
    $Formulario->agregarDestinatario($idrol);
}

if (empty($Formulario->getFechaInicio()) && empty($Formulario->getFechaFin())) {
    ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "INSERT INTO FORMULARIO(`titulo`, `descripcion`, `emailreceptor`, `cantidadrespuestas`, `estahabilitado`, `fechainicio`, `fechafin`, `fechacreacion`, `creador`) " .
            "VALUES ('{$Formulario->getTitulo()}', '{$Formulario->getDescripcion()}', '{$Formulario->getEmailReceptor()}', {$Formulario->getCantidadRespuestas()}, 0, NULL, NULL, STR_TO_DATE('{$Formulario->getFechaCreacion()}', '%Y-%m-%d'), 2)");
} else if (empty($Formulario->getFechaFin())) {
    ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "INSERT INTO FORMULARIO(`titulo`, `descripcion`, `emailreceptor`, `cantidadrespuestas`, `estahabilitado`, `fechainicio`, `fechafin`, `fechacreacion`, `creador`) " .
            "VALUES ('{$Formulario->getTitulo()}', '{$Formulario->getDescripcion()}', '{$Formulario->getEmailReceptor()}', {$Formulario->getCantidadRespuestas()}, 0, STR_TO_DATE('{$Formulario->getFechaInicio()}', '%Y-%m-%d'), NULL, STR_TO_DATE('{$Formulario->getFechaCreacion()}', '%Y-%m-%d'), 2)");
} else if (empty($Formulario->getFechaInicio())) {
    ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "INSERT INTO FORMULARIO(`titulo`, `descripcion`, `emailreceptor`, `cantidadrespuestas`, `estahabilitado`, `fechainicio`, `fechafin`, `fechacreacion`, `creador`) " .
            "VALUES ('{$Formulario->getTitulo()}', '{$Formulario->getDescripcion()}', '{$Formulario->getEmailReceptor()}', {$Formulario->getCantidadRespuestas()}, 0, NULL, STR_TO_DATE('{$Formulario->getFechaFin()}', '%Y-%m-%d'), STR_TO_DATE('{$Formulario->getFechaCreacion()}', '%Y-%m-%d'), 2)");
} else {
    ObjetoDatos::getInstancia()->ejecutarQuery("" .
            "INSERT INTO FORMULARIO(`titulo`, `descripcion`, `emailreceptor`, `cantidadrespuestas`, `estahabilitado`, `fechainicio`, `fechafin`, `fechacreacion`, `creador`) " .
            "VALUES ('{$Formulario->getTitulo()}', '{$Formulario->getDescripcion()}', '{$Formulario->getEmailReceptor()}', {$Formulario->getCantidadRespuestas()}, 0, STR_TO_DATE('{$Formulario->getFechaInicio()}', '%Y-%m-%d'), STR_TO_DATE('{$Formulario->getFechaFin()}', '%Y-%m-%d'), STR_TO_DATE('{$Formulario->getFechaCreacion()}', '%Y-%m-%d'), 2)");
}
?>

<p><strong>Error MySQL. Inserción del formulario:</strong></p>
<p>Si no sale ninguno (cód. 0) significa que todo salió bien.</p>
<p>Código: <?= mysqli_errno(ObjetoDatos::getInstancia()) ?>, Mensaje: <?= mysqli_error(ObjetoDatos::getInstancia()) ?></p>

<?php
ObjetoDatos::getInstancia()->commit();

/* Ahora, vamos con la inserción de campos a la base de datos */

ObjetoDatos::getInstancia()->autocommit(true); // Se activa el autocommit porque de lo contrario habían problemas...

$query = "SELECT `idformulario` " .
        "FROM FORMULARIO " .
        "WHERE `titulo` = '{$Formulario->getTitulo()}'";

$resultadoConsulta = ObjetoDatos::getInstancia()->ejecutarQuery($query);
$idformulario = $resultadoConsulta->fetch_assoc()['idformulario'];
?>

<br/>
<hr>
<strong>Consulta actual para la obtención del ID:</strong> <?= $query ?>
<hr>
<br/>

<br/><p><strong>Error MySQL. Obtención del ID del formulario:</strong></p>
<p>Si no sale ninguno (cód. 0) significa que todo salió bien. La ID es <?= $idformulario ?>.</p>
<p>Código: <?= mysqli_errno(ObjetoDatos::getInstancia()) ?>, Mensaje: <?= mysqli_error(ObjetoDatos::getInstancia()) ?></p>

<?php
/* ?>
  <strong>Campos del formulario:</strong> <?= count($Formulario->getCampos()); ?><br/>
  <?php */

ObjetoDatos::getInstancia()->autocommit(false);

foreach ($Formulario->getCampos() as $CampoActual) {
    ObjetoDatos::getInstancia()->begin_transaction();

    $query = "INSERT INTO CAMPO(`idformulario`, `titulo`, `descripcion`, `esobligatorio`, `posicion`) " .
            "VALUES ({$idformulario}, '{$CampoActual->getTitulo()}', '{$CampoActual->getDescripcion()}', {$CampoActual->esObligatorio()}, {$CampoActual->getPosicion()})";
    ?>

    <br/>
    <hr>
    <strong>Consulta actual:</strong> <?= $query ?>
    <hr>

    <?php
    ObjetoDatos::getInstancia()->ejecutarQuery($query);
    ?>

    <br/><p><strong>Error MySQL. Inserción de campos:</strong></p>
    <p>Si no sale ninguno (cód. 0) significa que todo salió bien.</p>
    <p>Código: <?= mysqli_errno(ObjetoDatos::getInstancia()) ?>, Mensaje: <?= mysqli_error(ObjetoDatos::getInstancia()) ?></p>

    <?php
    if ($CampoActual instanceof CampoTexto) {
        $query = "INSERT INTO CAMPO_TEXTO " .
                "VALUES ({$idformulario}, '{$CampoActual->getPista()}')";
        ObjetoDatos::getInstancia()->ejecutarQuery($query);
        ?>

        <br/>
        <hr>
        <strong>Consulta actual (campo de texto):</strong> <?= $query ?>
        <hr>

        <?php
    } else if ($CampoActual instanceof AreaTexto) {
        // Hacer algo...
    } else if ($CampoActual instanceof ListaDesplegable) {
        // Hacer algo...
    }

    ObjetoDatos::getInstancia()->commit();
}
?>

