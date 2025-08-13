-- Script para limpiar base de datos: eliminar tablas no utilizadas y sistema de roles
-- Ejecutar este script en phpMyAdmin o cliente MySQL

-- 1. Eliminar tablas no utilizadas
DROP TABLE IF EXISTS `stock_out`;
DROP TABLE IF EXISTS `stock_out_errors`;

-- 2. Si quieres mantener la tabla users pero simplificar (remover roles):
-- ALTER TABLE `users` DROP COLUMN `role`;

-- 3. O si quieres eliminar completamente la tabla users (recomendado si no se usa):
DROP TABLE IF EXISTS `users`;

-- Nota: El sistema ahora funcionará sin autenticación de usuarios
-- Los datos de stock, movimientos, proveedores, etc. se mantendrán intactos

SELECT 'Limpieza completada. Tablas eliminadas: stock_out, stock_out_errors, users' as mensaje;
