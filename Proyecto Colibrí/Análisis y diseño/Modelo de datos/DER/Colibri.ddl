-- *********************************************
-- * Standard SQL generation                   
-- *--------------------------------------------
-- * DB-MAIN version: 10.0.3              
-- * Generator date: Aug 23 2017              
-- * Generation date: Tue Nov  7 02:25:17 2017 
-- * LUN file: /home/cin/Dropbox/UNPA/2017/Lab. de desarrollo (Repositorio)/Colibri/Proyecto Colibrí/Análisis y diseño/Modelo de datos/DER/Colibri.lun 
-- * Schema: DER-Colibri_con UARGFLOW/SQL4 
-- ********************************************* 


-- Database Section
-- ________________ 

create database DER-Colibri_con UARGFLOW;


-- DBSpace Section
-- _______________


-- Tables Section
-- _____________ 

create table Administrador (
     idRol char(1) not null);

create table Campo (
     idCampo numeric(1) not null,
     etiqueta varchar(1) not null,
     obligatorio char not null,
     pista char(1) not null,
     numeroCampo char(1) not null,
     html char(1) not null,
     Texto_Simple numeric(1),
     Texto_Largo numeric(1),
     Opciones numeric(1),
     Fecha numeric(1),
     direccion numeric(1),
     constraint ID_Campo_ID primary key (idCampo));

create table COMPLETADO_ENVIADO (
     idFormulario numeric(1) not null,
     idUsuario char(1) not null,
     constraint ID_COMPLETADO_ENVIADO_ID primary key (idFormulario, idUsuario));

create table destinatarios (
     idFormulario numeric(1) not null,
     destinatarios date not null,
     constraint ID_destinatarios_ID primary key (idFormulario, destinatarios));

create table direccion (
     idCampo numeric(1) not null,
     calle char(1) not null,
     numero char(1) not null,
     ciudad char(1) not null,
     provincia char(1) not null,
     pais char(1) not null,
     codigo_postal char(1) not null,
     constraint FKCam_dir_ID primary key (idCampo));

create table elementos (
     idCampo numeric(1) not null,
     elementos char(1) not null,
     constraint ID_elementos_ID primary key (idCampo, elementos));

create table Fecha (
     idCampo numeric(1) not null,
     dia char(1) not null,
     mes char(1) not null,
     ano char(1) not null,
     calendario char(1) not null,
     constraint FKCam_Fec_ID primary key (idCampo));

create table Formulario (
     idFormulario numeric(1) not null,
     nombre varchar(1) not null,
     correo_respuesta varchar(1) not null,
     fechaDesde date not null,
     fechaHasta date not null,
     cantidad_respuestas numeric(1) not null,
     idCampo numeric(1) not null,
     constraint ID_Formulario_ID primary key (idFormulario));

create table Gestor_de_Formulario (
     cantidad_limite numeric(1) default 0 not null,
     libertad_publicacion char not null,
     idFormulario numeric(1) not null);

create table Informe (
     idInforme numeric(1) not null,
     fecha_Inicio date not null,
     fechaFin date not null,
     intervaloMeses numeric(1) not null,
     constraint ID_Informe_ID primary key (idInforme));

create table Lista_Desplegable (
);

create table opcionElegida (
     idCampo numeric(1) not null,
     opcionElegida char(1) not null,
     constraint ID_opcionElegida_ID primary key (idCampo, opcionElegida));

create table Opciones (
     idCampo numeric(1) not null,
     constraint FKCam_Opc_ID primary key (idCampo));

create table Texto_Largo (
     idCampo numeric(1) not null,
     parrafo char(1) not null,
     constraint FKCam_Tex_ID primary key (idCampo));

create table Texto_Simple (
     idCampo numeric(1) not null,
     lineaTexto char(1) not null,
     constraint FKCam_Tex_1_ID primary key (idCampo));

create table TIENE (
     idFormulario numeric(1) not null,
     idInforme numeric(1) not null,
     constraint ID_TIENE_ID primary key (idInforme, idFormulario));

