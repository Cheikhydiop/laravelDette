<?php

namespace App\Listeners;

use App\Events\ClientCreated;
use App\Services\UploadServiceCloud;
use Illuminate\Support\Facades\Storage;

class UploadClientPhoto
{
    protected $uploadService;

    public function __construct(UploadServiceCloud $uploadService)
    {
        $this->uploadService = $uploadService;
    }

    public function handle(ClientCreated $event)
    {
        $client = $event->client;

        if ($client->photo && file_exists($client->photo)) {
            try {
                $photoUrl = $this->uploadService->uploadImage($client->photo);
                $client->update(['photo' => $photoUrl]);
            } catch (\Exception $e) {
                // Si l'upload échoue, sauvegarder la photo localement pour la réessayer plus tard
                Storage::disk('local')->put('failed_uploads/' . $client->id, $client->photo);
            }
        }
    }
}

