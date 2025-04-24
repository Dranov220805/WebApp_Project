<?php

require 'vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// Use the SearchApi class for searching assets
use Cloudinary\Api\Search\SearchApi;
// Use the AdminApi class for managing assets
use Cloudinary\Api\Admin\AdminApi;
// Use the UploadApi class for uploading assets

//Configuration::instance('cloudinary://184481963549152:UsBSDvYPSAkheYpFz4H0hLyNYco@dydpf7z8u?secure=true');

function uploadToCloudinary($filePath, $folder = 'my_app_uploads')
{
    // Setup Cloudinary config (use environment variables or config file in production)
    Configuration::instance([
        'cloud' => [
            'cloud_name' => 'dydpf7z8u',
            'api_key' => '184481963549152',
            'api_secret' => 'UsBSDvYPSAkheYpFz4H0hLyNYco',
        ],
        'url' => [
            'secure' => true
        ]
    ]);

    try {
        $result = (new UploadApi())->upload($filePath, [
            'folder' => $folder
        ]);

        return [
            'success' => true,
            'url' => $result['secure_url'],
            'public_id' => $result['public_id'],
            'original_filename' => $result['original_filename']
        ];

    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}
