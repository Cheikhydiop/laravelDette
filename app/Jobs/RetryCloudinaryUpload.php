<?php

namespace App\Jobs;

use App\Models\Client;
use App\Services\UploadServiceCloud;
use Exception;
use Illuminate\Support\Facades\Log;

class RetryCloudinaryUpload extends Job
{
    protected $photoPath;
    protected $clientId;

    public function __construct($photoPath, $clientId)
    {
        $this->photoPath = $photoPath;
        $this->clientId = $clientId;
    }

    public function handle(UploadServiceCloud $uploadService)
    {
        try {
            $client = Client::find($this->clientId);

            if ($client && file_exists(storage_path('app/public/' . $this->photoPath))) {
                // Tentative d'upload
                $cloudinaryUrl = $uploadService->uploadImage(storage_path('app/public/' . $this->photoPath));

                // Mise à jour de l'URL de la photo dans la base de données
                $client->update(['photo' => $cloudinaryUrl]);

                // Suppression de l'image locale après succès
                unlink(storage_path('app/public/' . $this->photoPath));
            }
        } catch (Exception $e) {
            Log::error('Échec de la relance de l\'upload sur Cloudinary: ' . $e->getMessage());

            // Vous pouvez définir une logique de nouvelle tentative ici ou laisser le Job échouer
        }
    }
}
