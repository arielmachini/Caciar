/* Restablecer la tabla GESTOR_FORMULARIOS */

USE uargflow;

INSERT INTO ROL(`nombre`) VALUES ("Gestor de formularios");

DROP TABLE IF EXISTS GESTOR_FORMULARIOS; /* Se elimina la tabla si existe en la base de datos. */

CREATE TABLE GESTOR_FORMULARIOS (
    idusuario INT NOT NULL UNIQUE,
    limite SMALLINT DEFAULT - 1,
    libertad BIT NOT NULL,
    FOREIGN KEY (idusuario)
        REFERENCES USUARIO (idusuario)
        ON DELETE CASCADE ON UPDATE CASCADE
); /* Y se vuelve a crear */