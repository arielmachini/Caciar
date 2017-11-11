<?php
error_reporting(E_ALL);
ini_set("display_errors", 1);

include_once '../lib/Constantes.class.php';
include_once '../modelo/Workflow.class.php';
include_once 'creador/modelo/Formulario.class.php';
include_once 'creador/modelo/Campos.class.php';

$FormularioNuevo = new Formulario();
$WorkflowRoles = new WorkflowRoles();

?>
<html>
    <head>
        <title><?= Constantes::NOMBRE_SISTEMA ?>: Crear un nuevo formulario</title>
        <link href="../gui/estilo.css" type="text/css" rel="stylesheet" />
        <link href="../gui/responsivo.css" type="text/css" rel="stylesheet" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"/>
    </head>
    <body>
        <?php include_once '../gui/GUImenu.php'; ?>
        <section id="main-content">
            <article>
                <div class="content">
                    <h3>Crear un nuevo formulario</h3>
                    <p>A través de esta página usted podrá crear un nuevo formulario para el sistema Colibrí.</p>
                    <form action="procesar.php" method="post" onchange="alternarBotones()">
                        <strong style='display: inline-block; padding-bottom: 5px'>¿Dónde enviamos las respuestas?:</strong><br/>
                        <span style="display: inline-block; font-size: 14px; padding-bottom: 10px">Ingrese una dirección de correo electrónico válida a la que desee recibir las respuestas al formulario que está creando.</span><br/>
                        <input autocomplete="true" name="destinatario" placeholder="Ingrese una dirección de correo-e válida" required="true" type="email"/><br/><br/>
                        <strong style='display: inline-block; padding-bottom: 5px'>Título del formulario:</strong><br/>
                        <span style="display: inline-block; font-size: 14px; padding-bottom: 10px">Ingrese un título corto pero descriptivo para el formulario.</span><br/>
                        <input autocomplete="false" autofocus="true" name="titulo" required="true" type="text"/><br/><br/>
                        <strong style='display: inline-block; padding-bottom: 5px'>Descripción del formulario:</strong><br/>
                        <span style="display: inline-block; font-size: 14px; padding-bottom: 10px">La descripción es lo que se muestra bajo el título del formulario. No es obligatorio que escriba una, pero si lo hace procure ser breve y evitar los tecnicismos.</span><br/>
                        <textarea maxlength="512"></textarea><br/><br/>
                        <table border="0" cellpadding="0" cellspacing="0" style="border-style: none; border-color: transparent; background-color: transparent; width: 100%">
                            <tr>
                                <td style="width: 70%">
                                    <table id="estadoFormulario">
                                        <tr>
                                            <th>Posición</th>
                                            <th>Título</th>
                                            <th>Obligatorio</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </table>
                                </td>
                                <td style="width: 30%">
                                    <h4>Campos disponibles</h4><br/><br/>
                                        <input disabled="true" type="text" value="Campo de texto"/> <img onclick="#" src="../imagenes/creador_agregar_campo.png" title="Haga clic aquí para agregar un nuevo campo de texto"/><br/><br/>
                                        <textarea disabled="true">Área de texto</textarea><br/>
                                        <input type="button" value="Agregar"><br/><br/>
                                        <select disabled="true">
                                            <option>Opciones</option>
                                        </select>
                                        <input type="button" value="Agregar"><br/>
                                </td>
                            </tr>
                        </table>

                        <input onclick="guardarFormulario('$FormularioNuevo')" type="button" value="Guardar cambios"> <input disabled="true" type="submit" value="enviar">
                    </form>
                </div>
            </article>
        </section>
        <?php include_once '../gui/GUIfooter.php'; ?>
    </body>
</html>
