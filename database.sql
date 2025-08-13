-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Servidor: 127.0.0.1:3306
-- Tiempo de generación: 12-08-2025 a las 15:09:44
-- Versión del servidor: 9.1.0
-- Versión de PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `stock_manager`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `brands`
--

DROP TABLE IF EXISTS `brands`;
CREATE TABLE IF NOT EXISTS `brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `brands`
--

INSERT INTO `brands` (`id`, `name`) VALUES
(7, 'Multiprinter'),
(6, 'Infinity'),
(8, 'Magma'),
(9, 'Cartrige'),
(10, 'Ecolaser'),
(11, 'NDM'),
(12, 'Epson');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `consumable_models`
--

DROP TABLE IF EXISTS `consumable_models`;
CREATE TABLE IF NOT EXISTS `consumable_models` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `consumable_models`
--

INSERT INTO `consumable_models` (`id`, `name`) VALUES
(9, '662'),
(8, '664'),
(7, '122'),
(6, '667'),
(10, '85A'),
(11, '83A'),
(12, '226'),
(13, '26A'),
(14, '105A'),
(15, 'epson model');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `departments`
--

DROP TABLE IF EXISTS `departments`;
CREATE TABLE IF NOT EXISTS `departments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `departments`
--

INSERT INTO `departments` (`id`, `name`) VALUES
(1, 'Recursos Humanos'),
(2, 'Administracion'),
(3, 'Pre Prensa'),
(4, 'O.T.'),
(5, 'Facturacion'),
(6, 'Gerencia'),
(7, 'Comercial'),
(8, 'sgc minerva'),
(9, 'Produccion'),
(10, 'Informatica');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `printer_brands`
--

DROP TABLE IF EXISTS `printer_brands`;
CREATE TABLE IF NOT EXISTS `printer_brands` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `printer_brands`
--

INSERT INTO `printer_brands` (`id`, `name`) VALUES
(14, 'Epson'),
(13, 'HP');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `printer_models`
--

DROP TABLE IF EXISTS `printer_models`;
CREATE TABLE IF NOT EXISTS `printer_models` (
  `id` int NOT NULL AUTO_INCREMENT,
  `printer_brand_id` int NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `printer_brand_id` (`printer_brand_id`,`name`)
) ENGINE=MyISAM AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `printer_models`
--

INSERT INTO `printer_models` (`id`, `printer_brand_id`, `name`) VALUES
(23, 14, 'L-350'),
(15, 13, 'LaserJet Pro 402dne'),
(16, 13, 'LaserJet Pro M201 dw'),
(17, 13, 'LaserJet Pro P1102w'),
(18, 13, 'LaserJet 107w'),
(19, 13, 'Deskjet Ink Advantage 2775'),
(20, 13, 'Deskjet Ink Advantage 2545'),
(21, 13, 'Deskjet Ink Advantage 2135'),
(22, 13, 'Deskjet serie 2050');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `recipients`
--

DROP TABLE IF EXISTS `recipients`;
CREATE TABLE IF NOT EXISTS `recipients` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `recipients`
--

INSERT INTO `recipients` (`id`, `name`) VALUES
(1, 'Marcelo Sosa'),
(2, 'Diana'),
(3, 'Reinaldo');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `replenishment_orders`
--

DROP TABLE IF EXISTS `replenishment_orders`;
CREATE TABLE IF NOT EXISTS `replenishment_orders` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('PENDIENTE','PEDIDO','RECIBIDO') NOT NULL DEFAULT 'PENDIENTE',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `replenishment_orders`
--

INSERT INTO `replenishment_orders` (`id`, `order_date`, `status`) VALUES
(14, '2025-08-07 15:10:53', 'PENDIENTE');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `replenishment_order_items`
--

DROP TABLE IF EXISTS `replenishment_order_items`;
CREATE TABLE IF NOT EXISTS `replenishment_order_items` (
  `id` int NOT NULL AUTO_INCREMENT,
  `order_id` int NOT NULL,
  `stock_id` int NOT NULL,
  `quantity_requested` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `order_id` (`order_id`)
) ENGINE=MyISAM AUTO_INCREMENT=137 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `replenishment_order_items`
--

