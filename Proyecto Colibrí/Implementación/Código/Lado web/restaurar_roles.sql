/* Restaura los roles de los usuarios a administrador (ID 1) */

USE uargflow;

SELECT 
    *
FROM
    GESTOR_FORMULARIOS; /* Se muestran los usuarios que se van a actualizar */

UPDATE uargflow.USUARIO_ROL
SET idrol = 1
WHERE idusuario = 3;

UPDATE uargflow.USUARIO_ROL
SET idrol = 1
WHERE idusuario = 4;

UPDATE uargflow.USUARIO_ROL
SET idrol = 1
WHERE idusuario = 2;