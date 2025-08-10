-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 08-08-2025 a las 02:20:49
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
-- Base de datos: `stockapp_v2_5`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `accesorios`
--

CREATE TABLE `accesorios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `descripcion` text DEFAULT NULL,
  `marca` varchar(100) DEFAULT NULL,
  `modelo` varchar(100) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `stock_actual` int(11) DEFAULT 0,
  `stock_minimo` int(11) DEFAULT 0,
  `precio_compra` decimal(10,2) DEFAULT 0.00,
  `precio_venta` decimal(10,2) DEFAULT 0.00,
  `ubicacion` varchar(100) DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp(),
  `actualizado_en` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `accesorios`
--

INSERT INTO `accesorios` (`id`, `nombre`, `descripcion`, `marca`, `modelo`, `categoria_id`, `stock_actual`, `stock_minimo`, `precio_compra`, `precio_venta`, `ubicacion`, `activo`, `creado_en`, `actualizado_en`) VALUES
(1, 'Accesorio 2', 'Bateria Motorola E7i', 'Motorola', 'E7i', 3, 10, 1, 0.00, 0.00, 'Local', 1, '2025-07-23 22:50:21', '2025-08-01 04:13:29'),
(2, 'Accesorio 3', 'Cable Motorola E7i', 'Motorola', 'E7i', 2, 1, 1, 0.00, 0.00, 'Local', 1, '2025-07-23 22:54:27', '2025-07-30 02:59:03'),
(8, 'Accesorio 1', 'Modulo Pantalla', 'Motorola', 'E7i', 1, 16, 10, 1000.00, 1000.00, 'Local', 1, '2025-07-26 04:10:07', '2025-08-03 01:40:40'),
(10, 'Accesorio 4', 'Cables', 'Motorola', 'E7i', 2, 10, 1, 0.00, 0.00, 'Local', 1, '2025-07-30 03:52:54', '2025-08-03 01:40:26'),
(11, 'Pantalla Ejemplo', '', 'Iphone', 'A10', 1, 10, 5, 0.00, 0.00, 'Deposito', 1, '2025-08-02 05:12:45', '2025-08-02 05:12:45'),
(12, 'Prueba-3', 'Descripcion...', 'Motorola', 'E7i', 3, 11, 5, 0.00, 0.00, 'Deposito', 1, '2025-08-03 02:05:21', '2025-08-03 02:05:21'),
(13, 'Pantalla Ejemplo X', '', 'Iphone', 'A10', 1, 1, 0, 0.00, 0.00, 'Deposito', 1, '2025-08-03 02:13:55', '2025-08-03 02:14:15'),
(14, 'Accesorio 5', '', 'Motorola', 'A10', 1, 10, 1, 0.00, 0.00, 'Deposito', 1, '2025-08-03 02:22:23', '2025-08-03 02:23:22'),
(16, 'Accesorio 51', '', '', '', 1, 12, 0, 0.00, 0.00, 'Local', 1, '2025-08-03 04:02:31', '2025-08-04 22:38:37'),
(17, 'Pantalla Ejemplo', '', '', '', 1, 0, 0, 0.00, 0.00, 'Local', 1, '2025-08-03 04:06:53', '2025-08-03 04:06:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`) VALUES
(1, 'Pantallas'),
(2, 'Cables'),
(3, 'Baterias');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `movimientos`
--

