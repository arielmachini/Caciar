<link href="../lib/bootstrap-4.1.1-dist/css/uargflow_footer.css" type="text/css" rel="stylesheet" />

<div class="modal fade" id="dialogoCreditos" tabindex="-1" role="dialog" aria-labelledby="dialogoCreditosTitulo" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="dialogoCreditosTitulo">Créditos</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p><img src="../lib/img/colibri_creditos.png" width="100%"></p>
                <p>El sistema Colibrí es un desarrollo del grupo <i>Paire</i>, conformado por alumnos que cursaron la asignatura laboratorio de desarrollo de software durante el transcurso del año 2017.</p>
                <p><strong style="font-size: 18px">Integrantes del grupo Paire</strong></p>
                <ul>
                    <li>Ariel Machini</li>
                    <li>Cinthia Lima</li>
                </ul>
                <p><strong style="font-size: 18px">Diseño del icono del sistema</strong><br/>
                    El icono del sistema (el <i>colibrí</i>) fue diseñado por Ayelen Iturrioz.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<footer class="footer" style="border-top-color: #449c73;">
    <?php if (isset($_SESSION['usuario'])) { ?>
        <span class="oi oi-person" style="margin-right: 5px;"></span> <?= $_SESSION['usuario']->nombre; ?> (<a href="../app/salir.php" style="color: #ffb25b; text-decoration: none;">Cerrar sesión</a>) — 
    <?php } ?>

    <a class="enlace-pie" data-toggle="modal" data-target="#dialogoCreditos" href="#"><span class="oi oi-info" style="margin-right: 5px;"></span> Créditos</a> — 
    <a class="enlace-pie" href="https://www.uarg.unpa.edu.ar/" target="_blank"><span class="oi oi-globe" style="margin-right: 5px;"></span> Portal UNPA-UARG</a>
</footer>
