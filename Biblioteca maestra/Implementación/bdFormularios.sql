DROP DATABASE IF EXISTS `bdFormularios`;

CREATE DATABASE `bdFormularios`
	CHARACTER SET utf8
	COLLATE utf8_general_ci;

USE `bdFormularios`;

CREATE TABLE `formulario` (
    `idFormulario` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `idCreador` INT(11) NOT NULL,
    `emailReceptor` VARCHAR(200) NOT NULL,
    `titulo` VARCHAR(75) NOT NULL,
    `descripcion` VARCHAR(400) DEFAULT NULL,
    `fechaCreacion` DATE NOT NULL,
    `fechaApertura` DATE DEFAULT NULL,
    `fechaCierre` DATE DEFAULT NULL,
    `estaHabilitado` BIT(1) DEFAULT b'0',
    `notificacionesCorreo` BIT(1) DEFAULT b'1',
    PRIMARY KEY (`idFormulario`),
    UNIQUE KEY `titulo` (`titulo`),
    KEY `idCreador` (`idCreador`),
    CONSTRAINT `formulario_ibfk_1` FOREIGN KEY (`idCreador`)
        REFERENCES `bdUsuarios`.`usuario` (`id`)
        ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `respuesta` (
    `idRespuesta` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `idFormulario` INT(10) UNSIGNED NOT NULL,
    `csv` MEDIUMTEXT NOT NULL,
    `fueEnviada` BIT(1) NOT NULL DEFAULT b'0',
    PRIMARY KEY (`idRespuesta` , `idFormulario`),
    KEY `idFormulario` (`idFormulario`),
    CONSTRAINT `respuesta_ibfk_1` FOREIGN KEY (`idFormulario`)
        REFERENCES `formulario` (`idFormulario`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `campo` (
    `idCampo` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
    `idFormulario` INT(10) UNSIGNED NOT NULL,
    `titulo` VARCHAR(40) NOT NULL,
    `descripcion` VARCHAR(200) DEFAULT NULL,
    `esObligatorio` BIT(1) DEFAULT b'0',
    `posicion` TINYINT(3) UNSIGNED NOT NULL,
    PRIMARY KEY (`idCampo`),
    KEY `idFormulario` (`idFormulario`),
    CONSTRAINT `campo_ibfk_1` FOREIGN KEY (`idFormulario`)
        REFERENCES `formulario` (`idFormulario`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `campo_texto` (
    `idCampo` INT(10) UNSIGNED NOT NULL,
    `pista` VARCHAR(50) DEFAULT NULL,
    `subtipo` ENUM('0', '1', '2') NOT NULL DEFAULT '0', # 0 = Campo de texto; 1 = Campo de texto para valores numéricos ; 2 = Campo de texto para direcciones de e-mail.
    PRIMARY KEY (`idCampo`),
    CONSTRAINT `campo_texto_ibfk_1` FOREIGN KEY (`idCampo`)
        REFERENCES `campo` (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `area_texto` (
    `idCampo` INT(10) UNSIGNED NOT NULL,
    `limiteCaracteres` SMALLINT(3) UNSIGNED NOT NULL,
    PRIMARY KEY (`idCampo`),
    CONSTRAINT `area_texto_ibfk_1` FOREIGN KEY (`idCampo`)
        REFERENCES `campo` (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `fecha` (
    `idCampo` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`idCampo`),
    CONSTRAINT `fecha_ibfk_1` FOREIGN KEY (`idCampo`)
        REFERENCES `campo` (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `lista_boton_radio` (
    `idCampo` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`idCampo`),
    CONSTRAINT `lista_boton_radio_ibfk_1` FOREIGN KEY (`idCampo`)
        REFERENCES `campo` (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `boton_radio` (
    `idLista` INT(10) UNSIGNED NOT NULL,
    `textoOpcion` VARCHAR(40) NOT NULL,
    `posicion` TINYINT(2) UNSIGNED NOT NULL,
    PRIMARY KEY (`idLista` , `textoOpcion`),
    CONSTRAINT `boton_radio_ibfk_1` FOREIGN KEY (`idLista`)
        REFERENCES `lista_boton_radio` (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `lista_checkbox` (
    `idCampo` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`idCampo`),
    CONSTRAINT `lista_checkbox_ibfk_1` FOREIGN KEY (`idCampo`)
        REFERENCES `campo` (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `checkbox` (
    `idLista` INT(10) UNSIGNED NOT NULL,
    `textoOpcion` VARCHAR(40) NOT NULL,
    `posicion` TINYINT(2) UNSIGNED NOT NULL,
    PRIMARY KEY (`idLista` , `textoOpcion`),
    CONSTRAINT `checkbox_ibfk_1` FOREIGN KEY (`idLista`)
        REFERENCES `lista_checkbox` (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `lista_desplegable` (
    `idCampo` INT(10) UNSIGNED NOT NULL,
    PRIMARY KEY (`idCampo`),
    CONSTRAINT `lista_desplegable_ibfk_1` FOREIGN KEY (`idCampo`)
        REFERENCES `campo` (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `opcion` (
    `idLista` INT(10) UNSIGNED NOT NULL,
    `textoOpcion` VARCHAR(40) NOT NULL,
    `posicion` TINYINT(3) UNSIGNED NOT NULL,
    PRIMARY KEY (`idLista` , `textoOpcion`),
    CONSTRAINT `opcion_ibfk_1` FOREIGN KEY (`idLista`)
        REFERENCES `lista_desplegable` (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `formulario_rol` (
    `idFormulario` INT(10) UNSIGNED NOT NULL,
    `idRol` INT(11) NOT NULL,
    PRIMARY KEY (`idFormulario` , `idRol`),
    CONSTRAINT `formulario_rol_ibfk_1` FOREIGN KEY (`idFormulario`)
        REFERENCES `formulario` (`idFormulario`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `formulario_rol_ibfk_2` FOREIGN KEY (`idRol`)
        REFERENCES `bdUsuarios`.`rol` (`id`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

CREATE TABLE `gestor_formularios` (
    `idUsuario` INT(11) NOT NULL,
    `cuotaCreacion` TINYINT(3) NOT NULL DEFAULT -1, # El valor -1 representa que el gestor de formularios tiene una cuota de creación de formularios ilimitada.
    `puedePublicar` BIT(1) NOT NULL DEFAULT b'0',
    PRIMARY KEY (`idUsuario`),
    CONSTRAINT `gestor_formularios_ibfk_1` FOREIGN KEY (`idUsuario`)
        REFERENCES `bdUsuarios`.`usuario_rol` (`id_usuario`)
        ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=INNODB DEFAULT CHARSET='UTF8';

USE `bdUsuarios`;

/* Se agregan los roles y permisos necesarios para el correcto funcionamiento del sistema Colibrí: */
INSERT INTO `rol` VALUES (-1, "Público general");

INSERT INTO `rol` VALUES (NULL, "Gestor de formularios");
SELECT @idRolGestorFormularios := LAST_INSERT_ID();

INSERT INTO `permiso` VALUES (NULL, "Crear formularios");
SELECT @idPermisoCrearFormulario := LAST_INSERT_ID();

INSERT INTO `rol` VALUES (NULL, "Administrador de gestores de formularios");
SELECT @idRolAdministradorGestores := LAST_INSERT_ID();

INSERT INTO `permiso` VALUES (NULL, "Administrar gestores de formularios");
SELECT @idPermisoAdministrarGestores := LAST_INSERT_ID();

INSERT INTO `permiso` VALUES (NULL, "Eliminar formularios");
SELECT @idPermisoEliminarFormulario := LAST_INSERT_ID();

INSERT INTO `rol_permiso` VALUES
    (@idRolGestorFormularios, @idPermisoCrearFormulario),
    (@idRolAdministradorGestores, @idPermisoAdministrarGestores),
    (@idRolAdministradorGestores, @idPermisoEliminarFormulario);