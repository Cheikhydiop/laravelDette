<?php

namespace App\Services;

use Cloudinary\Cloudinary;
use Cloudinary\Configuration\Configuration;
use Exception;

class UploadServiceCloud
{
    protected $cloudinary;

    public function __construct()
    {
        // Configuration de Cloudinary avec les informations d'identification
        Configuration::instance([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key' => env('CLOUDINARY_API_KEY'),
                'api_secret' => env('CLOUDINARY_API_SECRET'),
            ],
            'url' => [
                'secure' => true
            ]
        ]);

        // Création d'une instance de Cloudinary
        $this->cloudinary = new Cloudinary();
    }

    public function uploadImage($photoPath)
    {
        try {
            // Tentative d'upload de l'image sur Cloudinary
            $uploadResult = $this->cloudinary->uploadApi()->upload($photoPath, [
                'folder' => 'clients_photos',
                'public_id' => pathinfo($photoPath, PATHINFO_FILENAME),
                'resource_type' => 'image'
            ]);

            // Retourner l'URL sécurisée de l'image uploadée
            return $uploadResult['secure_url'];
        } catch (Exception $e) {
            // Gestion d'erreur : affichage du message détaillé
            throw new Exception('Échec de l\'upload sur Cloudinary : ' . $e->getMessage());
        }
    }
}
