USE uargflow;

/* Se inserta el rol a la base de datos */
INSERT INTO ROL(`nombre`) VALUES ("Gestor de formularios");

DROP TABLE IF EXISTS GESTOR_FORMULARIOS;

CREATE TABLE GESTOR_FORMULARIOS (
    idUsuario INT NOT NULL,
    limite SMALLINT DEFAULT -1,
    libertad BIT NOT NULL,
    FOREIGN KEY (idUsuario)
        REFERENCES USUARIO (`idusuario`)
        ON DELETE CASCADE ON UPDATE CASCADE
);