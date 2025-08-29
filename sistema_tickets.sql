-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1
-- Tiempo de generación: 29-08-2025 a las 13:38:58
-- Versión del servidor: 10.4.24-MariaDB
-- Versión de PHP: 7.4.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `sistema_tickets`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `area`
--

CREATE TABLE `area` (
  `id_area` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `area`
--

INSERT INTO `area` (`id_area`, `nombre`) VALUES
(1, 'Transformación Digital'),
(3, 'Servicios Generales'),
(4, 'Equipos Médicos');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `backup`
--

CREATE TABLE `backup` (
  `id` int(11) NOT NULL,
  `usuario` varchar(100) NOT NULL,
  `archivo` varchar(300) NOT NULL,
  `fecha` date NOT NULL,
  `hora` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Volcado de datos para la tabla `backup`
--

INSERT INTO `backup` (`id`, `usuario`, `archivo`, `fecha`, `hora`) VALUES
(61, 'admin', 'procedimiento1709047088.sql', '2024-02-27', '12:18:08'),
(62, 'admin', 'procedimiento1709047233.sql', '2024-02-27', '12:20:33'),
(63, 'admin', 'procedimiento1709047314.sql', '2024-02-27', '12:21:54'),
(64, 'admin', 'procedimiento1729712513.sql', '2024-10-23', '16:41:53');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `carga_masiva`
--

CREATE TABLE `carga_masiva` (
  `id_carga_masiva` int(11) NOT NULL,
  `archivo` varchar(255) NOT NULL,
  `modulo` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `configuracion`
--

CREATE TABLE `configuracion` (
  `id_configuracion` int(11) NOT NULL,
  `logo_login` varchar(300) NOT NULL,
  `logo_panel` varchar(300) NOT NULL,
  `titulo_sistema` varchar(200) NOT NULL,
  `color_fondo_menu_panel` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `configuracion`
--

INSERT INTO `configuracion` (`id_configuracion`, `logo_login`, `logo_panel`, `titulo_sistema`, `color_fondo_menu_panel`) VALUES
(1, '1710180276_hospital.jpg', '1710180276_hospital.jpg', 'Web Hospital', '#9e4141');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `fallas`
--

CREATE TABLE `fallas` (
  `id_falla` int(11) NOT NULL,
  `nombre_fa` varchar(100) NOT NULL,
  `id_area` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `fallas`
--

INSERT INTO `fallas` (`id_falla`, `nombre_fa`, `id_area`) VALUES
(1, 'Computador No Enciende', 1),
(2, 'Goteras', 3),
(3, 'Corte de Luz', 3),
(4, 'Enchufes defectuosos', 3),
(5, 'Error de clave de GIS', 1),
(6, 'Creación de Sistema Clínico', 1),
(7, 'Activación de Office', 1),
(8, 'Problema de Correo', 1),
(9, 'Problemas con Archivos', 1),
(10, 'Enrolamientos de Médicos', 1),
(11, 'Fallas de Maquinarias Clínicas', 4),
(12, 'perros', 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `funcionarios`
--

CREATE TABLE `funcionarios` (
  `id_funcionarios` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` int(11) NOT NULL,
  `grado` int(11) NOT NULL,
  `area` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `menu`
--

CREATE TABLE `menu` (
  `id_menu` int(11) NOT NULL,
  `nombre_menu` varchar(100) NOT NULL,
  `url_menu` varchar(300) NOT NULL,
  `icono_menu` varchar(100) NOT NULL,
  `submenu` varchar(100) NOT NULL,
  `orden_menu` tinyint(4) NOT NULL,
  `area_protegida_menu` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `menu`
--

INSERT INTO `menu` (`id_menu`, `nombre_menu`, `url_menu`, `icono_menu`, `submenu`, `orden_menu`, `area_protegida_menu`) VALUES
(4, 'usuarios', '/usuarios', 'fas fa-users', 'No', 10, 'Si'),
(5, 'Perfil', '/perfil', 'far fa-user', 'No', 11, 'Si'),
(6, 'Respalda tus Datos', '/respaldos', 'fas fa-database', 'No', 5, 'Si'),
(7, 'Salir', '/salir', 'fas fa-sign-out-alt', 'No', 12, 'Si'),
(10, 'Mantenedor Menu', '/menu', 'fas fa-bars', 'No', 6, 'Si'),
(12, 'Acceso Menus', '/acceso_menus', 'fas fa-outdent', 'No', 8, 'Si'),
(19, 'Generador de Módulos', '/modulos', 'fas fa-table', 'No', 1, 'Si'),
(141, 'Documentación', '/Documentacion/index', 'fas fa-book', 'No', 9, 'Si'),
(272, 'Tickets', '/crud_ticket', 'far fa-file-alt', 'No', 2, 'Si'),
(273, 'Asignar Fallas', '/asignacion', 'fab fa-adn', 'No', 3, 'Si'),
(274, 'Técnicos', '/tecnicos', 'fab fa-alipay', 'No', 4, 'Si');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `modulos`
--

CREATE TABLE `modulos` (
  `id_modulos` int(11) NOT NULL,
  `tabla` varchar(100) NOT NULL,
  `id_tabla` varchar(100) DEFAULT NULL,
  `crud_type` varchar(100) NOT NULL,
  `query` text DEFAULT NULL,
  `controller_name` varchar(100) NOT NULL,
  `columns_table` text DEFAULT NULL,
  `name_view` varchar(100) NOT NULL,
  `add_menu` varchar(100) NOT NULL,
  `template_fields` varchar(100) NOT NULL,
  `id_menu` int(11) DEFAULT NULL,
  `active_filter` varchar(100) NOT NULL,
  `clone_row` varchar(100) NOT NULL,
  `active_popup` varchar(100) NOT NULL,
  `active_search` varchar(100) NOT NULL,
  `activate_deleteMultipleBtn` varchar(100) NOT NULL,
  `button_add` varchar(100) NOT NULL,
  `actions_buttons_grid` varchar(100) DEFAULT NULL,
  `modify_query` text DEFAULT NULL,
  `activate_nested_table` varchar(100) NOT NULL,
  `buttons_actions` varchar(100) DEFAULT NULL,
  `logo_pdf` varchar(300) DEFAULT NULL,
  `marca_de_agua_pdf` varchar(300) DEFAULT NULL,
  `activate_pdf` varchar(100) NOT NULL,
  `refrescar_grilla` varchar(100) NOT NULL,
  `consulta_pdf` text DEFAULT NULL,
  `id_campos_insertar` varchar(100) DEFAULT NULL,
  `encryption` varchar(100) DEFAULT NULL,
  `mostrar_campos_busqueda` varchar(300) NOT NULL,
  `mostrar_columnas_grilla` varchar(300) DEFAULT NULL,
  `mostrar_campos_formulario` varchar(300) DEFAULT NULL,
  `activar_recaptcha` varchar(100) NOT NULL,
  `sitekey_recaptcha` varchar(500) DEFAULT NULL,
  `sitesecret_repatcha` varchar(500) DEFAULT NULL,
  `mostrar_campos_filtro` varchar(300) DEFAULT NULL,
  `tipo_de_filtro` text DEFAULT NULL,
  `function_filter_and_search` varchar(100) DEFAULT NULL,
  `activar_union_interna` varchar(100) NOT NULL,
  `mostrar_campos_formulario_editar` varchar(300) DEFAULT NULL,
  `posicion_botones_accion_grilla` varchar(100) NOT NULL,
  `campos_requeridos` varchar(100) NOT NULL,
  `mostrar_columna_acciones_grilla` varchar(100) NOT NULL,
  `mostrar_paginacion` varchar(100) NOT NULL,
  `activar_numeracion_columnas` varchar(100) NOT NULL,
  `activar_registros_por_pagina` varchar(100) NOT NULL,
  `cantidad_de_registros_por_pagina` varchar(100) NOT NULL,
  `activar_edicion_en_linea` varchar(100) NOT NULL,
  `nombre_modulo` varchar(100) DEFAULT NULL,
  `ordenar_grilla_por` varchar(500) DEFAULT NULL,
  `tipo_orden` varchar(100) DEFAULT NULL,
  `posicionarse_en_la_pagina` varchar(100) DEFAULT NULL,
  `nombre_columnas` text DEFAULT NULL,
  `nuevo_nombre_columnas` text DEFAULT NULL,
  `ocultar_id_tabla` varchar(100) NOT NULL,
  `nombre_campos` text DEFAULT NULL,
  `nuevo_nombre_campos` text DEFAULT NULL,
  `totalRecordsInfo` varchar(100) NOT NULL,
  `area_protegida_por_login` varchar(100) NOT NULL,
  `tabla_principal_union` varchar(500) DEFAULT NULL,
  `tabla_secundaria_union` varchar(500) DEFAULT NULL,
  `campos_relacion_union_tabla_principal` text DEFAULT NULL,
  `campos_relacion_union_tabla_secundaria` text DEFAULT NULL,
  `posicion_filtro` varchar(100) DEFAULT NULL,
  `file_callback` varchar(100) DEFAULT NULL,
  `type_callback` text DEFAULT NULL,
  `type_fields` text NOT NULL,
  `text_no_data` varchar(100) DEFAULT NULL,
  `type_union` varchar(100) DEFAULT NULL,
  `send_email` varchar(100) NOT NULL,
  `activar_union_izquierda` varchar(100) NOT NULL,
  `tabla_principal_union_izquierda` varchar(500) DEFAULT NULL,
  `campos_relacion_union_tabla_principal_izquierda` varchar(500) DEFAULT NULL,
  `tabla_secundaria_union_izquierda` varchar(500) DEFAULT NULL,
  `campos_relacion_union_tabla_secundaria_izquierda` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `rol`
--

CREATE TABLE `rol` (
  `idrol` int(11) NOT NULL,
  `nombre_rol` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `rol`
--

INSERT INTO `rol` (`idrol`, `nombre_rol`) VALUES
(1, 'Administrador'),
(2, 'Tecnico'),
(3, 'Asignador');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `sector`
--

CREATE TABLE `sector` (
  `id_sector` int(11) NOT NULL,
  `nombre_sector` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `sector`
--

INSERT INTO `sector` (`id_sector`, `nombre_sector`) VALUES
(1, 'Pediatria'),
(2, 'Maternidad'),
(3, 'Esterilización'),
(4, 'Medicina'),
(5, 'UTI/UCI'),
(6, 'Cirugía'),
(7, 'Pensionado'),
(8, 'Recaudación Pensionado'),
(9, 'Pabellón'),
(10, 'Urgencia Maternal'),
(11, 'Central Teléfonica'),
(12, 'HD Pasillo'),
(13, 'HD Container'),
(14, 'Urgencia Pediátrica'),
(15, 'Preparto'),
(16, 'Toma de Muestras'),
(17, 'Ecografía'),
(18, 'Ventanilla Rayos'),
(19, 'Kinesiologia'),
(20, 'Ventanilla Laboratorio'),
(21, 'Mamografia'),
(22, 'Oficina Rayos'),
(23, 'Biopsias'),
(24, 'Oficina Urgencia'),
(25, 'Sala Observación'),
(26, 'Trabajadores Sociales Urgencia'),
(27, 'Box Adulto Urgencia'),
(28, 'Recaudación Urgencia'),
(29, 'Selector Urgencia'),
(30, 'ESI4'),
(31, 'Oficina Abastecimiento'),
(32, 'Oficina Reclutamiento'),
(33, 'Of Personal'),
(34, 'Contabilidad'),
(35, 'Auditoria'),
(36, 'Oficina de Partes'),
(37, 'Calidad'),
(38, 'Bienestar'),
(39, 'Dirección'),
(40, 'Subdirección'),
(41, 'GRD'),
(42, 'Oficina Tesoreria'),
(43, 'Recaudación'),
(44, 'Capacitación'),
(45, 'Bodega Abastecimiento'),
(46, 'Oficina Servicios Generales'),
(47, 'Oficina Equipos Médicos'),
(48, 'Lavanderia'),
(49, 'Archivos'),
(50, 'Archivos Pasivos'),
(51, 'Sigges'),
(52, 'Container Nadia'),
(53, 'Comunicaciones - Finis'),
(54, 'IAS - Finis'),
(55, 'Sala de Reuniones - Finis'),
(56, 'OIRS - Finis'),
(57, 'Lista de Espera - Finis'),
(58, 'Chile Crece - Finis'),
(59, 'Gestión de la Demanda - Finis');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `submenu`
--

CREATE TABLE `submenu` (
  `id_submenu` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `nombre_submenu` varchar(100) NOT NULL,
  `url_submenu` varchar(300) NOT NULL,
  `icono_submenu` varchar(100) NOT NULL,
  `orden_submenu` tinyint(4) NOT NULL,
  `area_protegida_submenu` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tecnicos`
--

CREATE TABLE `tecnicos` (
  `id_tecnicos` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `correo` varchar(100) NOT NULL,
  `area` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tecnicos`
--

INSERT INTO `tecnicos` (`id_tecnicos`, `nombre`, `correo`, `area`) VALUES
(1, 'Andres Llanos', 'sergio.llanosc@redsalud.gob.cl', 1),
(2, 'Jorge Berrios', 'jorge.berrios@redsalud.gob.cl', 1),
(3, 'Leonardo Martinez', 'leonardo.martinez@redsalud.gob.cl', 2),
(4, 'Franco Carrasco', 'franco.carrasco@redsalud.gob.cl', 1),
(5, 'Fabian Pacheco', 'fabian.pacheco@redsalud.gob.cl', 1),
(6, 'Daniel Huerta', 'daniel.huerta@redsalud.gob.cl', 2),
(7, 'Juan Pablo Álvarez Avalos', 'juan.alvarez@redsalud.gob.cl', 1),
(8, 'Nadia Salas', 'nadia.salas@redsalud.gob.cl', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tickets`
--

CREATE TABLE `tickets` (
  `id_tickets` int(11) NOT NULL,
  `n_ticket` varchar(100) NOT NULL,
  `nombreTecnico` varchar(100) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha` date NOT NULL,
  `correo` varchar(100) NOT NULL,
  `area` int(11) NOT NULL,
  `fallas` int(11) NOT NULL,
  `sector_funcionario` int(11) NOT NULL,
  `foto` varchar(300) DEFAULT NULL,
  `hora_asignacion` time NOT NULL,
  `hora_inicio` time NOT NULL,
  `hora_termino` time NOT NULL,
  `estado` varchar(100) NOT NULL,
  `prioridad` varchar(100) DEFAULT NULL,
  `observaciones` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario`
--

CREATE TABLE `usuario` (
  `id` int(11) NOT NULL,
  `nombre` varchar(50) NOT NULL,
  `email` varchar(200) NOT NULL,
  `usuario` varchar(15) NOT NULL,
  `password` varchar(200) NOT NULL,
  `token` longtext NOT NULL,
  `token_api` longtext NOT NULL,
  `expiration_token` int(11) DEFAULT NULL,
  `idrol` int(11) NOT NULL,
  `estatus` int(11) NOT NULL,
  `avatar` varchar(300) NOT NULL,
  `area` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usuario`
--

INSERT INTO `usuario` (`id`, `nombre`, `email`, `usuario`, `password`, `token`, `token_api`, `expiration_token`, `idrol`, `estatus`, `avatar`, `area`) VALUES
(1, 'Daniel Huerta', '6', 'dhuerta', '$2y$10$O9cXZmV/cKfhn1kL1miGr.6gPX2hRVjTG83BUNUzhb0tYPJ0Cma.C', '$2y$10$Li.WdUHXU0T8lr5l6vuf7.UN2/eBoLDv90jJaTMHxcWAq1OQJHK6W', 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpZCI6IjEiLCJlbWFpbCI6ImRhbmllbC50ZWxlbWF0aWNvQGdtYWlsLmNvbSIsInRpbWVzdGFtcCI6MTcyOTg3ODA3OSwiZXhwIjoxNzI5ODgxNjc5fQ.3ixpmkuvXfjnCkmoaCFtjEh0FyZm3vuYcrqEZYMHVac', 0, 2, 1, '1707312535_1707234514_1668021806_2.png', 2),
(24, 'Andres Llanos', '1', 'allanos', '$2y$10$wW.MFBwRbLmRG87MbF1rEuB0cCWZyNz./D6yy8zPXuNFXokvpkqca', '$2y$10$GiKV6TqNd5r8x8DrldNz8eJIdD/tZ6GvfuVsNfDcdOnvEcxJVvfsS', '', 0, 2, 1, '1710162578_user.png', 1),
(25, 'Jorge Berrios', '2', 'jberrios', '$2y$10$9h2B1esG9XVLvuGjAmZkZe8gJ6zYD3NgcwT8AD..MBIhapTmAgpEe', '$2y$10$GiKV6TqNd5r8x8DrldNz8eJIdD/tZ6GvfuVsNfDcdOnvEcxJVvfsS', '', 0, 2, 1, '1710162578_user.png', 1),
(26, 'Leonardo Martinez', '3', 'lmartinez', '$2y$10$RPdrm54yindnDpR5DaRhq.7lmR4kP1nyYMwNw4AxpgjKNWMjXxkPi', '$2y$10$GiKV6TqNd5r8x8DrldNz8eJIdD/tZ6GvfuVsNfDcdOnvEcxJVvfsS', '', 0, 2, 1, '1710162578_user.png', 2),
(27, 'Nadia Salas', '8', 'nsalas', '$2y$10$FeVQ1C/C1IANvKvI4T8Vi.94NNjlA.hdx2ipJnwGvXZ73Yx/NCjyi', '$2y$10$GiKV6TqNd5r8x8DrldNz8eJIdD/tZ6GvfuVsNfDcdOnvEcxJVvfsS', '', 0, 3, 1, '1710162578_user.png', 1),
(28, 'Fabian Pacheco', '5', 'fpacheco', '$2y$10$e/xTaHIpW1Rbz1ed/.hybe3l1dbpjavJ4WfpExprKIwyj7YuU57QS', '$2y$10$GiKV6TqNd5r8x8DrldNz8eJIdD/tZ6GvfuVsNfDcdOnvEcxJVvfsS', '', 0, 2, 1, '1710162578_user.png', 1),
(29, 'Franco Carrasco', '4', 'fcarrasco', '$2y$10$qVN/OwhjfI5GQgTYmyEzWeFA2OHdXHt7L1iiJIikSQufJ5NVEzoCi', '$2y$10$GiKV6TqNd5r8x8DrldNz8eJIdD/tZ6GvfuVsNfDcdOnvEcxJVvfsS', '', 0, 2, 1, '1710162578_user.png', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_menu`
--

CREATE TABLE `usuario_menu` (
  `id_usuario_menu` int(11) NOT NULL,
  `id_usuario` int(11) DEFAULT NULL,
  `id_menu` int(11) DEFAULT NULL,
  `visibilidad_menu` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `usuario_menu`
--

INSERT INTO `usuario_menu` (`id_usuario_menu`, `id_usuario`, `id_menu`, `visibilidad_menu`) VALUES
(1156, 1, 1, 'Mostrar'),
(1159, 1, 4, 'Mostrar'),
(1160, 1, 5, 'Mostrar'),
(1161, 1, 6, 'Mostrar'),
(1162, 1, 7, 'Mostrar'),
(1165, 1, 10, 'Mostrar'),
(1166, 20, 1, 'Mostrar'),
(1169, 20, 4, 'Mostrar'),
(1170, 20, 5, 'Mostrar'),
(1171, 20, 6, 'Mostrar'),
(1172, 20, 7, 'Mostrar'),
(1175, 20, 10, 'Mostrar'),
(1176, 1, 12, 'Mostrar'),
(1179, 1, 19, 'Ocultar'),
(1299, 1, 141, 'Mostrar'),
(1430, 1, 272, 'Mostrar'),
(1431, 1, 273, 'Mostrar'),
(1432, 1, 274, 'Mostrar'),
(1433, 24, 4, 'Mostrar'),
(1434, 24, 5, 'Mostrar'),
(1435, 24, 6, 'Mostrar'),
(1436, 24, 7, 'Mostrar'),
(1437, 24, 10, 'Mostrar'),
(1438, 24, 12, 'Mostrar'),
(1439, 24, 141, 'Mostrar'),
(1440, 24, 272, 'Mostrar'),
(1441, 24, 273, 'Mostrar'),
(1442, 24, 274, 'Mostrar'),
(1443, 25, 4, 'Mostrar'),
(1444, 25, 5, 'Mostrar'),
(1445, 25, 6, 'Mostrar'),
(1446, 25, 7, 'Mostrar'),
(1447, 25, 10, 'Mostrar'),
(1448, 25, 12, 'Mostrar'),
(1449, 25, 141, 'Mostrar'),
(1450, 25, 272, 'Mostrar'),
(1451, 25, 273, 'Mostrar'),
(1452, 25, 274, 'Mostrar'),
(1453, 26, 4, 'Mostrar'),
(1454, 26, 5, 'Mostrar'),
(1455, 26, 6, 'Mostrar'),
(1456, 26, 7, 'Mostrar'),
(1457, 26, 10, 'Mostrar'),
(1458, 26, 12, 'Mostrar'),
(1459, 26, 19, 'Mostrar'),
(1460, 26, 141, 'Mostrar'),
(1461, 26, 272, 'Mostrar'),
(1462, 26, 273, 'Mostrar'),
(1463, 26, 274, 'Mostrar'),
(1464, 27, 4, 'Mostrar'),
(1465, 27, 5, 'Mostrar'),
(1466, 27, 6, 'Mostrar'),
(1467, 27, 7, 'Mostrar'),
(1468, 27, 10, 'Mostrar'),
(1469, 27, 12, 'Mostrar'),
(1470, 27, 141, 'Mostrar'),
(1471, 27, 272, 'Mostrar'),
(1472, 27, 273, 'Mostrar'),
(1473, 27, 274, 'Mostrar');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuario_submenu`
--

CREATE TABLE `usuario_submenu` (
  `id_usuario_submenu` int(11) NOT NULL,
  `id_submenu` int(11) NOT NULL,
  `id_menu` int(11) NOT NULL,
  `visibilidad_submenu` varchar(100) NOT NULL,
  `id_usuario` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `area`
--
ALTER TABLE `area`
  ADD PRIMARY KEY (`id_area`);

--
-- Indices de la tabla `backup`
--
ALTER TABLE `backup`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `carga_masiva`
--
ALTER TABLE `carga_masiva`
  ADD PRIMARY KEY (`id_carga_masiva`);

--
-- Indices de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  ADD PRIMARY KEY (`id_configuracion`);

--
-- Indices de la tabla `fallas`
--
ALTER TABLE `fallas`
  ADD PRIMARY KEY (`id_falla`);

--
-- Indices de la tabla `funcionarios`
--
ALTER TABLE `funcionarios`
  ADD PRIMARY KEY (`id_funcionarios`);

--
-- Indices de la tabla `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id_menu`);

--
-- Indices de la tabla `modulos`
--
ALTER TABLE `modulos`
  ADD PRIMARY KEY (`id_modulos`);

--
-- Indices de la tabla `rol`
--
ALTER TABLE `rol`
  ADD PRIMARY KEY (`idrol`);

--
-- Indices de la tabla `sector`
--
ALTER TABLE `sector`
  ADD PRIMARY KEY (`id_sector`);

--
-- Indices de la tabla `submenu`
--
ALTER TABLE `submenu`
  ADD PRIMARY KEY (`id_submenu`),
  ADD KEY `id_menu` (`id_menu`);

--
-- Indices de la tabla `tecnicos`
--
ALTER TABLE `tecnicos`
  ADD PRIMARY KEY (`id_tecnicos`);

--
-- Indices de la tabla `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`id_tickets`);

--
-- Indices de la tabla `usuario`
--
ALTER TABLE `usuario`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `usuario_menu`
--
ALTER TABLE `usuario_menu`
  ADD PRIMARY KEY (`id_usuario_menu`);

--
-- Indices de la tabla `usuario_submenu`
--
ALTER TABLE `usuario_submenu`
  ADD PRIMARY KEY (`id_usuario_submenu`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `area`
--
ALTER TABLE `area`
  MODIFY `id_area` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `backup`
--
ALTER TABLE `backup`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=65;

--
-- AUTO_INCREMENT de la tabla `carga_masiva`
--
ALTER TABLE `carga_masiva`
  MODIFY `id_carga_masiva` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `configuracion`
--
ALTER TABLE `configuracion`
  MODIFY `id_configuracion` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `fallas`
--
ALTER TABLE `fallas`
  MODIFY `id_falla` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `funcionarios`
--
ALTER TABLE `funcionarios`
  MODIFY `id_funcionarios` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `menu`
--
ALTER TABLE `menu`
  MODIFY `id_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=275;

--
-- AUTO_INCREMENT de la tabla `modulos`
--
ALTER TABLE `modulos`
  MODIFY `id_modulos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=302;

--
-- AUTO_INCREMENT de la tabla `rol`
--
ALTER TABLE `rol`
  MODIFY `idrol` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `sector`
--
ALTER TABLE `sector`
  MODIFY `id_sector` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=60;

--
-- AUTO_INCREMENT de la tabla `submenu`
--
ALTER TABLE `submenu`
  MODIFY `id_submenu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `tecnicos`
--
ALTER TABLE `tecnicos`
  MODIFY `id_tecnicos` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `tickets`
--
ALTER TABLE `tickets`
  MODIFY `id_tickets` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `usuario`
--
ALTER TABLE `usuario`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT de la tabla `usuario_menu`
--
ALTER TABLE `usuario_menu`
  MODIFY `id_usuario_menu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1474;

--
-- AUTO_INCREMENT de la tabla `usuario_submenu`
--
ALTER TABLE `usuario_submenu`
  MODIFY `id_usuario_submenu` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
