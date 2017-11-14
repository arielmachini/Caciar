DROP SCHEMA IF EXISTS COLIBRI ;
CREATE SCHEMA COLIBRI CHARSET utf8;
USE COLIBRI;

-- Eliminar esta tablas antes de crear todas las demas en en la BD
CREATE TABLE ROL (
  `idrol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`idrol`),
  UNIQUE KEY `ID_ROL_IND` (`idrol`)
);

CREATE TABLE USUARIO (
  `idusuario` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `metodologin` varchar(25) NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`idusuario`),
  UNIQUE KEY `UN_USUARIO` (`email`,`nombre`),
  UNIQUE KEY `ID_USUARIO_IND` (`idusuario`)
) ;

CREATE TABLE GESTOR_FORMULARIOS (
    `idusuario` INT(11) NOT NULL UNIQUE,
    `limite` SMALLINT DEFAULT - 1,
    `libertad` BIT NOT NULL,
    FOREIGN KEY (`idusuario`)
        REFERENCES USUARIO (`idusuario`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE FORMULARIO (
    `id_formulario` INTEGER ZEROFILL UNSIGNED AUTO_INCREMENT NOT NULL,
    `titulo` VARCHAR(64) NOT NULL,
    `descripcion` VARCHAR(500),
    `correo_respuesta` VARCHAR(255) NOT NULL,
    `cantidad_respuestas` INTEGER,
    `esta_habilitado` BIT,
    `fecha_inicio` DATE,
    `fecha_fin` DATE,
    `fecha_creacion` DATE,
    `creador` INT,
    FOREIGN KEY (`creador`)
		REFERENCES GESTOR_FORMULARIOS (`idusuario`),
    PRIMARY KEY (`id_formulario`)
);

CREATE TABLE PUBLICADO_PARA(
	`formulario` INTEGER ZEROFILL UNSIGNED NOT NULL,
    `rol` int(11) NOT NULL,
	FOREIGN KEY (`formulario`)
		REFERENCES FORMULARIO (`id_formulario`)
        ON DELETE CASCADE ON UPDATE CASCADE,
	FOREIGN KEY (`rol`)
		REFERENCES ROL (`idrol`)
        ON DELETE CASCADE ON UPDATE CASCADE,
	PRIMARY KEY (`formulario`,`rol`)
);
CREATE TABLE CAMPO (
    `id_campo` INTEGER ZEROFILL UNSIGNED AUTO_INCREMENT NOT NULL,
    `id_formulario` INTEGER ZEROFILL UNSIGNED NOT NULL,
    `titulo` VARCHAR(64) NOT NULL,
    `descripcion` VARCHAR(128),
    `obligatorio` BIT NOT NULL,
    `posicion` TINYINT NOT NULL,
    FOREIGN KEY (`id_formulario`)
        REFERENCES FORMULARIO (`id_formulario`)
        ON DELETE CASCADE ON UPDATE CASCADE,
    PRIMARY KEY (`id_campo`)
);

CREATE TABLE CAMPO_TEXTO(
	`campo` INTEGER ZEROFILL UNSIGNED AUTO_INCREMENT NOT NULL,
    `pista` VARCHAR(50),
    FOREIGN KEY (`campo`)
		REFERENCES CAMPO(`id_campo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE AREA_TEXTO(
	`campo` INTEGER ZEROFILL UNSIGNED AUTO_INCREMENT NOT NULL,
    `limite_caracteres` TINYINT,
    FOREIGN KEY (`campo`)
		REFERENCES CAMPO(`id_campo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE LISTA_DESPLEGABLE(
	`campo` INTEGER ZEROFILL UNSIGNED AUTO_INCREMENT NOT NULL,
	`opcionInicial` VARCHAR (50),
    FOREIGN KEY (`campo`)
		REFERENCES CAMPO(`id_campo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE ELEMENTOS_LISTA_DESPLEGABLE(
	`lista_desplegable` INTEGER ZEROFILL UNSIGNED AUTO_INCREMENT NOT NULL,
    `elemento` VARCHAR (50),
    FOREIGN KEY (`lista_desplegable`)
		REFERENCES LISTA_DESPLEGABLE(`campo`)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE RELLENADO_ENVIADO (
	`formulario` INTEGER ZEROFILL UNSIGNED  NOT NULL,
	`usuario` INT NOT NULL,
    FOREIGN KEY(`formulario`)
		REFERENCES FORMULARIO(`id_formulario`),
	FOREIGN KEY(`usuario`)
		REFERENCES USUARIO(`idusuario`),
	PRIMARY KEY(`formulario`, `usuario`)
);

CREATE TABLE INFORME(
	`id_informe` INTEGER ZEROFILL UNSIGNED AUTO_INCREMENT NOT NULL,
    `fecha_inicio` DATE,
    `fecha_fin`DATE,
    `intervalo_meses` TINYINT,
    PRIMARY KEY (`id_informe`)
);

CREATE TABLE INFORME_TIENE_FORMULARIO(
	`formulario` INTEGER ZEROFILL UNSIGNED NOT NULL,
    `informe` INTEGER ZEROFILL UNSIGNED NOT NULL,
	FOREIGN KEY (`formulario`)
        REFERENCES FORMULARIO (`id_formulario`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
	FOREIGN KEY (`informe`)
        REFERENCES INFORME (`id_informe`)
        ON DELETE RESTRICT ON UPDATE CASCADE,
	PRIMARY KEY(`formulario`, `informe`)
);