INSERT INTO `replenishment_order_items` (`id`, `order_id`, `stock_id`, `quantity_requested`) VALUES
(136, 14, 70, 3),
(135, 14, 79, 4),
(134, 14, 68, 4),
(133, 14, 71, 6),
(132, 14, 74, 3),
(131, 14, 76, 2),
(130, 14, 77, 2),
(129, 14, 88, 2),
(128, 14, 84, 4),
(127, 13, 70, 5),
(126, 13, 79, 5),
(125, 13, 68, 5),
(124, 13, 71, 5),
(123, 13, 74, 5),
(122, 13, 76, 5),
(121, 13, 82, 5),
(120, 13, 77, 5),
(119, 13, 88, 5),
(118, 13, 84, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock`
--

DROP TABLE IF EXISTS `stock`;
CREATE TABLE IF NOT EXISTS `stock` (
  `id` int NOT NULL AUTO_INCREMENT,
  `brand_id` int DEFAULT NULL,
  `consumable_model_id` int DEFAULT NULL,
  `supplier_id` int DEFAULT NULL,
  `type` enum('Tóner','Tinta') DEFAULT NULL,
  `color` varchar(50) DEFAULT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `printer_brand_id` int DEFAULT NULL,
  `printer_model_id` int DEFAULT NULL,
  `date_added` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `fk_brand` (`brand_id`),
  KEY `fk_supplier` (`supplier_id`),
  KEY `fk_printer_brand` (`printer_brand_id`),
  KEY `fk_printer_model` (`printer_model_id`),
  KEY `fk_consumable_model` (`consumable_model_id`)
) ENGINE=MyISAM AUTO_INCREMENT=147 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `stock`
--

INSERT INTO `stock` (`id`, `brand_id`, `consumable_model_id`, `supplier_id`, `type`, `color`, `cost`, `printer_brand_id`, `printer_model_id`, `date_added`, `is_active`) VALUES
(76, 8, 9, 4, 'Tinta', 'Black', 0.00, 13, 20, '2025-08-07 10:07:53', 1),
(75, 7, 8, 4, 'Tinta', 'Black', 0.00, 13, 21, '2025-08-07 10:06:39', 1),
(74, 8, 8, 4, 'Tinta', 'Color', 0.00, 13, 21, '2025-08-07 10:05:51', 1),
(73, 8, 8, 4, 'Tinta', 'Black', 0.00, 13, 21, '2025-08-07 10:05:51', 1),
(72, 8, 7, 4, 'Tinta', 'Black', 0.00, 13, 22, '2025-08-07 10:04:26', 0),
(71, 6, 7, 4, 'Tinta', 'Color', 0.00, 13, 22, '2025-08-07 10:03:42', 1),
(70, 7, 6, 5, 'Tinta', 'Color', 110000.00, 13, 19, '2025-08-07 10:01:17', 1),
(69, 6, 6, 4, 'Tinta', 'Color', 0.00, 13, 19, '2025-08-07 10:00:43', 1),
(68, 6, 6, 4, 'Tinta', 'Black', 0.00, 13, 19, '2025-08-07 10:00:43', 1),
(77, 8, 9, 4, 'Tinta', 'Color', 0.00, 13, 20, '2025-08-07 10:07:53', 1),
(78, 7, 9, 5, 'Tinta', 'Black', 87100.00, 13, 20, '2025-08-07 10:08:20', 1),
(79, 9, 10, 5, 'Tóner', 'Monocromatico', 56000.00, 13, 17, '2025-08-07 10:09:51', 1),
(80, 10, 10, 4, 'Tóner', 'Monocromatico', 0.00, 13, 17, '2025-08-07 10:10:23', 1),
(81, 10, 11, 4, 'Tóner', 'Monocromatico', 0.00, 13, 16, '2025-08-07 10:11:56', 1),
(82, 10, 13, 4, 'Tóner', 'Monocromatico', 0.00, 13, 15, '2025-08-07 10:12:44', 1),
(83, 9, 13, 4, 'Tóner', 'Monocromatico', 0.00, 11, 13, '2025-08-07 10:13:32', 0),
(84, 10, 14, 5, 'Tóner', 'Monocromatico', 70000.00, 13, 18, '2025-08-07 10:14:17', 1),
(85, 11, 7, 5, 'Tinta', 'Black', 0.00, 13, 22, '2025-08-07 10:20:28', 1),
(86, 9, 11, 4, 'Tóner', 'Monocromatico', 0.00, 13, 16, '2025-08-07 10:41:04', 1),
(87, 9, 13, 4, 'Tóner', 'Monocromatico', 0.00, 11, 13, '2025-08-07 10:41:43', 0),
(88, 9, 13, 5, 'Tóner', 'Monocromatico', 47000.00, 13, 15, '2025-08-07 12:21:40', 1),
(89, 8, 7, 4, 'Tinta', 'Black', 0.00, 13, 22, '2025-08-08 10:23:20', 1),
(90, 7, 7, 5, 'Tinta', 'Black', 88000.00, 13, 22, '2025-08-08 18:46:27', 0),
(91, 7, 7, 5, 'Tinta', 'Black', 88000.00, 13, 22, '2025-08-08 18:58:01', 1),
(92, 7, 7, 5, 'Tinta', 'Color', 0.00, 13, 22, '2025-08-08 18:58:01', 1),
(93, 7, 7, 5, 'Tinta', 'Monocromatico', 0.00, 13, 22, '2025-08-08 18:58:01', 1),
(94, 7, 7, 5, 'Tinta', 'Cyan', 0.00, 13, 22, '2025-08-08 18:58:01', 1),
(95, 7, 7, 5, 'Tinta', 'Magenta', 0.00, 13, 22, '2025-08-08 18:58:01', 1),
(96, 7, 7, 5, 'Tinta', 'Yellow', 0.00, 13, 22, '2025-08-08 18:58:01', 1),
(97, 7, 7, 5, 'Tinta', 'Black', 0.00, 13, 22, '2025-08-08 18:58:01', 1),
(98, 7, 9, 5, 'Tinta', 'Black', 0.00, 13, 20, '2025-08-08 19:05:30', 1),
(99, 7, 9, 5, 'Tinta', 'Color', 92000.00, 13, 20, '2025-08-08 19:05:30', 1),
(100, 7, 9, 5, 'Tinta', 'Monocromatico', 0.00, 13, 20, '2025-08-08 19:05:30', 1),
(101, 7, 9, 5, 'Tinta', 'Cyan', 0.00, 13, 20, '2025-08-08 19:05:30', 1),
(102, 7, 9, 5, 'Tinta', 'Magenta', 0.00, 13, 20, '2025-08-08 19:05:30', 1),
(103, 7, 9, 5, 'Tinta', 'Yellow', 0.00, 13, 20, '2025-08-08 19:05:30', 1),
(104, 7, 9, 5, 'Tinta', 'Black', 0.00, 13, 20, '2025-08-08 19:05:30', 1),
(105, 7, 8, 5, 'Tinta', 'Black', 0.00, 13, 21, '2025-08-08 19:07:45', 1),
(106, 7, 8, 5, 'Tinta', 'Color', 99000.00, 13, 21, '2025-08-08 19:07:45', 1),
(107, 7, 8, 5, 'Tinta', 'Monocromatico', 0.00, 13, 21, '2025-08-08 19:07:45', 1),
(108, 7, 8, 5, 'Tinta', 'Cyan', 0.00, 13, 21, '2025-08-08 19:07:45', 1),
(109, 7, 8, 5, 'Tinta', 'Magenta', 0.00, 13, 21, '2025-08-08 19:07:45', 1),
(110, 7, 8, 5, 'Tinta', 'Yellow', 0.00, 13, 21, '2025-08-08 19:07:45', 1),
(111, 7, 8, 5, 'Tinta', 'Black', 0.00, 13, 21, '2025-08-08 19:07:45', 1),
(112, 7, 6, 5, 'Tinta', 'Black', 103000.00, 13, 19, '2025-08-08 19:09:48', 1),
(113, 7, 6, 5, 'Tinta', 'Color', 0.00, 13, 19, '2025-08-08 19:09:48', 1),
(114, 7, 6, 5, 'Tinta', 'Monocromatico', 0.00, 13, 19, '2025-08-08 19:09:48', 1),
(115, 7, 6, 5, 'Tinta', 'Cyan', 0.00, 13, 19, '2025-08-08 19:09:48', 1),
(116, 7, 6, 5, 'Tinta', 'Magenta', 0.00, 13, 19, '2025-08-08 19:09:48', 1),
(117, 7, 6, 5, 'Tinta', 'Yellow', 0.00, 13, 19, '2025-08-08 19:09:48', 1),
(118, 7, 6, 5, 'Tinta', 'Black', 0.00, 13, 19, '2025-08-08 19:09:48', 1),
(119, 7, 10, 5, 'Tóner', 'Black', 0.00, 13, 17, '2025-08-08 19:11:39', 1),
(120, 7, 10, 5, 'Tóner', 'Color', 0.00, 13, 17, '2025-08-08 19:11:39', 1),
(121, 7, 10, 5, 'Tóner', 'Monocromatico', 0.00, 13, 17, '2025-08-08 19:11:39', 1),
(122, 7, 10, 5, 'Tóner', 'Cyan', 0.00, 13, 17, '2025-08-08 19:11:39', 1),
(123, 7, 10, 5, 'Tóner', 'Magenta', 0.00, 13, 17, '2025-08-08 19:11:39', 1),
(124, 7, 10, 5, 'Tóner', 'Yellow', 0.00, 13, 17, '2025-08-08 19:11:39', 1),
(125, 7, 10, 5, 'Tóner', 'Black', 0.00, 13, 17, '2025-08-08 19:11:39', 1),
(126, 9, 14, 5, 'Tóner', 'Black', 0.00, 13, 18, '2025-08-08 19:15:12', 1),
(127, 9, 14, 5, 'Tóner', 'Color', 0.00, 13, 18, '2025-08-08 19:15:12', 1),
(128, 9, 14, 5, 'Tóner', 'Monocromatico', 0.00, 13, 18, '2025-08-08 19:15:12', 1),
(129, 9, 14, 5, 'Tóner', 'Cyan', 0.00, 13, 18, '2025-08-08 19:15:12', 1),
(130, 9, 14, 5, 'Tóner', 'Magenta', 0.00, 13, 18, '2025-08-08 19:15:12', 1),
(131, 9, 14, 5, 'Tóner', 'Yellow', 0.00, 13, 18, '2025-08-08 19:15:12', 1),
(132, 9, 14, 5, 'Tóner', 'Black', 0.00, 13, 18, '2025-08-08 19:15:12', 1),
(133, 9, 13, 5, 'Tóner', 'Black', 0.00, 13, 15, '2025-08-08 19:16:21', 1),
(134, 9, 13, 5, 'Tóner', 'Color', 0.00, 13, 15, '2025-08-08 19:16:21', 1),
(135, 9, 13, 5, 'Tóner', 'Monocromatico', 0.00, 13, 15, '2025-08-08 19:16:21', 1),
(136, 9, 13, 5, 'Tóner', 'Cyan', 0.00, 13, 15, '2025-08-08 19:16:21', 1),
(137, 9, 13, 5, 'Tóner', 'Magenta', 0.00, 13, 15, '2025-08-08 19:16:21', 1),
(138, 9, 13, 5, 'Tóner', 'Yellow', 0.00, 13, 15, '2025-08-08 19:16:21', 1),
(139, 9, 13, 5, 'Tóner', 'Black', 0.00, 13, 15, '2025-08-08 19:16:21', 1),
(140, 12, 15, 4, '', 'Black', 150000.00, 14, 23, '2025-08-11 13:30:27', 1),
(141, 12, 15, 4, '', 'Color', 150000.00, 14, 23, '2025-08-11 13:30:27', 1),
(142, 12, 15, 4, '', 'Monocromatico', 150000.00, 14, 23, '2025-08-11 13:30:27', 0),
(143, 12, 15, 4, '', 'Cyan', 150000.00, 14, 23, '2025-08-11 13:30:27', 1),
(144, 12, 15, 4, '', 'Magenta', 150000.00, 14, 23, '2025-08-11 13:30:27', 1),
(145, 12, 15, 4, '', 'Yellow', 150000.00, 14, 23, '2025-08-11 13:30:27', 1),
(146, 12, 15, 4, '', 'Black', 150000.00, 14, 23, '2025-08-11 13:30:27', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_departments`
--

DROP TABLE IF EXISTS `stock_departments`;
CREATE TABLE IF NOT EXISTS `stock_departments` (
  `stock_id` int NOT NULL,
  `department_id` int NOT NULL,
  PRIMARY KEY (`stock_id`,`department_id`),
  KEY `fk_department_id` (`department_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `stock_departments`
--

INSERT INTO `stock_departments` (`stock_id`, `department_id`) VALUES
(68, 1),
(68, 4),
(69, 1),
(69, 4),
(70, 1),
(70, 4),
(71, 3),
(72, 3),
(73, 6),
(73, 7),
(74, 6),
(74, 7),
(75, 6),
(75, 7),
(76, 8),
(77, 8),
(78, 8),
(79, 3),
(80, 3),
(81, 5),
(82, 2),
(83, 2),
(84, 1),
(85, 3),
(86, 5),
(87, 2),
(88, 2),
(89, 3),
(90, 3),
(91, 3),
(92, 3),
(93, 3),
(94, 3),
(95, 3),
(96, 3),
(97, 3),
(98, 8),
(99, 8),
(100, 8),
(101, 8),
(102, 8),
(103, 8),
(104, 8),
(105, 6),
(105, 7),
(106, 6),
(106, 7),
(107, 6),
(107, 7),
(108, 6),
(108, 7),
(109, 6),
(109, 7),
(110, 6),
(110, 7),
(111, 6),
(111, 7),
(112, 1),
(112, 4),
(113, 1),
(113, 4),
(114, 1),
(114, 4),
(115, 1),
(115, 4),
(116, 1),
(116, 4),
(117, 1),
(117, 4),
(118, 1),
(118, 4),
(119, 3),
(120, 3),
(121, 3),
(122, 3),
(123, 3),
(124, 3),
(125, 3),
(126, 1),
(127, 1),
(128, 1),
(129, 1),
(130, 1),
(131, 1),
(132, 1),
(133, 2),
(134, 2),
(135, 2),
(136, 2),
(137, 2),
(138, 2),
(139, 2),
(140, 5),
(141, 5),
(142, 5),
(143, 5),
(144, 5),
(145, 5),
(146, 5);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_movements`
--

DROP TABLE IF EXISTS `stock_movements`;
CREATE TABLE IF NOT EXISTS `stock_movements` (
  `id` int NOT NULL AUTO_INCREMENT,
  `stock_id` int NOT NULL,
  `quantity_change` int NOT NULL,
  `movement_type` enum('INITIAL_STOCK','REPLENISHMENT','SALE','RETURN_DEFECTIVE','ADJUSTMENT_ADD','ADJUSTMENT_REMOVE','SALE_REVERSAL','DEACTIVATED') NOT NULL,
  `reason` text,
  `recipient_id` int DEFAULT NULL,
  `department_id` int DEFAULT NULL,
  `replenishment_order_item_id` int DEFAULT NULL,
  `reverses_movement_id` int DEFAULT NULL,
  `movement_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `stock_id` (`stock_id`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `stock_id`, `quantity_change`, `movement_type`, `reason`, `recipient_id`, `department_id`, `replenishment_order_item_id`, `reverses_movement_id`, `movement_date`) VALUES
(65, 68, 4, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:00:43'),
(66, 69, 6, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:00:43'),
(67, 70, 1, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:01:17'),
(69, 71, 4, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:03:42'),
(70, 72, 8, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:04:26'),
(71, 73, 6, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:05:51'),
(72, 74, 3, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:05:51'),
(73, 75, 2, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:06:39'),
(74, 76, 2, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:07:53'),
(75, 77, 2, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:07:53'),
(76, 78, 1, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:08:20'),
(77, 79, 3, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:09:51'),
(78, 80, 1, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:10:23'),
(79, 81, 7, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:11:56'),
(80, 82, 3, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:12:44'),
(81, 83, 1, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:13:32'),
(82, 84, 1, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:14:17'),
(83, 85, 2, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:20:28'),
(85, 86, 1, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:41:04'),
(86, 87, 1, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 10:41:43'),
(87, 70, -1, 'SALE', 'Entregado a: Marcelo Sosa (Dpto: sgc minerva)', NULL, NULL, NULL, NULL, '2025-08-07 10:50:09'),
(88, 70, -1, 'SALE_REVERSAL', 'Motivo de corrección: no era la persona. (Anula la salida registrada el 2025-08-07 06:50:09)', NULL, NULL, NULL, 87, '2025-08-07 10:51:18'),
(89, 83, -1, 'SALE_REVERSAL', 'Anulación de creación. Motivo: Se ingreso  doble', NULL, NULL, NULL, 81, '2025-08-07 12:19:47'),
(90, 87, -1, 'SALE_REVERSAL', 'Anulación de creación. Motivo: se ingreso doble', NULL, NULL, NULL, 86, '2025-08-07 12:21:08'),
(91, 88, 2, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-07 12:21:40'),
(92, 70, 1, 'REPLENISHMENT', 'Compra a proveedor: Magma. Referencia: repocision', NULL, NULL, NULL, NULL, '2025-08-07 10:17:47'),
(93, 70, 1, 'REPLENISHMENT', 'Compra a proveedor: Magma. Referencia: repocision', NULL, NULL, NULL, NULL, '2025-08-07 10:18:32'),
(94, 70, -1, 'SALE', 'Entregado a: Diana (Dpto: Produccion)', NULL, NULL, NULL, NULL, '2025-08-08 10:19:40'),
(95, 68, -1, 'SALE', 'Entregado a: Diana (Dpto: Produccion)', NULL, NULL, NULL, NULL, '2025-08-08 10:20:55'),
(96, 72, -8, 'SALE_REVERSAL', 'Anulación de creación. Motivo: Se cargo mal la cantidad', NULL, NULL, NULL, 70, '2025-08-08 10:22:27'),
(97, 89, 7, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-08 10:23:20'),
(98, 84, -1, 'SALE', 'Entregado a: Marcelo Sosa (Dpto: Informatica)', NULL, NULL, NULL, NULL, '2025-08-08 11:22:51'),
(99, 84, 1, 'SALE_REVERSAL', 'Motivo de corrección: transacción de salida de producto por error. (Anula la salida registrada el 2025-08-08 07:22:51)', NULL, NULL, NULL, 98, '2025-08-08 11:24:39'),
(100, 90, 6, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-08 18:46:27'),
(101, 78, 2, 'REPLENISHMENT', 'Compra a proveedor: INK NDM S.R.L.. Referencia: Reposicion', NULL, NULL, NULL, NULL, '2025-08-08 18:48:27'),
(102, 90, -6, 'SALE_REVERSAL', 'Anulación de creación. Motivo: se registro erroneamente', NULL, NULL, NULL, 100, '2025-08-08 18:52:00'),
(103, 91, 6, 'REPLENISHMENT', 'Compra a proveedor: INK NDM S.R.L.. Referencia: Reposicion', NULL, NULL, NULL, NULL, '2025-08-08 18:59:09'),
(104, 99, 2, 'REPLENISHMENT', 'Compra a proveedor: INK NDM S.R.L.. Referencia: Reposicion', NULL, NULL, NULL, NULL, '2025-08-08 19:06:18'),
(105, 106, 3, 'REPLENISHMENT', 'Compra a proveedor: INK NDM S.R.L.. Referencia: Reposicion', NULL, NULL, NULL, NULL, '2025-08-08 19:08:24'),
(106, 70, 3, 'REPLENISHMENT', 'Compra a proveedor: INK NDM S.R.L.. Referencia: Reposicion', NULL, NULL, NULL, NULL, '2025-08-08 19:08:58'),
(107, 112, 4, 'REPLENISHMENT', 'Compra a proveedor: INK NDM S.R.L.. Referencia: Reposicion', NULL, NULL, NULL, NULL, '2025-08-08 19:10:18'),
(108, 79, 4, 'REPLENISHMENT', 'Compra a proveedor: INK NDM S.R.L.. Referencia: Reposicion', NULL, NULL, NULL, NULL, '2025-08-08 19:12:20'),
(109, 84, 4, 'REPLENISHMENT', 'Compra a proveedor: INK NDM S.R.L.. Referencia: Reposicion', NULL, NULL, NULL, NULL, '2025-08-08 19:15:33'),
(110, 88, 2, 'REPLENISHMENT', 'Compra a proveedor: INK NDM S.R.L.. Referencia: Reposicion', NULL, NULL, NULL, NULL, '2025-08-08 19:16:47'),
(111, 76, -1, 'SALE', 'Entregado a: Marcelo Sosa (Dpto: Recursos Humanos)', 1, 1, NULL, NULL, '2025-08-08 22:03:58'),
(112, 76, 1, 'SALE_REVERSAL', 'Motivo de corrección: Salida para prueba. (Anula la salida registrada el 2025-08-08 18:03:58)', NULL, NULL, NULL, 111, '2025-08-08 22:04:33'),
(113, 142, 4, 'INITIAL_STOCK', 'Creación de nuevo producto', NULL, NULL, NULL, NULL, '2025-08-11 13:30:27'),
(114, 142, -4, 'SALE_REVERSAL', 'Anulación de creación. Motivo: Se ingreso como prueba de testing de aplicación', NULL, NULL, NULL, 113, '2025-08-11 13:31:31'),
(115, 82, -1, 'SALE', 'Entregado a: Marcelo Sosa (Dpto: Administracion)', 1, 2, NULL, NULL, '2025-08-11 18:53:08'),
(116, 71, -1, 'SALE', 'Entregado a: Reinaldo (Dpto: Pre Prensa)', 3, 3, NULL, NULL, '2025-08-11 20:38:15'),
(117, 89, -1, 'SALE', 'Entregado a: Reinaldo (Dpto: Pre Prensa)', 3, 3, NULL, NULL, '2025-08-11 20:38:34'),
(118, 68, 1, 'SALE_REVERSAL', 'Motivo de corrección: se cargo mal. (Anula la salida registrada el 2025-08-08 06:20:55)', NULL, NULL, NULL, 95, '2025-08-12 14:18:25'),
(119, 70, 1, 'SALE_REVERSAL', 'Motivo de corrección: se cargo mal. (Anula la salida registrada el 2025-08-08 06:19:40)', NULL, NULL, NULL, 94, '2025-08-12 14:18:37'),
(120, 68, -1, 'SALE', 'Entregado a: Marcelo Sosa (Dpto: Recursos Humanos)', 1, 1, NULL, NULL, '2025-08-12 14:19:11'),
(121, 70, -1, 'SALE', 'Entregado a: Diana (Dpto: O.T.)', 2, 4, NULL, NULL, '2025-08-12 14:19:34');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_out`
--

DROP TABLE IF EXISTS `stock_out`;
CREATE TABLE IF NOT EXISTS `stock_out` (
  `id` int NOT NULL AUTO_INCREMENT,
  `item_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `recipient` int DEFAULT NULL,
  `department` int DEFAULT NULL,
  `date_issued` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `item_id` (`item_id`),
  KEY `fk_recipient` (`recipient`),
  KEY `fk_department` (`department`)
) ENGINE=MyISAM AUTO_INCREMENT=23 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `stock_out_errors`
--

DROP TABLE IF EXISTS `stock_out_errors`;
CREATE TABLE IF NOT EXISTS `stock_out_errors` (
  `id` int NOT NULL,
  `item_id` int DEFAULT NULL,
  `quantity` int DEFAULT NULL,
  `recipient` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `department` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `date_issued` timestamp NULL DEFAULT NULL,
  `reason` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'Anulación manual',
  `date_deleted` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `suppliers`
--

DROP TABLE IF EXISTS `suppliers`;
CREATE TABLE IF NOT EXISTS `suppliers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`) VALUES
(4, 'Magma'),
(5, 'INK NDM S.R.L.');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'admin'),
(2, 'Marcelo Sosa', '0192023a7bbd73250516f069df18b500', 'user'),
(3, 'Gestor', '0192023a7bbd73250516f069df18b500', 'user'),
(4, 'Operador', '0192023a7bbd73250516f069df18b500', 'user');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
