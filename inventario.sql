-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 15-12-2022 a las 01:20:10
-- Versión del servidor: 10.4.27-MariaDB
-- Versión de PHP: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `inventario`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categoria`
--

CREATE TABLE `categoria` (
  `id_categoria` int(10) NOT NULL,
  `nombre_categoria` varchar(60) NOT NULL,
  `ubicacion_categoria` varchar(150) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `categoria`
--

INSERT INTO `categoria` (`id_categoria`, `nombre_categoria`, `ubicacion_categoria`) VALUES
(3, 'Accesorios de consolas', 'Pasillo 7'),
(4, 'Laptops ACER', 'Pasillo 123'),
(5, 'PC de escritorio', 'Pasillo 12'),
(6, 'Tablets', 'Pasillo 10'),
(7, 'Video Juegos', 'Pasillo 1'),
(8, 'Accesorios de PC', 'Pasillo 7');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `producto`
--

CREATE TABLE `producto` (
  `id_producto` int(20) NOT NULL,
  `codigo_producto` varchar(70) NOT NULL,
  `nombre_producto` varchar(70) NOT NULL,
  `precio_producto` decimal(30,2) NOT NULL,
  `stock_producto` int(25) NOT NULL,
  `foto_producto` varchar(500) NOT NULL,
  `id_categoria` int(10) NOT NULL,
  `id_usuario` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `producto`
--

INSERT INTO `producto` (`id_producto`, `codigo_producto`, `nombre_producto`, `precio_producto`, `stock_producto`, `foto_producto`, `id_categoria`, `id_usuario`) VALUES
(2, '000001', 'FIFA 24', '150000.00', 105, 'FIFA_24_75.png', 7, 2),
(3, '55889', 'Laptop ACER', '1.50', 10, '', 4, 2),
(4, '52525', 'Pc Acer', '2.00', 2, 'Pc_Acer_80.jpg', 3, 2),
(6, '1231321', 'Prueba', '10000.00', 4, '', 8, 2);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id_usuario` int(10) NOT NULL,
  `nombre_usuario` varchar(40) NOT NULL,
  `apellido_usuario` varchar(40) NOT NULL,
  `usuario_usuario` varchar(20) NOT NULL,
  `clave_usuario` varchar(200) NOT NULL,
  `email_usuario` varchar(70) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_spanish2_ci;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id_usuario`, `nombre_usuario`, `apellido_usuario`, `usuario_usuario`, `clave_usuario`, `email_usuario`) VALUES
(2, 'Efrain', 'Vergara', 'SirPerziball', '$2y$10$KI2i0jNNWknjysZ2AYqlFO8QuFg4klgK.ItRfLln7dFBYVXvWIw8W', 'example@gmail.com'),
(5, 'Alison', 'Pacheco', 'AlisonGP', '$2y$10$Da72zyPBvbjhu2bmQ5b36O4sfqj2HMlSDtmJARhhyZkctb54h1piO', 'ejemplo@gmail.com'),
(6, 'Laura', 'Arevalo', 'Lauris', '$2y$10$90x3y1ZuWMRXRblCPM0h2uWQEAtPznvjltDaqBXbZjXUgEe68qKUu', 'ejemplox@gmail.com');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `categoria`
--
ALTER TABLE `categoria`
  ADD PRIMARY KEY (`id_categoria`);

--
-- Indices de la tabla `producto`
--
ALTER TABLE `producto`
  ADD PRIMARY KEY (`id_producto`),
  ADD KEY `id_categoria` (`id_categoria`),
  ADD KEY `id_usuario` (`id_usuario`),
  ADD KEY `id_categoria_2` (`id_categoria`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id_usuario`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `categoria`
--
ALTER TABLE `categoria`
  MODIFY `id_categoria` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `producto`
--
ALTER TABLE `producto`
  MODIFY `id_producto` int(20) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id_usuario` int(10) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `producto`
--
ALTER TABLE `producto`
  ADD CONSTRAINT `producto_ibfk_1` FOREIGN KEY (`id_categoria`) REFERENCES `categoria` (`id_categoria`),
  ADD CONSTRAINT `producto_ibfk_2` FOREIGN KEY (`id_usuario`) REFERENCES `usuario` (`id_usuario`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
