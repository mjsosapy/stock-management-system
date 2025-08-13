# Historial de Cambios

Todas las mejoras y cambios importantes del proyecto se documentan en este archivo.

El formato se basa en [Keep a Changelog](https://keepachangelog.com/es-ES/1.0.0/),
y este proyecto adhiere al [Versionado Semántico](https://semver.org/lang/es/).

## [2.1.0] - 2025-01-13

### ✨ Agregado
- **🎯 Exportación Personalizada**: Nueva funcionalidad para seleccionar columnas específicas en reportes
- **📋 Modal de Configuración**: Interfaz intuitiva para configurar exportaciones
- **📊 Formato Dual**: Soporte para exportación en Excel y PDF personalizado
- **🔍 Filtros Compartibles**: URLs para compartir vistas filtradas específicas
- **📱 Responsive Modal**: Modal completamente adaptable a dispositivos móviles

### 🔧 Mejorado
- **⚡ Performance**: Optimización en consultas de base de datos
- **🎨 UI/UX**: Mejoras significativas en la interfaz de usuario
- **📈 DataTables**: Actualización y mejoras en la funcionalidad de tablas
- **🔄 AJAX**: Mejoras en las actualizaciones en tiempo real

### 🐛 Corregido
- Corrección en el manejo de caracteres especiales en exportaciones
- Mejoras en la validación de datos de entrada
- Corrección de errores en filtros combinados

## [2.0.0] - 2025-01-12

### ✨ Agregado
- **💰 Edición en Línea de Costos**: Permite modificar precios directamente en la tabla
- **📊 Dashboard de Métricas**: 5 cards con estadísticas clave del inventario
- **🎯 Filtros Avanzados**: Sistema de 8 filtros diferentes para productos
- **📋 Exportación Excel**: Exportación avanzada con formato HTML
- **📑 Exportación PDF**: Generación de PDFs profesionales con dompdf

### 🔧 Mejorado
- **🎨 Interfaz Moderna**: Rediseño completo con Bootstrap 5
- **📱 Responsividad**: Optimización para dispositivos móviles
- **⚡ Carga Rápida**: Optimización de consultas y carga de assets
- **🔍 Búsqueda**: Mejoras en la funcionalidad de búsqueda global

### 🗑️ Eliminado
- Terminología obsoleta ("Monocromatico" y "Black" reemplazados por "Negro")
- Código legacy innecesario

## [1.5.0] - 2025-01-10

### ✨ Agregado
- **📈 Análisis de Costos**: Nuevo módulo completo de análisis financiero
- **📊 Gráficos Interactivos**: Visualizaciones de datos con Chart.js
- **🏢 Gestión por Departamentos**: Asignación y control por departamentos
- **📋 Historial Completo**: Seguimiento detallado de todos los movimientos

### 🔧 Mejorado
- **🔐 Seguridad**: Implementación de prepared statements
- **📝 Validaciones**: Mejoras en validación de datos frontend y backend
- **🎨 Estilos**: Implementación de tema consistente en toda la aplicación

## [1.0.0] - 2025-01-05

### ✨ Agregado - Lanzamiento Inicial
- **📦 Gestión de Stock**: CRUD completo para productos
- **🏭 Marcas y Modelos**: Sistema de categorización de productos
- **🚚 Proveedores**: Gestión de proveedores y relaciones
- **📊 Reportes Básicos**: Generación de reportes de stock
- **🔄 Movimientos**: Sistema de entradas y salidas de inventario

### 🛠️ Técnico
- **🏗️ Arquitectura MVC**: Implementación del patrón Modelo-Vista-Controlador
- **💾 Base de Datos**: Diseño e implementación del esquema MySQL
- **🎨 Bootstrap**: Implementación de framework CSS responsive
- **📱 jQuery**: Interactividad y AJAX para mejor UX

---

## Leyenda de Tipos de Cambio

- ✨ **Agregado**: Nuevas funcionalidades
- 🔧 **Mejorado**: Mejoras en funcionalidades existentes
- 🐛 **Corregido**: Corrección de errores
- 🗑️ **Eliminado**: Funcionalidades removidas
- 🛠️ **Técnico**: Cambios técnicos internos
- 🔐 **Seguridad**: Mejoras relacionadas con seguridad
