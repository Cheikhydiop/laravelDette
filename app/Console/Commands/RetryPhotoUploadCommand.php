<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\RetryPhotoUpload;
use App\Models\User;
use Illuminate\Support\Facades\Storage;

class RetryPhotoUploadCommand extends Command
{
    protected $signature = 'photo:retry-upload {userId} {photoPath}';
    protected $description = 'Retry the upload of a photo for a given user';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $userId = $this->argument('userId');
        $photoPath = $this->argument('photoPath');

        // if (!Storage::disk('public')->exists($photoPath)) {
        //     $this->error('Le fichier photo spécifié n\'existe pas.');
        //     return 1;
        // }

        //php artisan photo:retry-upload 5 /home/diop/avatar.png
        // hp artisan photo:retry-all
        
        if (!file_exists($photoPath)) {
            $this->error('Le fichier photo spécifié n\'existe pas.');
            return 1;
        }
        

       
        

        $user = User::find($userId);
        if (!$user) {
            $this->error('Utilisateur non trouvé.');
            return 1;
        }

        RetryPhotoUpload::dispatch($user, $photoPath);

        $this->info('Le job RetryPhotoUpload a été dispatché avec succès.');
        return 0;
    }
}
