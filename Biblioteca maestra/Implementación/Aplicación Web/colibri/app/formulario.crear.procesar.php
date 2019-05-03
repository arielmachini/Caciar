<!DOCTYPE html>

<?php
header('Content-Type: text/html; charset=utf-8');

include_once '../lib/ControlAcceso.Class.php';
ControlAcceso::requierePermiso(PermisosSistema::PERMISO_CREAR_FORMULARIOS);

/**
 * Se realiza esta comprobación para evitar que el usuario cree formularios
 * vacíos accediendo directamente a esta página.
 */
if (empty($_POST)) {
    ControlAcceso::redireccionar("formulario.crear.php");
}

require_once '../modelo/BDConexion.Class.php';
require_once '../modelo/Campos.Class.php';
require_once '../modelo/Formulario.Class.php';

function sanitizar($valor_) {
    $valor_ = trim($valor_);
    $valor_ = stripslashes($valor_);
    $valor_ = htmlspecialchars($valor_);

    return $valor_;
}

/**
 * Las siguientes dos consultas son necesarias para que los valores con
 * caracteres "foráneos al inglés" (como "á" o "ñ") se almacenen correctamente
 * en la base de datos.
 */
BDConexion::getInstancia()->query("" .
        "SET CHARACTER SET 'utf8'");

BDConexion::getInstancia()->query("" .
        "SET NAMES 'utf8'");

BDConexion::getInstancia()->autocommit(false);
BDConexion::getInstancia()->begin_transaction();

$formulario = new Formulario(date("Y") . "-" . date("m") . "-" . date("d"));
$i = 1;

$campo = json_decode(stripslashes(filter_input(INPUT_POST, "campoID" . $i)));

while (true) {
    if (!isset($campo)) {
        break;
    }

    $tipoCampo = $campo->tipoCampo;

    if ($tipoCampo === "AreaTexto") {
        $areaTexto = new AreaTexto();

        $areaTexto->setDescripcion(sanitizar($campo->descripcion));
        $areaTexto->setEsObligatorio($campo->obligatorio);
        $areaTexto->setLimiteCaracteres($campo->limite);
        $areaTexto->setPosicion($i);
        $areaTexto->setTitulo(sanitizar($campo->titulo));

        $formulario->agregarCampo($areaTexto);
    } else if ($tipoCampo === "CampoEmail") {
        $campoEmail = new CampoTexto();

        $campoEmail->setDescripcion(sanitizar($campo->descripcion));
        $campoEmail->setEsObligatorio($campo->obligatorio);
        $campoEmail->setPista($campo->pista);
        $campoEmail->setPosicion($i);
        $campoEmail->setSubtipo(CampoTexto::$CAMPO_EMAIL);
        $campoEmail->setTitulo(sanitizar($campo->titulo));

        $formulario->agregarCampo($campoEmail);
    } else if ($tipoCampo === "CampoNumerico") {
        $campoNumerico = new CampoTexto();

        $campoNumerico->setDescripcion(sanitizar($campo->descripcion));
        $campoNumerico->setEsObligatorio($campo->obligatorio);
        $campoNumerico->setPista($campo->pista);
        $campoNumerico->setPosicion($i);
        $campoNumerico->setSubtipo(CampoTexto::$CAMPO_NUMERICO);
        $campoNumerico->setTitulo(sanitizar($campo->titulo));

        $formulario->agregarCampo($campoNumerico);
    } else if ($tipoCampo === "CampoTexto") {
        $campoTexto = new CampoTexto();

        $campoTexto->setDescripcion(sanitizar($campo->descripcion));
        $campoTexto->setEsObligatorio($campo->obligatorio);
        $campoTexto->setPista($campo->pista);
        $campoTexto->setPosicion($i);
        $campoTexto->setTitulo(sanitizar($campo->titulo));

        $formulario->agregarCampo($campoTexto);
    } else if ($tipoCampo === "Fecha") {
        $campoFecha = new Fecha();

        $campoFecha->setDescripcion(sanitizar($campo->descripcion));
        $campoFecha->setEsObligatorio($campo->obligatorio);
        $campoFecha->setPosicion($i);
        $campoFecha->setTitulo(sanitizar($campo->titulo));

        $formulario->agregarCampo($campoFecha);
    } else if ($tipoCampo === "ListaDesplegable") {
        $listaDesplegable = new ListaDesplegable();
        $elementosLista = $campo->opciones;

        $listaDesplegable->setDescripcion(sanitizar($campo->descripcion));

        foreach ($elementosLista as $elemento) {
            $listaDesplegable->agregarElemento($elemento);
        }

        $listaDesplegable->setEsObligatorio($campo->obligatorio);
        $listaDesplegable->setPosicion($i);
        $listaDesplegable->setTitulo(sanitizar($campo->titulo));

        $formulario->agregarCampo($listaDesplegable);
    } else if ($tipoCampo === "ListaCheckbox") {
        $listaCheckbox = new ListaCheckbox();
        $elementosLista = $campo->opciones;

        $listaCheckbox->setDescripcion(sanitizar($campo->descripcion));

        foreach ($elementosLista as $elemento) {
            $listaCheckbox->agregarElemento($elemento);
        }

        $listaCheckbox->setEsObligatorio($campo->obligatorio);
        $listaCheckbox->setPosicion($i);
        $listaCheckbox->setTitulo(sanitizar($campo->titulo));

        $formulario->agregarCampo($listaCheckbox);
    } else { // Si $tipoCampo no coincide con ninguno de los tipos anteriores, entonces (por descarte) es "ListaRadio".
        $listaRadio = new ListaRadio();
        $elementosLista = $campo->opciones;

        $listaRadio->setDescripcion(sanitizar($campo->descripcion));

        foreach ($elementosLista as $elemento) {
            $listaRadio->agregarElemento($elemento);
        }

        $listaRadio->setEsObligatorio($campo->obligatorio);
        $listaRadio->setPosicion($i);
        $listaRadio->setTitulo(sanitizar($campo->titulo));

        $formulario->agregarCampo($listaRadio);
    }

    $i += 1;
    $campo = json_decode(stripslashes(filter_input(INPUT_POST, "campoID" . $i)));
}

