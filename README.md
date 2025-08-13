# 📦 Sistema de Gestión de Stock

Un sistema completo de gestión de inventario desarrollado en PHP con MySQL, diseñado para el control eficiente de productos, costos y movimientos de stock.

## 🌟 Características Principales

### 📊 **Dashboard Completo**
- Vista general del inventario en tiempo real
- Métricas clave: productos totales, valor del inventario, productos con bajo stock
- Gráficos interactivos de análisis de costos
- Alertas automáticas de stock mínimo

### 🔍 **Gestión de Productos**
- ✅ CRUD completo de productos (Crear, Leer, Actualizar, Eliminar)
- ✅ Gestión de marcas, modelos y proveedores
- ✅ Control de tipos de productos (Tóner, Cartuchos, etc.)
- ✅ Compatibilidad con impresoras
- ✅ Asignación por departamentos

### 💰 **Análisis de Costos**
- 📈 Dashboard de análisis financiero
- 💵 Edición en línea de costos unitarios
- 📋 Lista personalizable de productos y precios
- 🔄 Actualización en tiempo real de valores totales

### 📋 **Sistema de Reportes Avanzado**
- **🎯 Exportación Personalizada**: Selecciona qué columnas incluir en tus reportes
- **📑 PDF Profesional**: Reportes en formato PDF con diseño corporativo
- **📊 Excel Avanzado**: Exportación con formato HTML y estadísticas
- **🔍 Filtros Inteligentes**: 8 criterios de filtrado diferentes
- **🔗 URLs Compartibles**: Guarda y comparte vistas filtradas

### 📈 **Gestión de Movimientos**
- 📥 Entradas de stock con control de proveedores
- 📤 Salidas de stock por departamento
- 🔄 Devoluciones y productos defectuosos
- 📋 Historial completo de movimientos
- 🧾 Órdenes de reposición automáticas

### 🎨 **Interfaz Moderna**
- 📱 **100% Responsive**: Funciona perfectamente en móviles, tablets y desktop
- 🎨 **Bootstrap 5**: Diseño moderno y profesional
- ⚡ **DataTables**: Tablas interactivas con búsqueda y paginación
- 🌈 **FontAwesome**: Iconos vectoriales para mejor UX

## 🛠️ Tecnologías Utilizadas

### **Backend**
- **PHP 7.4+**: Lenguaje principal del servidor
- **MySQL 5.7+**: Base de datos relacional
- **PDO**: Capa de abstracción de base de datos
- **DomPDF**: Generación de documentos PDF

### **Frontend**
- **HTML5 & CSS3**: Estructura y estilos modernos
- **Bootstrap 5.3**: Framework CSS responsive
- **jQuery 3.7.1**: Manipulación del DOM y AJAX
- **DataTables 1.13.4**: Tablas interactivas avanzadas
- **FontAwesome 6**: Biblioteca de iconos vectoriales

### **Arquitectura**
- **MVC Pattern**: Modelo-Vista-Controlador
- **RESTful Routes**: URLs amigables y organizadas
- **AJAX**: Actualizaciones sin recarga de página
- **Responsive Design**: Adaptable a todos los dispositivos

## 📋 Requisitos del Sistema

- **PHP**: Versión 7.4 o superior
- **MySQL**: Versión 5.7 o superior
- **Apache/Nginx**: Servidor web con mod_rewrite
- **Extensiones PHP requeridas**:
  - PDO MySQL
  - mbstring
  - zip
  - dom

## 🚀 Instalación

### 1. **Clonar el Repositorio**
```bash
git clone https://github.com/tu-usuario/stock-management-system.git
cd stock-management-system
```

### 2. **Configurar la Base de Datos**
```sql
-- Crear la base de datos
CREATE DATABASE stock_management;

-- Importar la estructura
mysql -u tu_usuario -p stock_management < database.sql
```