create table TIENE_ASIGNADO (
     idUsuario char(1) not null,
     idRol char(1) not null,
     constraint ID_TIENE_ASIGNADO_ID primary key (idRol, idUsuario));

create table Workflow_Permiso (
     idPermiso char(1) not null,
     nombre char(1) not null,
     constraint ID_Workflow_Permiso_ID primary key (idPermiso));

create table WorkflowRol (
     idRol char(1) not null,
     nombre char(1) not null,
     idPermiso char(1) not null,
     constraint ID_WorkflowRol_ID primary key (idRol));

create table WorkflowUsuario (
     idUsuario char(1) not null,
     nombre char(1) not null,
     email char(1) not null,
     metodoLogin char(1) not null,
     estado char(1) not null,
     constraint ID_WorkflowUsuario_ID primary key (idUsuario),
     constraint SID_WorkflowUsuario_ID unique (email));


-- Constraints Section
-- ___________________ 

alter table Administrador add constraint FKASIGNA_FK
     foreign key (idRol)
     references WorkflowRol;

alter table Campo add constraint ID_Campo_CHK
     check(exists(select * from Formulario
                  where Formulario.idCampo = idCampo)); 

alter table Campo add constraint EXTONE_Campo
     check((direccion is not null and Fecha is null and Opciones is null and Texto_Largo is null and Texto_Simple is null)
           or (direccion is null and Fecha is not null and Opciones is null and Texto_Largo is null and Texto_Simple is null)
           or (direccion is null and Fecha is null and Opciones is not null and Texto_Largo is null and Texto_Simple is null)
           or (direccion is null and Fecha is null and Opciones is null and Texto_Largo is not null and Texto_Simple is null)
           or (direccion is null and Fecha is null and Opciones is null and Texto_Largo is null and Texto_Simple is not null)); 

alter table COMPLETADO_ENVIADO add constraint FKCOM_Wor_FK
     foreign key (idUsuario)
     references WorkflowUsuario;

alter table COMPLETADO_ENVIADO add constraint FKCOM_For
     foreign key (idFormulario)
     references Formulario;

alter table destinatarios add constraint FKFor_des
     foreign key (idFormulario)
     references Formulario;

alter table direccion add constraint FKCam_dir_FK
     foreign key (idCampo)
     references Campo;

alter table elementos add constraint FKOpc_ele
     foreign key (idCampo)
     references Opciones;

alter table Fecha add constraint FKCam_Fec_FK
     foreign key (idCampo)
     references Campo;

alter table Formulario add constraint ID_Formulario_CHK
     check(exists(select * from COMPLETADO_ENVIADO
                  where COMPLETADO_ENVIADO.idFormulario = idFormulario)); 

alter table Formulario add constraint ID_Formulario_CHK
     check(exists(select * from Gestor_de_Formulario
                  where Gestor_de_Formulario.idFormulario = idFormulario)); 

alter table Formulario add constraint ID_Formulario_CHK
     check(exists(select * from destinatarios
                  where destinatarios.idFormulario = idFormulario)); 

alter table Formulario add constraint ID_Formulario_CHK
     check(exists(select * from TIENE
                  where TIENE.idFormulario = idFormulario)); 

alter table Formulario add constraint FKESTA_COMPUESTO_POR_FK
     foreign key (idCampo)
     references Campo;

alter table Gestor_de_Formulario add constraint FKCREADOR_POR_FK
     foreign key (idFormulario)
     references Formulario;

alter table opcionElegida add constraint FKOpc_opc
     foreign key (idCampo)
     references Opciones;

alter table Opciones add constraint FKCam_Opc_CHK
     check(exists(select * from elementos
                  where elementos.idCampo = idCampo)); 

alter table Opciones add constraint FKCam_Opc_CHK
     check(exists(select * from opcionElegida
                  where opcionElegida.idCampo = idCampo)); 

alter table Opciones add constraint FKCam_Opc_FK
     foreign key (idCampo)
     references Campo;