$formulario->setDescripcion(sanitizar(filter_input(INPUT_POST, "descripcionFormulario")));
$formulario->setEmailReceptor(sanitizar(filter_input(INPUT_POST, "destinatarioFormulario")));
$formulario->setFechaApertura(sanitizar(filter_input(INPUT_POST, "fechaAperturaFormulario")));
$formulario->setFechaCierre(sanitizar(filter_input(INPUT_POST, "fechaCierreFormulario")));

$formulario->setTitulo(sanitizar(filter_input(INPUT_POST, "tituloFormulario")));

if (empty($formulario->getFechaApertura()) && empty($formulario->getFechaCierre())) {
    $consulta = BDConexion::getInstancia()->query("" .
            "INSERT INTO " . BDCatalogoTablas::BD_TABLA_FORMULARIO . "(`idCreador`, `emailReceptor`, `titulo`, `descripcion`, `fechaCreacion`, `fechaApertura`, `fechaCierre`, `estaHabilitado`, `cantidadRespuestas`) " .
            "VALUES ({$_SESSION['usuario']->id}, '{$formulario->getEmailReceptor()}', '{$formulario->getTitulo()}', '{$formulario->getDescripcion()}', STR_TO_DATE('{$formulario->getFechaCreacion()}', '%Y-%m-%d'), NULL, NULL, 0, 0)");
} else if (empty($formulario->getFechaCierre())) {
    $consulta = BDConexion::getInstancia()->query("" .
            "INSERT INTO " . BDCatalogoTablas::BD_TABLA_FORMULARIO . "(`idCreador`, `emailReceptor`, `titulo`, `descripcion`, `fechaCreacion`, `fechaApertura`, `fechaCierre`, `estaHabilitado`, `cantidadRespuestas`) " .
            "VALUES ({$_SESSION['usuario']->id}, '{$formulario->getEmailReceptor()}', '{$formulario->getTitulo()}', '{$formulario->getDescripcion()}', STR_TO_DATE('{$formulario->getFechaCreacion()}', '%Y-%m-%d'), STR_TO_DATE('{$formulario->getFechaApertura()}', '%Y-%m-%d'), NULL, 0, 0)");
} else if (empty($formulario->getFechaApertura())) {
    $consulta = BDConexion::getInstancia()->query("" .
            "INSERT INTO " . BDCatalogoTablas::BD_TABLA_FORMULARIO . "(`idCreador`, `emailReceptor`, `titulo`, `descripcion`, `fechaCreacion`, `fechaApertura`, `fechaCierre`, `estaHabilitado`, `cantidadRespuestas`) " .
            "VALUES ({$_SESSION['usuario']->id}, '{$formulario->getEmailReceptor()}', '{$formulario->getTitulo()}', '{$formulario->getDescripcion()}', STR_TO_DATE('{$formulario->getFechaCreacion()}', '%Y-%m-%d'), NULL, STR_TO_DATE('{$formulario->getFechaCierre()}', '%Y-%m-%d'), 0, 0)");
} else {
    $consulta = BDConexion::getInstancia()->query("" .
            "INSERT INTO " . BDCatalogoTablas::BD_TABLA_FORMULARIO . "(`idCreador`, `emailReceptor`, `titulo`, `descripcion`, `fechaCreacion`, `fechaApertura`, `fechaCierre`, `estaHabilitado`, `cantidadRespuestas`) " .
            "VALUES ({$_SESSION['usuario']->id}, '{$formulario->getEmailReceptor()}', '{$formulario->getTitulo()}', '{$formulario->getDescripcion()}', STR_TO_DATE('{$formulario->getFechaCreacion()}', '%Y-%m-%d'), STR_TO_DATE('{$formulario->getFechaApertura()}', '%Y-%m-%d'), STR_TO_DATE('{$formulario->getFechaCierre()}', '%Y-%m-%d'), 0, 0)");
}