CREATE TABLE `movimientos` (
  `id` int(11) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `tipo` enum('entrada','salida','servicio') NOT NULL,
  `sector` enum('accesorio','servicio') NOT NULL,
  `item_id` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `observacion` text DEFAULT NULL,
  `activo` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `movimientos`
--

INSERT INTO `movimientos` (`id`, `usuario_id`, `tipo`, `sector`, `item_id`, `cantidad`, `total`, `observacion`, `activo`, `creado_en`) VALUES
(11, 1, 'salida', 'accesorio', 8, 5, 0.00, '', 1, '2025-07-31 04:30:20'),
(13, 1, 'servicio', 'servicio', 1, 1, 6300.00, 'activacion', 1, '2025-07-31 22:01:41'),
(14, 1, 'servicio', 'servicio', 2, 1, 5000.00, 'software', 1, '2025-08-01 04:14:14'),
(15, 1, 'servicio', 'servicio', 4, 1, 1000.00, 'frp', 1, '2025-08-02 03:24:03'),
(16, 1, 'entrada', 'accesorio', 8, 1, 0.00, 'Compra', 1, '2025-08-02 05:09:05'),
(17, 3, 'servicio', 'servicio', 10, 1, 2300.00, 'activacion', 1, '2025-08-03 08:05:51'),
(18, 3, 'entrada', 'accesorio', 16, 1, 0.00, 'Compra', 1, '2025-08-03 08:06:25'),
(19, 1, 'entrada', 'accesorio', 16, 10, 0.00, 'Compra', 1, '2025-08-04 22:38:37'),
(20, 1, 'servicio', 'servicio', 11, 1, 1111.00, 'activacion', 1, '2025-08-06 03:41:22');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `servicios`
--

CREATE TABLE `servicios` (
  `id` int(11) NOT NULL,
  `marca` varchar(100) NOT NULL,
  `modelo` varchar(100) NOT NULL,
  `activacion` decimal(10,2) DEFAULT 0.00,
  `software` decimal(10,2) DEFAULT 0.00,
  `frp` decimal(10,2) DEFAULT 0.00,
  `formatear` decimal(10,2) DEFAULT 0.00,
  `pin_de_carga` decimal(10,2) DEFAULT 0.00,
  `letras_rojas` tinyint(1) DEFAULT 0,
  `pegado_tapa` tinyint(1) DEFAULT 0,
  `activo` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `servicios`
--

INSERT INTO `servicios` (`id`, `marca`, `modelo`, `activacion`, `software`, `frp`, `formatear`, `pin_de_carga`, `letras_rojas`, `pegado_tapa`, `activo`, `creado_en`) VALUES
(1, 'Iphone', 'A1', 5000.00, 5000.00, 5000.00, 5000.00, 5000.00, 1, 1, 1, '2025-07-24 22:37:36'),
(2, 'Motorola', 'E7i', 5000.00, 5000.00, 5000.00, 5000.00, 5000.00, 1, 1, 1, '2025-07-24 22:53:31'),
(4, 'Samsung', 'Galaxy 10', 1000.00, 1000.00, 1000.00, 1000.00, 5000.00, 1, 0, 1, '2025-07-27 00:30:22'),
(10, 'Huawei', 'H10', 1000.00, 1000.00, 1000.00, 1000.00, 1000.00, 0, 0, 1, '2025-08-03 02:06:10'),
(11, 'Motorola', 'A10', 1111.00, 1111.00, 1111.00, 1111.00, 1111.00, 0, 0, 1, '2025-08-03 03:24:06'),
(12, 'Motorola', 'A10', 1111.00, 1111.00, 1111.00, 1111.00, 1111.00, 0, 0, 1, '2025-08-03 03:27:44'),
(13, 'Samsung', 'Edge', 1.00, 1.00, 1.00, 1.00, 1.00, 0, 0, 1, '2025-08-03 03:30:48'),
(14, 'a', 'a', 1.00, 1.00, 1.00, 1.00, 1.00, 0, 0, 1, '2025-08-03 03:31:25'),
(15, 'Motorola', 'A10', 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 1, '2025-08-03 04:07:17'),
(16, 'Huawei', 'Huawei', 0.00, 0.00, 0.00, 0.00, 0.00, 0, 0, 1, '2025-08-03 04:08:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('admin','empleado') DEFAULT 'empleado',
  `activo` tinyint(1) DEFAULT 1,
  `creado_en` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password`, `rol`, `activo`, `creado_en`) VALUES
(1, 'Administrador', 'admin@stockapp.com', '$2y$10$5Ziha11r0kTCUfP9HanOk.ufYN7uH2OAv58V7Ng9FnlF.DWQ8dKXW', 'admin', 1, '2025-07-07 13:46:39'),
(3, 'Empleado', 'empleado@stockapp.com', '$2y$10$8UCIludFfKf.rkRE3Z9UP.Err0C2CUeh.o1iOleiao8K2tVfhKhk2', 'empleado', 0, '2025-08-03 07:32:10');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `accesorios`
--
ALTER TABLE `accesorios`
  ADD PRIMARY KEY (`id`),
  ADD KEY `categoria_id` (`categoria_id`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD PRIMARY KEY (`id`),
  ADD KEY `usuario_id` (`usuario_id`);

--
-- Indices de la tabla `servicios`
--
ALTER TABLE `servicios`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `accesorios`
--
ALTER TABLE `accesorios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `movimientos`
--
ALTER TABLE `movimientos`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT de la tabla `servicios`
--
ALTER TABLE `servicios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `accesorios`
--
ALTER TABLE `accesorios`
  ADD CONSTRAINT `accesorios_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `movimientos`
--
ALTER TABLE `movimientos`
  ADD CONSTRAINT `movimientos_ibfk_1` FOREIGN KEY (`usuario_id`) REFERENCES `usuarios` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
