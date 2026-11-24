-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3307
-- Tiempo de generación: 24-11-2026 a las 14:08:19
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
  `usuario_id` int(11) DEFAULT NULL,
  `nombre` varchar(60) NOT NULL,
  `apellido` varchar(60) NOT NULL,
  `email` varchar(100) NOT NULL,
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `alumnos`
--

INSERT INTO `alumnos` (`id`, `usuario_id`, `nombre`, `apellido`, `email`, `fecha_registro`) VALUES
(2, NULL, 'Jose Carlos', 'Ramirez', 'josecreuz@gmail.com', '2026-10-15 02:02:42'),
(3, NULL, 'Josue', 'Rivero', 'josuerivero@gmail.com', '2026-10-15 02:30:25'),
(6, NULL, 'Marta', 'Salva', 'martuchy123selvy@gmail.com', '2026-10-15 02:41:07'),
(9, NULL, 'Ana', 'Martinez', 'ana.martinez@ies11.edu.ar', '2026-11-24 07:02:46'),
(10, NULL, 'Lucas', 'Rivero', 'lucas.rivero@ies11.edu.ar', '2026-11-24 07:02:46'),
(11, 3, 'Alumno', 'Prueba', 'alumno@ies11.edu.ar', '2026-11-24 10:57:32');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `intentos_login`
--

CREATE TABLE `intentos_login` (
  `id` int(10) UNSIGNED NOT NULL,
  `clave_hash` varchar(64) NOT NULL,
  `fecha_intento` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `notificaciones`
--

CREATE TABLE `notificaciones` (
  `id` int(11) NOT NULL,
  `tipo` varchar(50) NOT NULL,
  `titulo` varchar(150) NOT NULL,
  `mensaje` varchar(255) NOT NULL,
  `usuario_id` int(11) DEFAULT NULL,
  `rol_destino` varchar(30) DEFAULT NULL,
  `leida` tinyint(1) NOT NULL DEFAULT 0,
  `fecha_creacion` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `notificaciones`
--

INSERT INTO `notificaciones` (`id`, `tipo`, `titulo`, `mensaje`, `usuario_id`, `rol_destino`, `leida`, `fecha_creacion`) VALUES
(1, 'alumno', 'Nuevo alumno registrado', 'Juan Pérez fue incorporado al sistema académico.', NULL, 'Administrador', 0, '2026-11-24 08:47:01');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id` int(11) NOT NULL,
  `usuario` varchar(50) NOT NULL,
  `email` varchar(120) NOT NULL,
  `password` varchar(255) NOT NULL,
  `rol` enum('Administrador','Docente','Alumno','Invitado') NOT NULL DEFAULT 'Invitado',
  `fecha_registro` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id`, `usuario`, `email`, `password`, `rol`, `fecha_registro`) VALUES
(1, 'admin', 'admin@ies11.edu.ar', '$2y$10$HH6v3l3H1ZyjFdBDvkec6.jpzmCn1y.8M3k8MvZWLDtGCQHztxTWG', 'Administrador', '2026-11-24 05:25:00'),
(2, 'docente', 'docente@ies11.edu.ar', '$2y$10$DfIKKGAYp5a6doq5NaBl0.Rps5frbrj7LJphQJeH.in4eQHd.Zpja', 'Docente', '2026-11-24 05:25:00'),
(3, 'alumno', 'alumno@ies11.edu.ar', '$2y$10$qbqVLi2.b5ldMwhvc056CuWos4YUoDRRqk6CAnoDWYm9SEd3us3Nm', 'Alumno', '2026-11-24 05:25:00'),
(4, 'invitado', 'invitado@ies11.edu.ar', '$2y$10$WBgZRqr2iuSbR5B/RYDC1OInbuCAB5TKodJGFArX8767IUPaPLXcy', 'Invitado', '2026-11-24 05:25:00');

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
-- Indices de la tabla `intentos_login`
--
ALTER TABLE `intentos_login`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_clave_hash` (`clave_hash`),
  ADD KEY `idx_fecha_intento` (`fecha_intento`);

--
-- Indices de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_usuario_id` (`usuario_id`),
  ADD KEY `idx_rol_destino` (`rol_destino`),
  ADD KEY `idx_fecha_creacion` (`fecha_creacion`);

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
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT de la tabla `intentos_login`
--
ALTER TABLE `intentos_login`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `notificaciones`
--
ALTER TABLE `notificaciones`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
