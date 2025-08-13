# Configuración del Proyecto

Este archivo contiene información importante sobre la configuración y estructura del proyecto.

## 🗂️ Estructura de Archivos

```
stock-app/
├── 📁 config/
│   └── Database.php          # Configuración de conexión a BD
├── 📁 controllers/
│   ├── BaseController.php    # Controlador base con funcionalidades comunes
│   ├── StockController.php   # Gestión de inventario
│   ├── CostAnalysisController.php # Análisis de costos y precios
│   ├── HistoryController.php # Historial de movimientos
│   └── ReportController.php  # Generación de reportes
├── 📁 models/
│   ├── Stock.php            # Modelo principal de productos
│   ├── Brand.php            # Gestión de marcas
│   ├── Department.php       # Departamentos organizacionales
│   ├── History.php          # Registro de movimientos
│   ├── Supplier.php         # Proveedores
│   └── Report.php           # Reportes y estadísticas
├── 📁 views/
│   ├── 📁 templates/        # Plantillas base (header, footer)
│   ├── 📁 stock/           # Vistas de gestión de stock
│   ├── 📁 cost_analysis/   # Vistas de análisis de costos
│   ├── 📁 history/         # Vistas de historial
│   └── 📁 reports/         # Vistas de reportes
└── 📁 vendor/              # Dependencias de Composer
```

## ⚙️ Variables de Configuración

### Database.php
```php
define('DB_HOST', 'localhost');     # Servidor de base de datos
define('DB_NAME', 'stock_db');      # Nombre de la base de datos
define('DB_USER', 'usuario');       # Usuario de la base de datos
define('DB_PASS', 'contraseña');    # Contraseña de la base de datos
define('BASE_URL', 'http://localhost/stock-app/'); # URL base del proyecto
```

## 🎨 Dependencias Frontend

### CSS Frameworks
- **Bootstrap 5.3.0**: Framework CSS principal
- **DataTables 1.13.4**: Tablas interactivas
- **FontAwesome 6.0**: Iconos vectoriales

### JavaScript Libraries
- **jQuery 3.7.1**: Manipulación DOM y AJAX
- **DataTables JS**: Funcionalidad de tablas
- **Chart.js**: Gráficos (futuro uso)

## 📊 Esquema de Base de Datos

### Tablas Principales

#### `stock`
- `id` (PRIMARY KEY)
- `brand_id` (FK)
- `model_name`
- `color`
- `type`
- `cost`
- `supplier_id` (FK)
- `is_active`
- `created_at`
- `updated_at`

#### `brands`
- `id` (PRIMARY KEY)
- `name`
- `created_at`

#### `suppliers`
- `id` (PRIMARY KEY)
- `name`
- `contact_info`
- `created_at`

#### `departments`
- `id` (PRIMARY KEY)
- `name`
- `description`
- `created_at`

#### `stock_movements`
- `id` (PRIMARY KEY)
- `stock_id` (FK)
- `movement_type` (in/out/return)
- `quantity_change`
- `reason`
- `department_id` (FK)
- `created_at`

## 🔧 Configuración del Servidor

### Requisitos de PHP
```ini
; php.ini configuraciones recomendadas
memory_limit = 256M
max_execution_time = 300
upload_max_filesize = 10M
post_max_size = 10M
```

### Apache VirtualHost
```apache
<VirtualHost *:80>
    DocumentRoot "C:/wamp64/www/stock-app"
    ServerName stock-app.local
    
    <Directory "C:/wamp64/www/stock-app">
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

### .htaccess (si es necesario)
```apache
RewriteEngine On
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$ index.php [QSA,L]
```

## 🔐 Configuración de Seguridad

### Configuraciones PHP
- `register_globals = Off`
- `magic_quotes_gpc = Off`
- `allow_url_fopen = Off`
- `display_errors = Off` (en producción)

### Base de Datos
- Usuario con permisos mínimos necesarios
- Conexiones SSL cuando sea posible
- Prepared statements para todas las consultas

## 📱 Configuración Responsive

### Breakpoints Bootstrap
- **xs**: <576px (móviles)
- **sm**: ≥576px (móviles grandes)
- **md**: ≥768px (tablets)
- **lg**: ≥992px (desktops)
- **xl**: ≥1200px (desktops grandes)

## 🎯 Rutas del Sistema

### Principales
- `/` - Dashboard principal
- `/stock/` - Gestión de inventario
- `/cost-analysis/` - Análisis de costos
- `/history/` - Historial de movimientos
- `/reports/` - Reportes y estadísticas

### API Endpoints
- `POST /cost-analysis/update-cost` - Actualizar costos
- `GET /cost-analysis/export-products-pdf` - Exportar PDF
- `POST /cost-analysis/export-custom-pdf` - Exportar PDF personalizado

## 🔄 Estados del Sistema

### Stock Status
- `active`: Producto activo en inventario
- `inactive`: Producto descontinuado
- `out_of_stock`: Sin existencias

### Movement Types
- `in`: Entrada de mercadería
- `out`: Salida de mercadería
- `return`: Devolución
- `adjustment`: Ajuste de inventario

## 📋 Logs y Debugging

### PHP Error Log
```php
error_log("Mensaje de debug", 3, "logs/debug.log");
```

### JavaScript Console
```javascript
console.log('Debug info:', data);
console.error('Error:', error);
```

## 🚀 Optimizaciones

### Performance
- Índices apropiados en base de datos
- Paginación en consultas grandes
- Caching de consultas frecuentes
- Compresión de assets

### SEO (futuro)
- URLs amigables
- Meta tags dinámicos
- Sitemap XML
- Robots.txt
