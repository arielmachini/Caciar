<?php
// error_reporting(E_ALL);
// ini_set("display_errors", 1);

include_once realpath("./Formulario.class.php");
include_once realpath("./Campos.class.php");

$FormularioNuevo = new Formulario();
$WorkflowRoles = new WorkflowRoles();

date_default_timezone_set("America/Argentina/Rio_Gallegos");

?>
<html>
    <head>
        <title><?= Constantes::NOMBRE_SISTEMA ?>: Crear un nuevo formulario</title>
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet">
        <link href="../gui/responsivo.css" type="text/css" rel="stylesheet">
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
        <script type="text/javascript">
            $('#agregar').onclick(function () {
                alert("TEST!!!");
                var filas = $("#vistaPrevia tr:first td").length;
                var filaNueva = "<tr>";

                for (var i = 0; i < filas; i++) {
                    filaNueva += "<td>Hola</td>";
                }

                filaNueva += "</tr>";

                $("#vistaPrevia").append(filaNueva);
            });
        </script>
    </head>
    <body>
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>Crear un nuevo formulario</h3>
                    <p>A través de esta página usted podrá crear un nuevo formulario para el sistema Colibrí.</p>
                    <form action="procesar.php" method="post" onchange="desactivarBoton()">
                        <strong style='display: inline-block; padding-bottom: 5px'>¿Dónde enviamos las respuestas?:</strong><br/>
                        <span style="display: inline-block; font-size: 14px; padding-bottom: 10px">Ingrese una dirección de correo electrónico válida a la que desee recibir las respuestas al formulario que está creando.</span><br/>
                        <input autocomplete="on" name="destinatario" placeholder="Ingrese una dirección de correo-e válida" required style="width: 60%" type="email"/><br/><br/>
                        <strong style='display: inline-block; padding-bottom: 5px'>Título del formulario:</strong><br/>
                        <span style="display: inline-block; font-size: 14px; padding-bottom: 10px">Ingrese un título corto pero descriptivo para el formulario.</span><br/>
                        <input autocomplete="off" autofocus="true" name="titulo" required style="width: 60%" type="text"/><br/><br/>
                        <strong style='display: inline-block; padding-bottom: 5px'>Descripción del formulario:</strong><br/>
                        <span style="display: inline-block; font-size: 14px; padding-bottom: 10px">La descripción es lo que se muestra bajo el título del formulario. No es obligatorio que escriba una, pero si lo hace procure ser breve y evitar los tecnicismos.</span><br/>
                        <textarea maxlength="500" placeholder="Se admiten hasta 500 caracteres." style="height: 100px; width: 60%"></textarea><br/><br/>
                        <strong style='display: inline-block; padding-bottom: 5px'>Fechas límite:</strong><br/>
                        <span style="display: inline-block; font-size: 14px; padding-bottom: 10px">Si lo desea puede establecer una fecha a partir de la cual el formulario estará disponible para ser rellenado, así como también puede establecer una fecha en la que se dejen de aceptar respuestas. Esto es opcional, por lo que también puede elegir que el formulario esté disponible hasta que se deshabilite manualmente.</span><br/>
                        <input readonly min="<?= date("Y/m/d", time()) ?>" type="date" />
                        <table border="0" cellpadding="0" cellspacing="0" style="border-style: none; border-color: transparent; background-color: transparent; width: 100%">
                            <tbody>
                                <tr>
                                    <td style="vertical-align:top; width: 70%">
                                        <table id="vistaPrevia" style="width: 90%">
                                            <tr>
                                                <th>Pos.</th>
                                                <th>Título</th>
                                                <th>Obligatorio</th>
                                                <th>Acciones</th>
                                            </tr>
                                        </table>
                                    </td>
                                    <td id="camposDisponibles" style="width: 30%">
                                        <h4>Campos disponibles</h4><br/><br/>
                                        <input disabled type="text" value="Campo de texto"/> <img src="../imagenes/creador_agregar_campo.png" title="Haga clic aquí para agregar un nuevo campo de texto"/><br/><br/>
                                        <textarea disabled>Área de texto</textarea><br/>
                                        <input id="agregar" type="button" value="Agregar"><br/><br/>
                                        <select disabled>
                                            <option>Opciones</option>
                                        </select>
                                        <input type="button" value="Agregar"><br/>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <input onclick="guardarFormulario('$FormularioNuevo')" type="button" value="Guardar cambios"> <input disabled type="submit" value="enviar">
                    </form>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>
