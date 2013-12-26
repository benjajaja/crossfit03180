-- phpMyAdmin SQL Dump
-- version OVH
-- http://www.phpmyadmin.net
--
-- Servidor: mysql51-97.perso
-- Tiempo de generación: 26-12-2013 a las 15:22:05
-- Versión del servidor: 5.1.66
-- Versión de PHP: 5.3.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Base de datos: `crossfit`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `eventos`
--
-- Creación: 21-12-2013 a las 18:58:13
--

CREATE TABLE IF NOT EXISTS `eventos` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) NOT NULL,
  `max_usuarios` int(3) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `evento_calendario`
--
-- Creación: 25-12-2013 a las 11:10:32
--

CREATE TABLE IF NOT EXISTS `evento_calendario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_evento` int(3) NOT NULL,
  `x` int(2) NOT NULL,
  `y` int(2) NOT NULL,
  `estado` int(1) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_evento` (`id_evento`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--
-- Creación: 20-12-2013 a las 12:46:24
--

CREATE TABLE IF NOT EXISTS `usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre` varchar(30) NOT NULL,
  `pass` varchar(15) NOT NULL,
  `email` varchar(50) NOT NULL,
  `bonos` int(5) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_evento`
--
-- Creación: 22-12-2013 a las 12:50:30
--

CREATE TABLE IF NOT EXISTS `usuario_evento` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_usuario` int(11) NOT NULL,
  `id_evento` int(30) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `id_usuario` (`id_usuario`),
  KEY `id_evento` (`id_evento`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=0 ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `evento_calendario`
--
ALTER TABLE `evento_calendario`
  ADD CONSTRAINT `evento_calendario_ibfk_4` FOREIGN KEY (`id_evento`) REFERENCES `eventos` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuario_evento`
--
ALTER TABLE `usuario_evento`
  ADD CONSTRAINT `usuario_evento_ibfk_3` FOREIGN KEY (`id_evento`) REFERENCES `evento_calendario` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `usuario_evento_ibfk_1` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
