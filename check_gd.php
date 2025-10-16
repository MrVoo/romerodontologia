<?php
echo "<h2>Verificación de la extensión GD</h2>";

if (extension_loaded('gd')) {
    echo "<p style='color: green;'>✅ La extensión GD está instalada y disponible.</p>";

    $gdInfo = gd_info();
    echo "<h3>Información de GD:</h3>";
    echo "<ul>";
    foreach ($gdInfo as $key => $value) {
        echo "<li><strong>$key:</strong> " . (is_bool($value) ? ($value ? 'Sí' : 'No') : $value) . "</li>";
    }
    echo "</ul>";

    echo "<p style='color: green;'>✅ El sistema de redimensionado de imágenes funcionará correctamente.</p>";
} else {
    echo "<p style='color: red;'>❌ La extensión GD NO está instalada.</p>";
    echo "<h3>Instrucciones de instalación:</h3>";
    echo "<h4>Para Windows con XAMPP/WAMP:</h4>";
    echo "<ol>";
    echo "<li>Abrir el archivo <code>php.ini</code></li>";
    echo "<li>Buscar la línea <code>;extension=gd</code></li>";
    echo "<li>Quitar el punto y coma del inicio: <code>extension=gd</code></li>";
    echo "<li>Reiniciar Apache</li>";
    echo "</ol>";

    echo "<h4>Para WSL2/Ubuntu:</h4>";
    echo "<pre>sudo apt update\nsudo apt install php-gd\nsudo systemctl restart apache2</pre>";

    echo "<h4>Para PHP standalone:</h4>";
    echo "<p>Descargar una versión de PHP que incluya GD desde <a href='https://windows.php.net/download/'>https://windows.php.net/download/</a></p>";
}

echo "<br><a href='index.php'>← Volver al sitio principal</a>";
?>