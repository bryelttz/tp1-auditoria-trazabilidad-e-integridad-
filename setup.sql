-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 22-06-2026 a las 22:59:29
-- Versión del servidor: 10.4.32-MariaDB
-- Versión de PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `frutas_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `inventario`
--

CREATE TABLE `inventario` (
  `id` int(11) NOT NULL,
  `fruta` varchar(50) NOT NULL,
  `cantidad` int(11) NOT NULL DEFAULT 0,
  `eliminado` tinyint(1) NOT NULL DEFAULT 0,
  `hash_control` varchar(32) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `inventario`
--

INSERT INTO `inventario` (`id`, `fruta`, `cantidad`, `eliminado`, `hash_control`, `fecha_creacion`) VALUES
(1, 'banana', 14, 0, '0e4e16518cad65065bb32a9fa5360f00', '2026-06-22 16:22:11'),
(2, 'manzana', 8, 0, '7e109c95605a631dcef0c75f560e17fd', '2026-06-22 16:22:11'),
(3, 'pera', 5, 0, 'bcb3917a645cbd2555a4e76b39d3484f', '2026-06-22 16:22:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `trazabilidad`
--

CREATE TABLE `trazabilidad` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `fecha_hora` datetime NOT NULL,
  `tipo_accion` varchar(20) NOT NULL,
  `detalles` text DEFAULT NULL,
  `ip_usuario` varchar(45) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `trazabilidad`
--

INSERT INTO `trazabilidad` (`id`, `usuario`, `fecha_hora`, `tipo_accion`, `detalles`, `ip_usuario`) VALUES
(1, 'admin', '2026-06-22 13:22:11', 'INSERCIÓN', 'Datos iniciales cargados: banana(12), manzana(8), pera(5)', NULL),
(2, 'admin', '2026-06-22 13:22:34', 'UPDATE', 'banana: 12 → 13 (+1)', NULL),
(3, 'admin', '2026-06-22 13:36:52', 'UPDATE', 'banana: 13 → 14 (+1)', NULL);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `inventario`
--
ALTER TABLE `inventario`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fruta` (`fruta`);

--
-- Indices de la tabla `trazabilidad`
--
ALTER TABLE `trazabilidad`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `inventario`
--
ALTER TABLE `inventario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `trazabilidad`
--
ALTER TABLE `trazabilidad`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
