<?php

function resizeAndCropImage($sourcePath, $destinationPath, $targetWidth = 900, $targetHeight = 650) {
    // Verificar que la extensión GD esté disponible
    if (!extension_loaded('gd')) {
        throw new Exception('La extensión GD de PHP no está instalada');
    }

    // Obtener información de la imagen original
    $imageInfo = getimagesize($sourcePath);
    if (!$imageInfo) {
        throw new Exception('No se pudo leer la imagen');
    }

    $originalWidth = $imageInfo[0];
    $originalHeight = $imageInfo[1];
    $imageType = $imageInfo[2];

    // Crear recurso de imagen desde el archivo original
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $sourceImage = imagecreatefromjpeg($sourcePath);
            break;
        case IMAGETYPE_PNG:
            $sourceImage = imagecreatefrompng($sourcePath);
            break;
        case IMAGETYPE_GIF:
            $sourceImage = imagecreatefromgif($sourcePath);
            break;
        default:
            throw new Exception('Tipo de imagen no soportado');
    }

    if (!$sourceImage) {
        throw new Exception('No se pudo crear la imagen desde el archivo');
    }

    // Calcular las dimensiones de redimensionado
    $scaleWidth = $targetWidth / $originalWidth;
    $scaleHeight = $targetHeight / $originalHeight;

    // Usar la escala mayor para asegurar que la imagen cubra completamente el área objetivo
    $scale = max($scaleWidth, $scaleHeight);

    $newWidth = round($originalWidth * $scale);
    $newHeight = round($originalHeight * $scale);

    // Crear imagen temporal redimensionada
    $resizedImage = imagecreatetruecolor($newWidth, $newHeight);

    // Preservar transparencia para PNG
    if ($imageType == IMAGETYPE_PNG) {
        imagealphablending($resizedImage, false);
        imagesavealpha($resizedImage, true);
        $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
        imagefill($resizedImage, 0, 0, $transparent);
    }

    // Redimensionar la imagen
    imagecopyresampled(
        $resizedImage,
        $sourceImage,
        0, 0, 0, 0,
        $newWidth, $newHeight,
        $originalWidth, $originalHeight
    );

    // Crear imagen final con las dimensiones objetivo
    $finalImage = imagecreatetruecolor($targetWidth, $targetHeight);

    // Preservar transparencia para PNG
    if ($imageType == IMAGETYPE_PNG) {
        imagealphablending($finalImage, false);
        imagesavealpha($finalImage, true);
        $transparent = imagecolorallocatealpha($finalImage, 255, 255, 255, 127);
        imagefill($finalImage, 0, 0, $transparent);
    }

    // Calcular posición para centrar el recorte
    $cropX = round(($newWidth - $targetWidth) / 2);
    $cropY = round(($newHeight - $targetHeight) / 2);

    // Recortar y centrar la imagen
    imagecopy(
        $finalImage,
        $resizedImage,
        0, 0,
        $cropX, $cropY,
        $targetWidth, $targetHeight
    );

    // Guardar la imagen procesada
    $success = false;
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            $success = imagejpeg($finalImage, $destinationPath, 90);
            break;
        case IMAGETYPE_PNG:
            $success = imagepng($finalImage, $destinationPath);
            break;
        case IMAGETYPE_GIF:
            $success = imagegif($finalImage, $destinationPath);
            break;
    }

    // Liberar memoria
    imagedestroy($sourceImage);
    imagedestroy($resizedImage);
    imagedestroy($finalImage);

    if (!$success) {
        throw new Exception('No se pudo guardar la imagen procesada');
    }

    return true;
}

function getImageExtension($imageType) {
    switch ($imageType) {
        case IMAGETYPE_JPEG:
            return 'jpg';
        case IMAGETYPE_PNG:
            return 'png';
        case IMAGETYPE_GIF:
            return 'gif';
        default:
            return 'jpg';
    }
}

?>