-- MySQL dump 10.13  Distrib 5.5.55, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: uargflow
-- ------------------------------------------------------
-- Server version	5.5.55-0ubuntu0.14.04.1
CREATE SCHEMA uargflow CHARSET UTF8;
USE uargflow;
/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `PERMISO`
--

DROP TABLE IF EXISTS `PERMISO`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PERMISO` (
  `idpermiso` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`idpermiso`),
  UNIQUE KEY `ID_PERMISO_IND` (`idpermiso`)
) ENGINE=InnoDB AUTO_INCREMENT=28 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `PERMISO`
--

LOCK TABLES `PERMISO` WRITE;
/*!40000 ALTER TABLE `PERMISO` DISABLE KEYS */;
INSERT INTO `PERMISO` VALUES (4,'Alta Documento'),(5,'Baja Documento'),(6,'Modificacion Documento'),(19,'Consultar'),(22,'Movimientos'),(24,'Modificacion Movimiento'),(25,'Reportes'),(26,'Usuarios'),(27,'Parametros');
/*!40000 ALTER TABLE `PERMISO` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ROL`
--

DROP TABLE IF EXISTS `ROL`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ROL` (
  `idrol` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  PRIMARY KEY (`idrol`),
  UNIQUE KEY `ID_ROL_IND` (`idrol`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ROL`
--

LOCK TABLES `ROL` WRITE;
/*!40000 ALTER TABLE `ROL` DISABLE KEYS */;
INSERT INTO `ROL` VALUES (1,'Administrador'),(2,'Usuario Sector'),(3,'Mesa de Entrada'),(4,'Usuario Consulta');
/*!40000 ALTER TABLE `ROL` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ROL_PERMISO`
--

DROP TABLE IF EXISTS `ROL_PERMISO`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ROL_PERMISO` (
  `idrol` int(11) NOT NULL,
  `idpermiso` int(11) NOT NULL,
  PRIMARY KEY (`idpermiso`,`idrol`),
  UNIQUE KEY `ID_ROL_PERMISO_IND` (`idpermiso`,`idrol`),
  KEY `FKASO_ROL_IND` (`idrol`),
  CONSTRAINT `FKASO_PER` FOREIGN KEY (`idpermiso`) REFERENCES `PERMISO` (`idpermiso`),
  CONSTRAINT `FKASO_ROL_FK` FOREIGN KEY (`idrol`) REFERENCES `ROL` (`idrol`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ROL_PERMISO`
--

LOCK TABLES `ROL_PERMISO` WRITE;
/*!40000 ALTER TABLE `ROL_PERMISO` DISABLE KEYS */;
INSERT INTO `ROL_PERMISO` VALUES (1,4),(1,5),(1,6),(1,19),(1,22),(1,24),(1,25),(1,26),(1,27),(2,19),(2,22),(2,24),(2,25),(3,4),(3,5),(3,6),(3,19),(3,22),(3,24),(3,25),(4,19);
/*!40000 ALTER TABLE `ROL_PERMISO` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `USUARIO`
--

DROP TABLE IF EXISTS `USUARIO`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `USUARIO` (
  `idusuario` int(11) NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `metodologin` varchar(25) NOT NULL,
  `estado` enum('Activo','Inactivo') NOT NULL DEFAULT 'Activo',
  PRIMARY KEY (`idusuario`),
  UNIQUE KEY `UN_USUARIO` (`email`,`nombre`),
  UNIQUE KEY `ID_USUARIO_IND` (`idusuario`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `USUARIO`
--

LOCK TABLES `USUARIO` WRITE;
/*!40000 ALTER TABLE `USUARIO` DISABLE KEYS */;
INSERT INTO `USUARIO` VALUES (1,'portaluarg@uarg.unpa.edu.ar','Portal UARG','Google','Activo'),(2,'esantos@uarg.unpa.edu.ar','Eder dos Santos','Google','Activo');
/*!40000 ALTER TABLE `USUARIO` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `USUARIO_GOOGLE`
--

DROP TABLE IF EXISTS `USUARIO_GOOGLE`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `USUARIO_GOOGLE` (
  `idusuario` int(11) NOT NULL,
  `googleid` varchar(255) NOT NULL,
  `imagen` varchar(500) NOT NULL,
  PRIMARY KEY (`idusuario`),
  UNIQUE KEY `SID_USUARIO_GOOGLE_ID` (`googleid`),
  UNIQUE KEY `SID_USUARIO_GOOGLE_IND` (`googleid`),
  UNIQUE KEY `FKUSU_USU_1_IND` (`idusuario`),
  CONSTRAINT `FKUSU_USU_1_FK` FOREIGN KEY (`idusuario`) REFERENCES `USUARIO` (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `USUARIO_GOOGLE`
--

LOCK TABLES `USUARIO_GOOGLE` WRITE;
/*!40000 ALTER TABLE `USUARIO_GOOGLE` DISABLE KEYS */;
INSERT INTO `USUARIO_GOOGLE` VALUES (1,'111909519395941528462','https://lh5.googleusercontent.com/-UwMjYu__O6I/AAAAAAAAAAI/AAAAAAAAAAA/AI6yGXwRG92VOwDQKGE45E5109FqeEb7Kg/s96-c/photo.jpg'),(2,'105571509298220793328','https://lh4.googleusercontent.com/-RuvGVp4_BAU/AAAAAAAAAAI/AAAAAAAAADs/xJN3qODrQPk/s96-c/photo.jpg');
/*!40000 ALTER TABLE `USUARIO_GOOGLE` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `USUARIO_MANUAL`
--

DROP TABLE IF EXISTS `USUARIO_MANUAL`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `USUARIO_MANUAL` (
  `idusuario` int(11) NOT NULL,
  `clave` char(1) NOT NULL,
  PRIMARY KEY (`idusuario`),
  UNIQUE KEY `FKUSU_USU_IND` (`idusuario`),
  CONSTRAINT `FKUSU_USU_FK` FOREIGN KEY (`idusuario`) REFERENCES `USUARIO` (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `USUARIO_MANUAL`
--

LOCK TABLES `USUARIO_MANUAL` WRITE;
/*!40000 ALTER TABLE `USUARIO_MANUAL` DISABLE KEYS */;
/*!40000 ALTER TABLE `USUARIO_MANUAL` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `USUARIO_ROL`
--

DROP TABLE IF EXISTS `USUARIO_ROL`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `USUARIO_ROL` (
  `idrol` int(11) NOT NULL,
  `idusuario` int(11) NOT NULL,
  PRIMARY KEY (`idrol`,`idusuario`),
  UNIQUE KEY `ID_USUARIO_ROL_IND` (`idrol`,`idusuario`),
  KEY `FKVIN_USU_IND` (`idusuario`),
  CONSTRAINT `FKVIN_ROL` FOREIGN KEY (`idrol`) REFERENCES `ROL` (`idrol`),
  CONSTRAINT `FKVIN_USU_FK` FOREIGN KEY (`idusuario`) REFERENCES `USUARIO` (`idusuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `USUARIO_ROL`
--

LOCK TABLES `USUARIO_ROL` WRITE;
/*!40000 ALTER TABLE `USUARIO_ROL` DISABLE KEYS */;
INSERT INTO `USUARIO_ROL` VALUES (1,2);
/*!40000 ALTER TABLE `USUARIO_ROL` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-08-23 16:55:04
