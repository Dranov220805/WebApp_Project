<?php
require 'vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

Configuration::instance([
    'cloud' => [
        'cloud_name' => 'dydpf7z8u',
        'api_key' => '184481963549152',
        'api_secret' => 'UsBSDvYPSAkheYpFz4H0hLyNYco',
    ],
    'url' => ['secure' => true]
]);

function uploadAvatarToCloudinary($fileTmpPath): array
{
    try {
        $uploadResult = (new UploadApi())->upload($fileTmpPath, [
            'folder' => 'user_avatars'
        ]);

        return [
            'success' => true,
            'url' => $uploadResult['secure_url'],
            'public_id' => $uploadResult['public_id']
        ];
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
