<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Repositories\ClientRepositoryInterface;
use App\Repositories\ClientRepository;
use App\Services\ClientServiceInterface;
use App\Services\ClientService;
use App\Repositories\ArticleRepository;
use App\Services\UploadService;
use App\Services\QrCodeService;
use MongoDB\Client as MongoClient;
use App\Services\DatabaseInterface;
use App\Services\MongoDBService;
use App\Services\FirebaseService;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(ClientRepositoryInterface::class, ClientRepository::class);
        $this->app->bind(ClientServiceInterface::class, ClientService::class);

        $this->app->singleton('articleRepository', function ($app) {
            return new ArticleRepository();
        });

        $this->app->singleton(UploadService::class, function ($app) {
            return new UploadService();
        });

        $this->app->singleton(QrCodeService::class, function ($app) {
            return new QrCodeService();
        });

        $this->app->singleton(MongoClient::class, function ($app) {
            return new MongoClient(env('MONGODB_URI'));
        });
        // $dbType = env('DB_TYPE');
        // dd($dbType);
        // \Log::info('DB_TYPE value: ' . $dbType);
    
        // if ($dbType === 'mongodb') {
        //     $this->app->singleton(DatabaseInterface::class, function ($app) {
        //         return new MongoDBService(
        //             env('MONGO_DB_CONNECTION_STRING'),
        //             env('MONGO_DB_NAME'),
        //             'archives'
        //         );
        //     });
        // } elseif ($dbType === 'firebase') {
        //     $this->app->singleton(DatabaseInterface::class, function ($app) {
        //         return new FirebaseService();
        //     });
        // } else {
        //     throw new \Exception('Unsupported database type.');
        // }
    }
}
