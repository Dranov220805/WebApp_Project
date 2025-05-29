<?php
require 'vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

Configuration::instance([
    'cloud' => [
        'cloud_name' => 'your_cloud_name',
        'api_key' => 'your_api_key',
        'api_secret' => 'your_api_secret',
    ],
    'url' => ['secure' => true]
]);

function uploadAvatarToCloudinary($fileTmpPath): array
{
    try {
        $uploadResult = (new UploadApi())->upload($fileTmpPath, [
            'folder' => 'Pernote/user-icon'
        ]);

        return [
            'status' => true,
            'url' => $uploadResult['secure_url'],
            'public_id' => $uploadResult['public_id']
        ];
    } catch (Exception $e) {
        return [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }
}

function uploadNoteImageToCloudinary($fileTmpPath): array {
    try {
        $uploadResult = (new UploadApi())->upload($fileTmpPath, [
            'folder' => 'Pernote/user-image'
        ]);

        return [
            'status' => true,
            'url' => $uploadResult['secure_url'],
            'public_id' => $uploadResult['public_id']
        ];
    } catch (Exception $e) {
        return [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }
}

function extractPublicIdFromUrl(string $url): ?string
{
    $parsedUrl = parse_url($url);
    if (!isset($parsedUrl['path'])) {
        return null;
    }

    // Remove leading slash and split path
    $pathParts = explode('/', ltrim($parsedUrl['path'], '/'));

    // Find index of "upload" (always part of Cloudinary path)
    $uploadIndex = array_search('upload', $pathParts);
    if ($uploadIndex === false || !isset($pathParts[$uploadIndex + 1])) {
        return null;
    }

    // public_id is everything after "upload/" (excluding version and extension)
    $publicIdParts = array_slice($pathParts, $uploadIndex + 2);
    $publicIdWithExtension = implode('/', $publicIdParts);

    // Remove extension
    $publicId = preg_replace('/\.[^.]+$/', '', $publicIdWithExtension);
    return $publicId;
}

function deleteImageFromCloudinary(string $publicId)
{
    try {
        $deleteResult = (new UploadApi())->destroy($publicId, [
            'resource_type' => 'image'
        ]);

        return [
            'status' => true,
            'message' => 'Deleted successfully'
        ];
    } catch (Exception $e) {
        return [
            'status' => false,
            'message' => $e->getMessage()
        ];
    }
}

function deleteImageByImageUrl(string $imageUrl) {
    $publicId = extractPublicIdFromUrl($imageUrl);

    return deleteImageFromCloudinary($publicId);
}