if ($consulta) { // Si la inserción del formulario se completó exitosamente, se continúa con el procesamiento.
    $idFormulario = BDConexion::getInstancia()->query("" .
                    "SELECT `idFormulario` " .
                    "FROM " . BDCatalogoTablas::BD_TABLA_FORMULARIO . " " .
                    "WHERE `titulo` = '{$formulario->getTitulo()}'")->fetch_assoc()['idFormulario'];

    /* Se guardan los roles de destino para el formulario. */
    $rolesDestinoFormulario = $_POST['rolesDestinoFormulario'];

    foreach ($rolesDestinoFormulario as $idRol) {
        $consulta = BDConexion::getInstancia()->query("" .
                "INSERT INTO " . BDCatalogoTablas::BD_TABLA_FORMULARIO_ROL . " " .
                "VALUES ({$idFormulario}, {$idRol})");
    }

    /* A partir de este punto, se procede con la inserción de los campos del formulario. */
    foreach ($formulario->getCampos() as $campo) {
        $consulta = BDConexion::getInstancia()->query("" .
                "INSERT INTO " . BDCatalogoTablas::BD_TABLA_CAMPO . "(`idFormulario`, `titulo`, `descripcion`, `esObligatorio`, `posicion`) " .
                "VALUES ({$idFormulario}, '{$campo->getTitulo()}', '{$campo->getDescripcion()}', {$campo->esObligatorio()}, {$campo->getPosicion()})");

        if (!$consulta) {
            /**
             * Si hay un problema con la inserción del campo, se rompe con el
             * bucle para ir al rollback.
             */
            break;
        }

        $idCampo = BDConexion::getInstancia()->query("" .
                        "SELECT `idCampo` " .
                        "FROM " . BDCatalogoTablas::BD_TABLA_CAMPO . " " .
                        "WHERE `idFormulario` = {$idFormulario} AND `titulo` = '{$campo->getTitulo()}'")->fetch_assoc()['idCampo'];

        if ($campo instanceof AreaTexto) {
            $consulta = BDConexion::getInstancia()->query("" .
                    "INSERT INTO " . BDCatalogoTablas::BD_TABLA_AREA_TEXTO . " " .
                    "VALUES ({$idCampo}, {$campo->getLimiteCaracteres()})");
        } else if ($campo instanceof CampoTexto) {
            $consulta = BDConexion::getInstancia()->query("" .
                    "INSERT INTO " . BDCatalogoTablas::BD_TABLA_CAMPO_TEXTO . " " .
                    "VALUES ({$idCampo}, '{$campo->getPista()}', '{$campo->getSubtipo()}')");
        } else if ($campo instanceof Fecha) {
            $consulta = BDConexion::getInstancia()->query("" .
                    "INSERT INTO " . BDCatalogoTablas::BD_TABLA_FECHA . " " .
                    "VALUES ({$idCampo})");
        } else if ($campo instanceof ListaDesplegable) {
            $consulta = BDConexion::getInstancia()->query("" .
                    "INSERT INTO " . BDCatalogoTablas::BD_TABLA_LISTA_DESPLEGABLE . " " .
                    "VALUES ({$idCampo})");

            foreach ($campo->getElementos() as $elemento) {
                $elemento = sanitizar($elemento);

                $consulta = BDConexion::getInstancia()->query("" .
                        "INSERT INTO " . BDCatalogoTablas::BD_TABLA_OPCION . " " .
                        "VALUES ({$idCampo}, '{$elemento}')");
            }
        } else if ($campo instanceof ListaCheckbox) {
            $consulta = BDConexion::getInstancia()->query("" .
                    "INSERT INTO " . BDCatalogoTablas::BD_TABLA_LISTA_CHECKBOX . " " .
                    "VALUES ({$idCampo})");

            foreach ($campo->getElementos() as $elemento) {
                $elemento = sanitizar($elemento);

                $consulta = BDConexion::getInstancia()->query("" .
                        "INSERT INTO " . BDCatalogoTablas::BD_TABLA_CHECKBOX . " " .
                        "VALUES ({$idCampo}, '{$elemento}')");
            }
        } else { // Si $campo no coincide con ninguno de los tipos anteriores, entonces (por descarte) es "ListaRadio".
            $consulta = BDConexion::getInstancia()->query("" .
                    "INSERT INTO " . BDCatalogoTablas::BD_TABLA_LISTA_BOTON_RADIO . " " .
                    "VALUES ({$idCampo})");

            foreach ($campo->getElementos() as $elemento) {
                $elemento = sanitizar($elemento);

                $consulta = BDConexion::getInstancia()->query("" .
                        "INSERT INTO " . BDCatalogoTablas::BD_TABLA_BOTON_RADIO . " " .
                        "VALUES ({$idCampo}, '{$elemento}')");
            }
        }

        if (!$consulta) {
            /**
             * Si hay un problema con la inserción de la información del campo,
             * se rompe con el bucle para ir al rollback.
             */
            break;
        }
    }

    if ($consulta) {
        BDConexion::getInstancia()->commit();
    } else {
        BDConexion::getInstancia()->rollback();
    }
} else {
    BDConexion::getInstancia()->rollback();
}
?>

