USE uargflow;

DROP TABLE IF EXISTS FORMULARIO;

CREATE TABLE FORMULARIO (
    `idFormulario` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `titulo` VARCHAR(70) UNIQUE NOT NULL,
    `descripcion` VARCHAR(400),
    `emailReceptor` VARCHAR(256) NOT NULL,
    `cantidadRespuestas` MEDIUMINT UNSIGNED NOT NULL,
    `estaHabilitado` BIT DEFAULT 0,
    `fechaInicio` DATE,
    `fechaFin` DATE,
    `fechaCreacion` DATE,
    `idCreador` INT NOT NULL,
    
    PRIMARY KEY (`idFormulario`),
    FOREIGN KEY (`idCreador`)
        REFERENCES GESTOR_FORMULARIOS (`idUsuario`)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

DELIMITER %%
CREATE TRIGGER `FORMULARIO_FECHA_CREACION_POR_DEFECTO` BEFORE INSERT ON `FORMULARIO`
FOR EACH ROW BEGIN
	IF (ISNULL(NEW.`fechaCreacion`)) THEN
		SET NEW.`fechaCreacion` = STR_TO_DATE(CURDATE(), '%Y-%m-%d');
	END IF;
END%%
DELIMITER ;

/* * * Comienzo de tipos de campo * * */

CREATE TABLE CAMPO (
    `idCampo` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `idFormulario` INT UNSIGNED NOT NULL,
    `titulo` VARCHAR(70) NOT NULL,
    `descripcion` VARCHAR(100),
    `esObligatorio` BIT DEFAULT 0,
    `posicion` TINYINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (`idCampo`),
    FOREIGN KEY (`idFormulario`)
        REFERENCES FORMULARIO (`idFormulario`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE AREA_TEXTO (
    `idCampo` INT UNSIGNED NOT NULL,
    `limiteCaracteres` SMALLINT UNSIGNED NOT NULL,
    
    PRIMARY KEY (`idCampo`),
    FOREIGN KEY (`idCampo`)
        REFERENCES CAMPO (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE CAMPO_EMAIL (
	`idCampo` INT UNSIGNED NOT NULL,
    `pista` VARCHAR(60),
    
    PRIMARY KEY (`idCampo`),
    FOREIGN KEY (`idCampo`)
		REFERENCES CAMPO (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE CAMPO_TEXTO (
    `idCampo` INT UNSIGNED NOT NULL,
    `pista` VARCHAR(60),
    
    PRIMARY KEY (`idCampo`),
    FOREIGN KEY (`idCampo`)
        REFERENCES CAMPO (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE FECHA (
    `idCampo` INT UNSIGNED NOT NULL,
    `fechaMinima` DATE,
    `fechaMaxima` DATE,
    
    PRIMARY KEY (`idCampo`),
    FOREIGN KEY (`idCampo`)
        REFERENCES CAMPO (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

/* Subtipo de campo: Lista de checkboxes */

CREATE TABLE LISTA_CHECKBOX (
    `idCampo` INT UNSIGNED NOT NULL,
    
    PRIMARY KEY (`idCampo`),
    FOREIGN KEY (`idCampo`)
        REFERENCES CAMPO (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE CHECKBOX (
	`idCampo` INT UNSIGNED NOT NULL,
    `textoOpcion` VARCHAR(40) NOT NULL,
    
    PRIMARY KEY (`idCampo`, `textoOpcion`),
    FOREIGN KEY (`idCampo`)
		REFERENCES LISTA_CHECKBOX (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

/* Fin de lista de checkboxes */

/* Subtipo de campo: Lista desplegable */

CREATE TABLE LISTA_DESPLEGABLE (
    `idCampo` INT UNSIGNED NOT NULL,
    
    PRIMARY KEY (`idCampo`),
    FOREIGN KEY (`idCampo`)
        REFERENCES CAMPO (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE OPCION_LISTA_DESPLEGABLE (
    `idLista` INT UNSIGNED NOT NULL,
    `textoOpcion` VARCHAR(40) NOT NULL,
    
    PRIMARY KEY (`idLista` , `textoOpcion`),
    FOREIGN KEY (`idLista`)
        REFERENCES LISTA_DESPLEGABLE (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

/* Fin de lista desplegable */

/* Subtipo de campo: Lista de botones de radio */

CREATE TABLE LISTA_BOTON_RADIO (
	`idCampo` INT UNSIGNED NOT NULL,
    
    PRIMARY KEY (`idCampo`),
    FOREIGN KEY (`idCampo`)
		REFERENCES CAMPO (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

CREATE TABLE BOTON_RADIO (
	`idLista` INT UNSIGNED NOT NULL,
    `textoOpcion` VARCHAR(40) NOT NULL,
    
    PRIMARY KEY (`idLista`, `textoOpcion`),
    FOREIGN KEY (`idLista`)
		REFERENCES LISTA_BOTON_RADIO (`idCampo`)
        ON DELETE CASCADE ON UPDATE CASCADE
);

/* Fin de lista de botones de radio */

/* * * Fin de tipos de campo * * */