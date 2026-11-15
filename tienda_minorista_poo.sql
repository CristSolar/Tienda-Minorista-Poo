-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-11-2026 a las 19:29:52
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
-- Base de datos: `tienda_minorista_poo`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id_categoria` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `descripcion` varchar(150) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id_categoria`, `nombre`, `descripcion`) VALUES
(14, 'Abarrotes', 'Alimentos empaquetados, granos y productos de despensa'),
(15, 'Bebidas', 'Refrescos, jugos, agua y bebidas embotelladas'),
(16, 'Lácteos', 'Leche, quesos, crema, yogures y derivados'),
(17, 'Limpieza', 'Detergentes, desinfectantes y artículos del hogar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `productos`
--

CREATE TABLE `productos` (
  `id_producto` int(11) NOT NULL,
  `codigo` varchar(30) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `id_categoria` int(11) NOT NULL,
  `precio` decimal(10,2) NOT NULL CHECK (`precio` >= 0),
  `cantidad_disponible` int(11) NOT NULL DEFAULT 0 CHECK (`cantidad_disponible` >= 0),
  `existencia_minima` int(11) NOT NULL DEFAULT 5 CHECK (`existencia_minima` >= 0),
  `fecha_registro` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `productos`
--

INSERT INTO `productos` (`id_producto`, `codigo`, `nombre`, `id_categoria`, `precio`, `cantidad_disponible`, `existencia_minima`, `fecha_registro`) VALUES
(1, 'PROD-001', 'Arroz Extra 1kg', 14, 28.50, 25, 10, '2026-10-07 00:30:50'),
(2, 'PROD-002', 'Aceite Vegetal 900ml', 14, 45.00, 4, 8, '2026-10-07 00:30:50'),
(3, 'PROD-003', 'Frijol Negro 1kg', 14, 32.00, 15, 5, '2026-10-07 00:30:50'),
(4, 'PROD-004', 'Azúcar Estándar 1kg', 14, 26.00, 30, 10, '2026-10-07 00:30:50'),
(5, 'PROD-005', 'Pasta Espagueti 200g', 14, 11.50, 2, 6, '2026-10-07 00:30:50'),
(6, 'PROD-006', 'Refresco Cola 600ml', 15, 18.00, 40, 15, '2026-10-07 00:30:50'),
(7, 'PROD-007', 'Agua Mineral 1.5L', 15, 22.00, 12, 5, '2026-10-07 00:30:50'),
(8, 'PROD-008', 'Jugo de Naranja 1L', 15, 28.00, 3, 10, '2026-10-07 00:30:50'),
(9, 'PROD-009', 'Té Helado Limón 500ml', 15, 16.50, 18, 8, '2026-10-07 00:30:50'),
(10, 'PROD-010', 'Bebida Energizante 473ml', 15, 38.00, 8, 5, '2026-10-07 00:30:50'),
(11, 'PROD-011', 'Leche Entera 1L', 16, 26.00, 15, 5, '2026-10-07 00:30:50'),
(12, 'PROD-012', 'Queso Panela 400g', 16, 65.00, 6, 4, '2026-10-07 00:30:50'),
(13, 'PROD-013', 'Yogurt Natural 1kg', 16, 42.00, 0, 5, '2026-10-07 00:30:50'),
(14, 'PROD-014', 'Crema Ácida 200g', 16, 19.50, 0, 6, '2026-10-07 00:30:50'),
(15, 'PROD-015', 'Mantequilla con Sal 90g', 16, 18.50, 22, 10, '2026-10-07 00:30:50'),
(16, 'PROD-016', 'Detergente en Polvo 1kg', 17, 38.00, 12, 5, '2026-10-07 00:30:50'),
(17, 'PROD-017', 'Cloro Multiusos 1L', 17, 15.00, 17, 10, '2026-10-07 00:30:50'),
(18, 'PROD-018', 'Limpiador Multiusos 1L', 17, 24.50, 1, 8, '2026-10-07 00:30:50'),
(19, 'PROD-019', 'Jabón para Trastes 750ml', 17, 31.00, 16, 6, '2026-10-07 00:30:50'),
(20, 'PROD-020', 'Suavizante de Telas 850ml', 17, 29.00, 9, 5, '2026-10-07 00:30:50'),
(25, '', 'Arroz Extra 1kg', 14, 28.50, 0, 5, '2026-10-08 07:20:38'),
(28, 'PRD-ABC808', 'Arroz Extra 1kg', 14, 28.50, 20, 5, '2026-10-08 07:25:17'),
(29, 'PRD-18238A', 'Arroz Extra 1kg', 14, 28.50, 10, 5, '2026-10-08 21:52:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedores`
--

CREATE TABLE `proveedores` (
  `id` int(11) NOT NULL,
  `nombre_empresa` varchar(100) NOT NULL,
  `contacto` varchar(100) DEFAULT NULL,
  `telefono` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `direccion` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `proveedores`
--

INSERT INTO `proveedores` (`id`, `nombre_empresa`, `contacto`, `telefono`, `email`, `direccion`) VALUES
(1, 'Distribuidora del Sur S.A.', 'Carlos Pérez', '9611234567', 'contacto@delsur.com', '1ra Poniente Norte #140, Tuxtla Gutiérrez'),
(2, 'Comercializadora Chiapaneca', 'Ana Gómez', '9619876543', 'ventas@comerciachiapas.com', 'Blvd. Belisario Domínguez #1500, Tuxtla Gutiérrez');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id`, `nombre_rol`, `descripcion`) VALUES
(1, 'ADMINISTRADOR', 'Control total del sistema, gestión de usuarios y reportes'),
(2, 'VENDEDOR', 'Acceso restringido al catálogo y registro de ventas');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `contrasena` varchar(255) NOT NULL,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp(),
  `rol` varchar(20) NOT NULL DEFAULT 'comun'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `usuario`, `contrasena`, `fecha_creacion`, `rol`) VALUES
(1, 'administrador', 'SODC060806', '2026-10-07 07:35:37', 'admin'),
(2, 'vendedor', '123456', '2026-10-07 07:41:08', 'comun');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `ventas`
--

CREATE TABLE `ventas` (
  `id_venta` int(11) NOT NULL,
  `id_producto` int(11) NOT NULL,
  `cantidad` int(11) NOT NULL,
  `total` decimal(10,2) NOT NULL,
  `fecha_venta` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `ventas`
--

INSERT INTO `ventas` (`id_venta`, `id_producto`, `cantidad`, `total`, `fecha_venta`) VALUES
(1, 13, 2, 84.00, '2026-11-04 15:11:19'),
(2, 14, 14, 273.00, '2026-11-04 15:11:49'),
(3, 9, 2, 33.00, '2026-11-09 13:41:55'),
(4, 17, 13, 195.00, '2026-11-09 13:47:27'),
(5, 3, 3, 96.00, '2026-11-09 13:50:36');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id_categoria`),
  ADD UNIQUE KEY `nombre` (`nombre`);

--
-- Indices de la tabla `productos`
--
ALTER TABLE `productos`
  ADD PRIMARY KEY (`id_producto`),
  ADD UNIQUE KEY `codigo` (`codigo`),
  ADD KEY `fk_productos_categorias` (`id_categoria`);

--
-- Indices de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`),
  ADD UNIQUE KEY `usuario` (`usuario`);

--
-- Indices de la tabla `ventas`
--
ALTER TABLE `ventas`
  ADD PRIMARY KEY (`id_venta`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id_categoria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `productos`
--
ALTER TABLE `productos`
  MODIFY `id_producto` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `proveedores`
--
ALTER TABLE `proveedores`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `ventas`
--
ALTER TABLE `ventas`
  MODIFY `id_venta` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `productos`
--
ALTER TABLE `productos`
  ADD CONSTRAINT `fk_productos_categorias` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
