<?php

function backupPhpIni($phpIniPath) {
    $backupPath = $phpIniPath . '.backup-' . date('Y-m-d-His');
    if (!copy($phpIniPath, $backupPath)) {
        die("Error: No se pudo crear el backup del archivo php.ini\n");
    }
    echo "Backup creado en: $backupPath\n";
}

function updatePhpIni($phpIniPath) {
    // Crear backup antes de modificar
    backupPhpIni($phpIniPath);

    $content = file_get_contents($phpIniPath);
    if ($content === false) {
        die("Error: No se pudo leer el archivo php.ini\n");
    }

    // Configuraciones a actualizar
    $updates = [
        // Opcache
        'opcache.enable' => '1',
        'opcache.enable_cli' => '1',
        'opcache.memory_consumption' => '128',
        'opcache.interned_strings_buffer' => '8',
        'opcache.max_accelerated_files' => '4000',
        'opcache.revalidate_freq' => '60',
        'opcache.fast_shutdown' => '1',
        'opcache.jit' => 'disable',
        'opcache.jit_buffer_size' => '0',

        // Rendimiento general
        'memory_limit' => '256M',
        'max_execution_time' => '120',
        'post_max_size' => '16M',
        'upload_max_filesize' => '8M',
        'max_input_vars' => '3000',

        // Sesiones y seguridad
        'session.gc_maxlifetime' => '7200',
        'session.cookie_httponly' => '1',
        'session.use_strict_mode' => '1',
        'session.cookie_samesite' => '"Strict"'
    ];

    foreach ($updates as $key => $value) {
        $pattern = '/^' . preg_quote($key) . '\s*=.*$/m';
        $replacement = $key . ' = ' . $value;
        
        if (preg_match($pattern, $content)) {
            $content = preg_replace($pattern, $replacement, $content);
        } else {
            // Si la configuración no existe, la agregamos en la sección correspondiente
            $section = strpos($key, 'opcache.') === 0 ? '[opcache]' : '[PHP]';
            $pos = strpos($content, $section);
            if ($pos !== false) {
                $content = substr_replace($content, $section . "\n" . $replacement . "\n", $pos, strlen($section));
            }
        }
    }

    // Guardar cambios
    if (file_put_contents($phpIniPath, $content) === false) {
        die("Error: No se pudo escribir en el archivo php.ini\n");
    }

    echo "Configuración de PHP actualizada exitosamente.\n";
}

// Crear archivo de configuración de Apache para optimización
function createApacheOptimizationConfig() {
    $htaccessContent = <<<EOT
# Habilitar compresión GZIP
<IfModule mod_deflate.c>
    SetOutputFilter DEFLATE
    AddOutputFilterByType DEFLATE text/html text/css text/plain text/xml application/x-javascript application/json
</IfModule>

# Configurar caché del navegador
<IfModule mod_expires.c>
    ExpiresActive On
    ExpiresByType image/jpg "access plus 1 year"
    ExpiresByType image/jpeg "access plus 1 year"
    ExpiresByType image/gif "access plus 1 year"
    ExpiresByType image/png "access plus 1 year"
    ExpiresByType text/css "access plus 1 month"
    ExpiresByType application/javascript "access plus 1 month"
</IfModule>

# Habilitar Keep-Alive
<IfModule mod_headers.c>
    Header set Connection keep-alive
</IfModule>

# Configuración de seguridad adicional
<IfModule mod_headers.c>
    Header set X-Content-Type-Options "nosniff"
    Header set X-Frame-Options "SAMEORIGIN"
    Header set X-XSS-Protection "1; mode=block"
    Header set Referrer-Policy "strict-origin-when-cross-origin"
</IfModule>
EOT;

    $htaccessPath = __DIR__ . '/../.htaccess';
    if (file_exists($htaccessPath)) {
        copy($htaccessPath, $htaccessPath . '.backup-' . date('Y-m-d-His'));
    }

    if (file_put_contents($htaccessPath, $htaccessContent) === false) {
        die("Error: No se pudo crear el archivo .htaccess\n");
    }

    echo "Archivo .htaccess creado/actualizado exitosamente.\n";
}

// Detectar la ubicación del php.ini
$phpIniPath = php_ini_loaded_file();
if ($phpIniPath === false) {
    die("Error: No se pudo encontrar el archivo php.ini\n");
}

echo "Iniciando optimización...\n";
echo "Archivo php.ini encontrado en: $phpIniPath\n";

// Aplicar las optimizaciones
updatePhpIni($phpIniPath);
createApacheOptimizationConfig();

echo "\nOptimización completada. Por favor, reinicia el servidor Apache para aplicar los cambios.\n";
echo "Nota: Asegúrate de que los módulos mod_deflate, mod_expires y mod_headers estén habilitados en Apache.\n";