alter table Texto_Largo add constraint FKCam_Tex_FK
     foreign key (idCampo)
     references Campo;

alter table Texto_Simple add constraint FKCam_Tex_1_FK
     foreign key (idCampo)
     references Campo;

alter table TIENE add constraint FKTIE_Inf
     foreign key (idInforme)
     references Informe;

alter table TIENE add constraint FKTIE_For_FK
     foreign key (idFormulario)
     references Formulario;

alter table TIENE_ASIGNADO add constraint FKTIE_Wor_1
     foreign key (idRol)
     references WorkflowRol;

alter table TIENE_ASIGNADO add constraint FKTIE_Wor_FK
     foreign key (idUsuario)
     references WorkflowUsuario;

alter table Workflow_Permiso add constraint ID_Workflow_Permiso_CHK
     check(exists(select * from WorkflowRol
                  where WorkflowRol.idPermiso = idPermiso)); 

alter table WorkflowRol add constraint ID_WorkflowRol_CHK
     check(exists(select * from Administrador
                  where Administrador.idRol = idRol)); 

alter table WorkflowRol add constraint ID_WorkflowRol_CHK
     check(exists(select * from TIENE_ASIGNADO
                  where TIENE_ASIGNADO.idRol = idRol)); 

alter table WorkflowRol add constraint FKES_ASIGNADO_FK
     foreign key (idPermiso)
     references Workflow_Permiso;

alter table WorkflowUsuario add constraint ID_WorkflowUsuario_CHK
     check(exists(select * from COMPLETADO_ENVIADO
                  where COMPLETADO_ENVIADO.idUsuario = idUsuario)); 

alter table WorkflowUsuario add constraint ID_WorkflowUsuario_CHK
     check(exists(select * from TIENE_ASIGNADO
                  where TIENE_ASIGNADO.idUsuario = idUsuario)); 


-- Index Section
-- _____________ 

create index FKASIGNA_IND
     on Administrador (idRol);

create unique index ID_Campo_IND
     on Campo (idCampo);

create unique index ID_COMPLETADO_ENVIADO_IND
     on COMPLETADO_ENVIADO (idFormulario, idUsuario);

create index FKCOM_Wor_IND
     on COMPLETADO_ENVIADO (idUsuario);

create unique index ID_destinatarios_IND
     on destinatarios (idFormulario, destinatarios);

create unique index FKCam_dir_IND
     on direccion (idCampo);

create unique index ID_elementos_IND
     on elementos (idCampo, elementos);

create unique index FKCam_Fec_IND
     on Fecha (idCampo);

create unique index ID_Formulario_IND
     on Formulario (idFormulario);

create index FKESTA_COMPUESTO_POR_IND
     on Formulario (idCampo);

create index FKCREADOR_POR_IND
     on Gestor_de_Formulario (idFormulario);

create unique index ID_Informe_IND
     on Informe (idInforme);

create unique index ID_opcionElegida_IND
     on opcionElegida (idCampo, opcionElegida);

create unique index FKCam_Opc_IND
     on Opciones (idCampo);

create unique index FKCam_Tex_IND
     on Texto_Largo (idCampo);

create unique index FKCam_Tex_1_IND
     on Texto_Simple (idCampo);

create unique index ID_TIENE_IND
     on TIENE (idInforme, idFormulario);

create index FKTIE_For_IND
     on TIENE (idFormulario);

create unique index ID_TIENE_ASIGNADO_IND
     on TIENE_ASIGNADO (idRol, idUsuario);

create index FKTIE_Wor_IND
     on TIENE_ASIGNADO (idUsuario);

create unique index ID_Workflow_Permiso_IND
     on Workflow_Permiso (idPermiso);

create unique index ID_WorkflowRol_IND
     on WorkflowRol (idRol);

create index FKES_ASIGNADO_IND
     on WorkflowRol (idPermiso);

create unique index ID_WorkflowUsuario_IND
     on WorkflowUsuario (idUsuario);

create unique index SID_WorkflowUsuario_IND
     on WorkflowUsuario (email);

