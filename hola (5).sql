-- phpMyAdmin SQL Dump
-- version 4.0.4
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 20-08-2025 a las 17:34:35
-- Versión del servidor: 5.6.12-log
-- Versión de PHP: 5.4.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Base de datos: `hola`
--
CREATE DATABASE IF NOT EXISTS `hola` DEFAULT CHARACTER SET latin1 COLLATE latin1_swedish_ci;
USE `hola`;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `areas_generacion`
--

CREATE TABLE IF NOT EXISTS `areas_generacion` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `descripcion` text,
  `activo` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `peligro_residuos`
--

CREATE TABLE IF NOT EXISTS `peligro_residuos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `unidad` varchar(50) NOT NULL,
  `crp` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `residuos_peligrosos`
--

CREATE TABLE IF NOT EXISTS `residuos_peligrosos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trabajador` varchar(255) DEFAULT NULL,
  `residuo` varchar(255) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `unidad` varchar(50) DEFAULT NULL,
  `crp` varchar(50) DEFAULT NULL,
  `area_generacion` varchar(255) DEFAULT NULL,
  `ingreso` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `residuos_peligrosos_terminados`
--

CREATE TABLE IF NOT EXISTS `residuos_peligrosos_terminados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trabajador` varchar(255) DEFAULT NULL,
  `residuo` varchar(255) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `unidad` varchar(50) DEFAULT NULL,
  `crp` varchar(50) DEFAULT NULL,
  `area_generacion` varchar(255) DEFAULT NULL,
  `ingreso` date DEFAULT NULL,
  `salida` date DEFAULT NULL,
  `fase_siguiente` varchar(255) DEFAULT NULL,
  `destino_razon_social` varchar(255) DEFAULT NULL,
  `manifiesto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `residuos_rme`
--

CREATE TABLE IF NOT EXISTS `residuos_rme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(255) NOT NULL,
  `clave` varchar(50) NOT NULL,
  `unidad` varchar(50) NOT NULL,
  `almacen` varchar(100) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rme`
--

CREATE TABLE IF NOT EXISTS `rme` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trabajador` varchar(255) DEFAULT NULL,
  `residuo` varchar(255) DEFAULT NULL,
  `clave` varchar(50) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `unidad` varchar(50) DEFAULT NULL,
  `almacen` varchar(255) DEFAULT NULL,
  `area_generacion` varchar(255) DEFAULT NULL,
  `ingreso` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rme_terminados`
--

CREATE TABLE IF NOT EXISTS `rme_terminados` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trabajador` varchar(255) DEFAULT NULL,
  `residuo` varchar(255) DEFAULT NULL,
  `clave` varchar(50) DEFAULT NULL,
  `cantidad` int(11) DEFAULT NULL,
  `unidad` varchar(50) DEFAULT NULL,
  `almacen` varchar(255) DEFAULT NULL,
  `area_generacion` varchar(255) DEFAULT NULL,
  `ingreso` date DEFAULT NULL,
  `salida` date DEFAULT NULL,
  `fase_siguiente` varchar(255) DEFAULT NULL,
  `destino_razon_social` varchar(255) DEFAULT NULL,
  `manifiesto` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `NumEmpleado` varchar(50) NOT NULL,
  `nombre` varchar(255) NOT NULL,
  `contrasena` varchar(255) DEFAULT NULL,
  `rol` int(1) NOT NULL DEFAULT '2',
  PRIMARY KEY (`id`),
  UNIQUE KEY `NumEmpleado` (`NumEmpleado`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=22 ;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `NumEmpleado`, `nombre`, `contrasena`, `rol`) VALUES
(1, 'admin', 'Hola', 'hola123', 1),
(2, '26', 'Karen Mejia Delgado', NULL, 2),
(3, '170', 'Francisco Garcia Rocha', NULL, 2),
(4, '440', 'Reynaldo Hernandez Juarez', NULL, 2),
(5, '790', 'Juan Pablo Mireles Suarez', NULL, 2),
(6, '856', 'Jesus Rebeles Velazquez', NULL, 2),
(7, '98', 'Jonathan Falcon Ortega', NULL, 2),
(8, '594', 'Jesus Hurtado Mena', NULL, 2),
(9, '356', 'Emmanuel Mercado Galarza', NULL, 2),
(10, '35', 'Yerania Campos Tovar', NULL, 2),
(11, '379', 'Pedro Negrete Almanza', NULL, 2),
(12, '377', 'Adriana Padilla Rodriguez', NULL, 2),
(13, '30', 'Julio Castillo Ontiveros', NULL, 2),
(14, '251', 'Emmanuel Vazquez Zavala', NULL, 2),
(15, '799', 'Victor Mejia Delgado', NULL, 2),
(16, '531', 'Edgar Carmona López', NULL, 2),
(17, '16', 'Antonio Jurado Acosta', NULL, 2),
(18, '473', 'Narzedalia Rodriguez Soto', NULL, 2),
(19, '467', 'Raul Alanis Torralba', NULL, 2),
(20, 'Aux1', 'Maria', NULL, 2),
(21, 'Aux2', 'Yoana', NULL, 2);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
