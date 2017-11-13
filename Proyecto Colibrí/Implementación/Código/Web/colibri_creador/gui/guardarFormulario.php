<?php

/* Este archivo tiene la responsabilidad de guardar el formulario en la variable
 * $_SESSION cada vez que se apreta el botón guardar */
$_SESSION['formulario'] = filter_input(INPUT_POST, "formulario");