### 3. **Configurar la Conexión**
Edita el archivo `config/Database.php`:
```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'stock_management');
define('DB_USER', 'tu_usuario');
define('DB_PASS', 'tu_contraseña');
```

### 4. **Instalar Dependencias**
```bash
composer install
```

### 5. **Configurar el Servidor Web**
Apunta tu servidor web a la carpeta del proyecto y asegúrate de que el mod_rewrite esté habilitado.

## 📁 Estructura del Proyecto

```
stock-app/
├── 📁 config/              # Configuración de la aplicación
│   └── Database.php        # Configuración de base de datos
├── 📁 controllers/         # Controladores MVC
│   ├── BaseController.php
│   ├── StockController.php
│   ├── CostAnalysisController.php
│   ├── HistoryController.php
│   └── ReportController.php
├── 📁 models/              # Modelos de datos
│   ├── Stock.php
│   ├── Brand.php
│   ├── Department.php
│   ├── History.php
│   └── Report.php
├── 📁 views/               # Vistas y templates
│   ├── templates/          # Plantillas base
│   ├── stock/              # Vistas de gestión de stock
│   ├── cost_analysis/      # Vistas de análisis de costos
│   ├── history/            # Vistas de historial
│   └── reports/            # Vistas de reportes
├── 📁 vendor/              # Dependencias de Composer
├── 📄 composer.json        # Configuración de Composer
├── 📄 database.sql         # Estructura de base de datos
└── 📄 index.php            # Punto de entrada principal
```

## 🎯 Funcionalidades Destacadas

### **🔧 Edición en Línea**
- Modifica costos unitarios directamente desde la tabla
- Actualización automática de valores totales
- Validación en tiempo real
- Feedback visual inmediato

### **📊 Filtros Avanzados**
- **Por Marca**: Filtra productos por marca específica
- **Por Tipo**: Tóner, Cartucho, etc.
- **Por Color**: Negro, Cyan, Magenta, Yellow
- **Por Proveedor**: Filtra por proveedor específico
- **Por Stock**: Bajo, Normal, Alto
- **Por Rango de Costo**: Rangos personalizables
- **Por Departamento**: Filtra por departamento asignado
- **Búsqueda General**: Busca en todas las columnas

### **📋 Exportación Personalizada**
- **Selección de Columnas**: Elige exactamente qué información exportar
- **Formatos Múltiples**: Excel y PDF con diseño profesional
- **Filtros Aplicados**: Solo exporta los datos filtrados visibles
- **Timestamps**: Nombres de archivo con fecha y hora automática

## 🔐 Seguridad

- **Preparated Statements**: Protección contra inyección SQL
- **Validación de Datos**: Sanitización en frontend y backend
- **Control de Errores**: Manejo elegante de excepciones
- **Logs de Auditoria**: Registro de operaciones críticas

## 📈 Métricas del Sistema

- **🏗️ Líneas de Código**: ~3,000+
- **📝 Archivos PHP**: 15+
- **🎨 Vistas**: 20+
- **📊 Tablas BD**: 8+
- **⚡ Funciones JS**: 25+

## 🤝 Contribuciones

Las contribuciones son bienvenidas. Por favor:

1. Fork el proyecto
2. Crea una rama para tu feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📄 Licencia

Este proyecto está bajo la Licencia MIT. Ver el archivo `LICENSE` para más detalles.

## 📧 Contacto

**Desarrollador**: mjsosapy  
**GitHub**: [@mjsosapy](https://github.com/mjsosapy)  
**Proyecto**: [https://github.com/mjsosapy/stock-management-system](https://github.com/mjsosapy/stock-management-system)

## 🙏 Agradecimientos

- Bootstrap Team por el excelente framework CSS
- DataTables por las tablas interactivas
- DomPDF por la generación de PDFs
- FontAwesome por los iconos vectoriales

---

⭐ **¡Si este proyecto te ha sido útil, considera darle una estrella!** ⭐
