<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Jobs\RetryPhotoUpload;
use App\Models\User;

class RetryAllPhotosCommand extends Command
{
    protected $signature = 'photo:retry-all';
    protected $description = 'Retry uploading all photos for users';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $users = User::whereNotNull('photo')->where('upload_status', 'pending')->get();

        foreach ($users as $user) {
            $photoPath = $user->photo;

            if (file_exists($photoPath)) {
                RetryPhotoUpload::dispatch($user, $photoPath);
                $this->info('Le job d\'upload pour l\'utilisateur ID ' . $user->id . ' a été dispatché avec succès.');
            } else {
                $this->error('Le fichier photo pour l\'utilisateur ID ' . $user->id . ' n\'existe pas.');
            }
        }
    }
}
