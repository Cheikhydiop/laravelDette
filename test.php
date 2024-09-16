<?php
require 'vendor/autoload.php';

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

Configuration::instance([
    'cloud' => [
        'cloud_name' => getenv('CLOUDINARY_CLOUD_NAME'),
        'api_key' => getenv('CLOUDINARY_API_KEY'),
        'api_secret' => getenv('CLOUDINARY_API_SECRET')
    ],
    'url' => [
        'secure' => true
    ]
]);

$cloudinary = new Cloudinary();
$imagePath = '/path/to/your/image.jpg'; // Remplacez ce chemin par le chemin réel de votre image

try {
    $result = $cloudinary->uploadApi()->upload($imagePath, [
        'folder' => 'clients_photos',
        'public_id' => 'test_image',
        'resource_type' => 'image'
    ]);
    echo json_encode($result, JSON_PRETTY_PRINT);
} catch (Exception $e) {
    echo 'Erreur: ' . $e->getMessage();
}
