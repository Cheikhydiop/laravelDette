<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Cloudinary\Cloudinary;

class UploadImageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle()
    {
        $imagePath = public_path('/home/diop/Images/khafif_general.jpg'); // Assurez-vous que ce chemin est correct

        // Téléchargez l'image vers Cloudinary
        $cloudinary = \Cloudinary\Laravel\Facades\Cloudinary::getCloudinary();

        $uploadResult = $cloudinary->uploadApi()->upload($imagePath, [
            'folder' => 'test_images'
        ]);

        \Log::info('Image uploaded to Cloudinary: ' . $uploadResult['secure_url']);
    }
}