<html>
    <head>
        <meta charset="UTF-8">
        <link rel="stylesheet" href="../lib/bootstrap-4.1.1-dist/css/bootstrap.css" />
        <link rel="stylesheet" href="../lib/open-iconic-master/font/css/open-iconic-bootstrap.css" />

        <!-- Hojas de estilo requeridas por el sistema Colibrí -->
        <link rel="stylesheet" href="../gui/css/colibri.css" />

        <script type="text/javascript" src="../lib/JQuery/jquery-3.3.1.js"></script>
        <script type="text/javascript" src="../lib/bootstrap-4.1.1-dist/js/bootstrap.min.js"></script>

        <title><?php echo Constantes::NOMBRE_SISTEMA; ?> - Crear formulario</title>
    </head>
    <body>
        <?php include_once '../gui/navbar.php'; ?>

        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h3>Crear formulario</h3>
                </div>
                <div class="card-body">
                    <?php if ($consulta) { ?>
                        <div class="alert alert-success" role="alert">
                            Su formulario ha sido creado exitosamente. Recuerde que puede habilitarlo desde el gestor de formularios.
                        </div>
                    <?php } else { ?>
                        <div class="alert alert-danger" role="alert">
                            Se produjo un error al intentar procesar la solicitud.
                        </div>
                    <?php } ?>
                </div>
                <div class="card-footer">
                    <a href="formularios.php"><button type="button" class="btn btn-primary"><span class="oi oi-account-logout"></span> Finalizar</button></a> <a href="formulario.gestor.php"><button type="button" class="btn btn-primary"><span class="oi oi-dashboard"></span> Gestor de formularios</button></a>
                </div>
            </div>
        </div>

        <?php include_once '../gui/footer.php'; ?>
    </body>

</html>

