<?php
// Habilitar reporte de errores para debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'auth.php';
require_once 'image_processor.php';

// Verificar que el usuario esté logueado
if (!isAdminLoggedIn()) {
    header('Location: admin_login.php');
    exit;
}

// Solo procesar si es un POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $serviceId = $_POST['serviceId'] ?? '';
    $serviceTitle = $_POST['serviceTitle'] ?? '';
    $serviceDescription = $_POST['serviceDescription'] ?? '';

    // Cargar datos actuales
    $servicesData = json_decode(file_get_contents('services_data.json'), true);

    if (isset($servicesData[$serviceId])) {
        // Actualizar título y descripción
        $servicesData[$serviceId]['title'] = $serviceTitle;
        $servicesData[$serviceId]['description'] = $serviceDescription;

        // Manejar la subida de imagen si hay una nueva
        if (isset($_FILES['serviceImage']) && $_FILES['serviceImage']['error'] === UPLOAD_ERR_OK) {
            $uploadDir = 'assets/img/portfolio/';
            $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
            $fileType = $_FILES['serviceImage']['type'];

            if (in_array($fileType, $allowedTypes)) {
                try {
                    // Obtener información de la imagen
                    $imageInfo = getimagesize($_FILES['serviceImage']['tmp_name']);
                    $extension = getImageExtension($imageInfo[2]);

                    $newFileName = 'service_' . $serviceId . '_' . time() . '.' . $extension;
                    $uploadPath = $uploadDir . $newFileName;

                    // Procesar la imagen (redimensionar y recortar a 900x650px)
                    resizeAndCropImage($_FILES['serviceImage']['tmp_name'], $uploadPath, 900, 650);

                    // Eliminar imagen anterior si no es una imagen por defecto
                    $oldImage = $servicesData[$serviceId]['image'];
                    if (file_exists($oldImage) && !in_array(basename($oldImage), ['cabin.png', 'cake.png', 'circus.png', 'game.png', 'safe.png', 'submarine.png'])) {
                        unlink($oldImage);
                    }

                    $servicesData[$serviceId]['image'] = $uploadPath;

                } catch (Exception $e) {
                    $_SESSION['error'] = 'Error al procesar la imagen: ' . $e->getMessage();
                    header('Location: index.php');
                    exit;
                }
            } else {
                $_SESSION['error'] = 'Tipo de archivo no permitido. Solo se permiten JPG, PNG y GIF.';
                header('Location: index.php');
                exit;
            }
        }

        // Guardar cambios en el archivo JSON
        if (file_put_contents('services_data.json', json_encode($servicesData, JSON_PRETTY_PRINT))) {
            $_SESSION['message'] = 'Servicio actualizado correctamente';
        } else {
            $_SESSION['error'] = 'Error al guardar los cambios';
        }
    } else {
        $_SESSION['error'] = 'Servicio no encontrado';
    }

    header('Location: index.php');
    exit;
} else {
    // Si no es un POST, redirigir al index
    $_SESSION['error'] = 'Acceso no válido';
    header('Location: index.php');
    exit;
}
?>