<?php

require 'vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

function uploadToCloudinary($filePath, $folder = 'Pernote/user-icon')
{
    Configuration::instance([
        'cloud' => [
            'cloud_name' => 'dydpf7z8u',
            'api_key' => '184481963549152',
            'api_secret' => 'UsBSDvYPSAkheYpFz4H0hLyNYco',
        ],
        'url' => ['secure' => true]
    ]);

    try {
        $result = (new UploadApi())->upload($filePath, ['folder' => $folder]);
        return ['success' => true, 'url' => $result['secure_url']];
    } catch (Exception $e) {
        return ['success' => false, 'error' => $e->getMessage()];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['avatar'])) {
    $tempPath = $_FILES['avatar']['tmp_name'];
    $uploadResult = uploadToCloudinary($tempPath);

    if ($uploadResult['success']) {
        $_SESSION['avatar_url'] = $uploadResult['url']; // Save avatar URL in session
        // Optionally save in DB too
        echo "Upload successful!";
    } else {
        echo "Upload failed: " . $uploadResult['error'];
    }
}
