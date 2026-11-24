-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 17-10-2026 a las 01:03:01
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
-- Base de datos: `instituto`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `alumnos`
--

CREATE TABLE `alumnos` (
  `id` int(10) UNSIGNED NOT NULL,
  `nombre` varchar(60) NOT NULL,
  `apellido` varchar(60) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id`, `nombre`, `apellido`, `email`, `fecha_registro`) VALUES
(2, 'Jose Carlos', 'Ramirez', 'josecreuz@gmail.com', '2026-10-15 02:02:42'),
(3, 'Josue', 'Rivero', 'josuerivero@gmail.com', '2026-10-15 02:30:25'),
(6, 'Marta', 'Salva', 'martuchy123selvy@gmail.com', '2026-10-15 02:41:07'),
(7, 'Elias', 'Rojas', 'eliasrojas@gmail.com', '2026-10-15 02:53:46'),
(8, 'Marta Belen', 'Ortega', 'martega@gmail.com', '2026-10-15 02:55:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(10) UNSIGNED NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('Administrador','Docente','Alumno','Invitado') NOT NULL DEFAULT 'Alumno',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `email`, `password`, `rol`, `fecha_registro`) VALUES
(1, 'luz.sal', 'luz.ies11@gmail.com', '$2y$10$zqgH7M3kyBMVX2vZTtmoQe1AQNUX5m1qWdO7VzI8LXocGWQXsreei', 'Alumno', '2026-10-16 00:53:03'),
(2, 'admin', 'admin@ies11.edu.ar', '$2y$10$HH6v3l3H1ZyjFdBDvkec6.jpzmCn1y.8M3k8MvZWLDtGCQHztxTWG', 'Administrador', '2026-10-16 22:30:11'),
(3, 'docente', 'docente@ies11.edu.ar', '$2y$10$DfIKKGAYp5a6doq5NaBl0.Rps5frbrj7LJphQJeH.in4eQHd.Zpja', 'Docente', '2026-10-16 22:30:11'),
(4, 'alumno', 'alumno@ies11.edu.ar', '$2y$10$qbqVLi2.b5ldMwhvc056CuWos4YUoDRRqk6CAnoDWYm9SEd3us3Nm', 'Alumno', '2026-10-16 22:30:11'),
(5, 'invitado', 'invitado@ies11.edu.ar', '$2y$10$WBgZRqr2iuSbR5B/RYDC1OInbuCAB5TKodJGFArX8767IUPaPLXcy', 'Invitado', '2026-10-16 22:30:11');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usuario` (`usuario`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `alumnos`
--
ALTER TABLE `alumnos`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
