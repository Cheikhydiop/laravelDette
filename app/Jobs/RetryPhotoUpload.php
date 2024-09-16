<?php

// namespace App\Jobs;

// use App\Models\Client;
// use Illuminate\Bus\Queueable;
// use Illuminate\Contracts\Queue\ShouldQueue;
// use Illuminate\Foundation\Bus\Dispatchable;
// use Illuminate\Queue\InteractsWithQueue;
// use Illuminate\Queue\SerializesModels;
// use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
// use Illuminate\Support\Facades\Log;
// use Exception;
// use Illuminate\Support\Facades\Storage;

// class RetryPhotoUpload implements ShouldQueue
// {
//     use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

//     protected $client;
//     protected $photo;

//     /**
//      * Create a new job instance.
//      *
//      * @return void
//      */
//     public function __construct(Client $client, $photo)
//     {
//         $this->client = $client;
//         $this->photo = $photo;
//     }

//     /**
//      * Execute the job.
//      *
//      * @return void
//      */
//     public function handle()
//     {
//         try {
//             // Upload de la photo sur Cloudinary
//             if ($this->photo) {
//                 $photoPath = $this->photo; // Récupérer le chemin local
//                 $uploadedPhotoUrl = Cloudinary::upload($photoPath)->getSecurePath();
//                 $this->client->photo = $uploadedPhotoUrl;
//                 $this->client->upload_status = 'success';
//                 $this->client->save();
//             }

//             // Planifier le job pour envoyer l'email
//             // SendLoyaltyCardEmail::dispatch($this->client)->onQueue('emails');

//         } catch (Exception $e) {
//             // Si l'upload échoue, stocker l'image localement dans 'uploads/pending'
//             Log::error('Échec de l\'upload sur Cloudinary: ' . $e->getMessage());

//             // Déplacer la photo dans le dossier temporaire
//             $failedPhotoPath = Storage::disk('public')->move('photos/' . basename($this->photo), 'uploads/pending/' . basename($this->photo));

//             // Marquer le client avec un flag pour indiquer que l'upload a échoué
//             $this->client->photo = $failedPhotoPath;
//             $this->client->upload_status = 'failed'; // Marquer l'upload comme échoué
//             $this->client->save();
//         }
//     }
// }



namespace App\Jobs;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Exception;

class RetryPhotoUpload implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $photo;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $photo)
    {
        $this->user = $user;
        $this->photo = $photo;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Upload de la photo sur Cloudinary
            if ($this->photo) {
                $photoPath = $this->photo; // Récupérer le chemin local
                
                // Upload sur Cloudinary et obtenir l'URL de la photo
                $uploadedPhoto = Cloudinary::upload($photoPath);
                $uploadedPhotoUrl = $uploadedPhoto->getSecurePath();
                
                // Mettre à jour les informations de l'utilisateur
                $this->user->photo = $uploadedPhotoUrl;
                $this->user->upload_status = 'success'; // Assurez-vous que le champ 'upload_status' existe dans la table 'users'
                $this->user->save();
            }

            // Planifier le job pour envoyer l'email
            // SendLoyaltyCardEmail::dispatch($this->user)->onQueue('emails');

        } catch (Exception $e) {
            // Log de l'erreur
            Log::error('Échec de l\'upload sur Cloudinary: ' . $e->getMessage());

            // Déplacer la photo dans le dossier temporaire pour les fichiers échoués
            if (Storage::disk('public')->exists('photos/' . basename($this->photo))) {
                $failedPhotoPath = 'uploads/pending/' . basename($this->photo);
                Storage::disk('public')->move('photos/' . basename($this->photo), $failedPhotoPath);

                // Mettre à jour les informations de l'utilisateur
                $this->user->photo = $failedPhotoPath;
                $this->user->upload_status = 'failed'; // Assurez-vous que le champ 'upload_status' existe dans la table 'users'
                $this->user->save();
            } else {
                Log::warning('Le fichier photo local n\'existe pas : ' . $this->photo);
            }
        }
    }
}
