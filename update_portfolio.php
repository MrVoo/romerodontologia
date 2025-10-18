<?php
session_start();
require_once 'auth.php';

// Verificar autenticación
if (!isAdminLoggedIn()) {
    $_SESSION['error'] = 'Acceso no autorizado';
    header('Location: admin_login.php');
    exit;
}

try {
    // Cargar texto por defecto
    $defaultText = file_get_contents('assets/img/default/default.txt');
    $defaultImage = 'assets/img/default/default.webp';

    // Inicializar array de imágenes finales (siempre 6 imágenes)
    $finalImages = array_fill(0, 6, $defaultImage);

    // Procesar imágenes existentes
    if (isset($_POST['existingImages']) && is_array($_POST['existingImages'])) {
        foreach ($_POST['existingImages'] as $index => $image) {
            if ($index < 6) {
                $finalImages[$index] = $image;
            }
        }
    }

    // Procesar archivos de reemplazo de imágenes
    if (isset($_FILES['imageFiles'])) {
        $uploadDir = 'assets/img/portfolio/';

        // Crear directorio si no existe
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        foreach ($_FILES['imageFiles']['tmp_name'] as $index => $tmpName) {
            if ($index >= 6) continue;

            if ($_FILES['imageFiles']['error'][$index] == UPLOAD_ERR_OK) {
                $imageInfo = getimagesize($tmpName);
                if ($imageInfo !== false) {
                    // Eliminar imagen anterior si no es la por defecto
                    $oldImage = $finalImages[$index];
                    if ($oldImage !== $defaultImage &&
                        strpos($oldImage, 'assets/img/portfolio/') !== false &&
                        file_exists($oldImage)) {
                        unlink($oldImage);
                    }

                    // Generar nombre único
                    $extension = pathinfo($_FILES['imageFiles']['name'][$index], PATHINFO_EXTENSION);
                    $filename = 'portfolio_' . $index . '_' . time() . '.' . $extension;
                    $targetPath = $uploadDir . $filename;

                    // Procesar y guardar imagen
                    if (processImage($tmpName, $targetPath, $imageInfo[2], 1400, 800)) {
                        $finalImages[$index] = $targetPath;
                    }
                }
            }
        }
    }

    // Actualizar descripción (usar por defecto si está vacía)
    $description = $_POST['portfolioDescription'];
    // Limpiar HTML vacío de Quill (solo <p><br></p> o similar)
    $cleanDescription = trim(strip_tags($description, '<strong><em><u><ol><ul><li>'));

    if (empty($cleanDescription)) {
        $description = $defaultText;
    }

    // Preparar datos para guardar
    $portfolioData = [
        'description' => $description,
        'images' => array_values($finalImages)
    ];

    // Guardar JSON
    file_put_contents('portfolio_data.json', json_encode($portfolioData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $_SESSION['message'] = 'Portafolio actualizado exitosamente';

} catch (Exception $e) {
    $_SESSION['error'] = 'Error al actualizar el portafolio: ' . $e->getMessage();
}

header('Location: index.php#about');
exit;

function processImage($source, $destination, $imageType, $maxWidth = 1400, $maxHeight = 800) {
    // Cargar imagen según tipo
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $image = imagecreatefromjpeg($source);
            break;
        case IMAGETYPE_PNG:
            $image = imagecreatefrompng($source);
            break;
        case IMAGETYPE_GIF:
            $image = imagecreatefromgif($source);
            break;
        case IMAGETYPE_WEBP:
            $image = imagecreatefromwebp($source);
            break;
        default:
            return false;
    }

    if (!$image) {
        return false;
    }

    // Obtener dimensiones originales
    $width = imagesx($image);
    $height = imagesy($image);

    // Calcular nuevas dimensiones manteniendo aspecto
    $ratio = min($maxWidth / $width, $maxHeight / $height);

    if ($ratio < 1) {
        $newWidth = round($width * $ratio);
        $newHeight = round($height * $ratio);

        // Crear nueva imagen
        $newImage = imagecreatetruecolor($newWidth, $newHeight);

        // Preservar transparencia para PNG
        if ($imageType == IMAGETYPE_PNG) {
            imagealphablending($newImage, false);
            imagesavealpha($newImage, true);
        }

        // Redimensionar
        imagecopyresampled($newImage, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
        imagedestroy($image);
        $image = $newImage;
    }

    // Guardar imagen
    $result = false;
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $result = imagejpeg($image, $destination, 90);
            break;
        case IMAGETYPE_PNG:
            $result = imagepng($image, $destination, 9);
            break;
        case IMAGETYPE_GIF:
            $result = imagegif($image, $destination);
            break;
        case IMAGETYPE_WEBP:
            $result = imagewebp($image, $destination, 90);
            break;
    }

    imagedestroy($image);
    return $result;
}
?>
