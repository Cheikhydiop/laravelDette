<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ClientController;
use App\Http\Controllers\TestController;
use App\Jobs\RetryPhotoUpload;
use App\Jobs\UploadImageJob;
use App\Http\Controllers\DetteController;
use App\Models\Client;
use App\Http\Controllers\DemandeControlleur;
use Illuminate\Support\Facades\Route;
// use App\Http\Controllers\MongoTestController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\NotificationController;

use App\Events\TestEvent;

Route::get('/export-dettes', [ExportController::class, 'export']);
// routes/web.php
Route::post('/demandes', [DemandeControlleur::class, 'store']);
Route::get('/demande', [DemandeControlleur::class, 'showDemande']);

Route::post('/demandes/{id}/relance', [DemandeControlleur::class, 'relancer']);
Route::get('/demandes/notifications/client', [NotificationController::class, 'index']);


Route::get('/test-pusher', function () {
    $message = 'Hello Pusher!';
    broadcast(new TestEvent($message));
    return 'Event has been broadcast!';
});
Route::view('/test', 'test');

    Route::prefix('v1')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout.post');
    Route::get('/client/{id}/user', [ClientController::class, 'WithUser']);
    // Route::get('/mongo', [MongoTestController::class, 'testConnection']);
    Route::get('/send-sms', [TestController::class, 'sendSms']);




    Route::get('/test-job', function () {
        $client = Client::find(1); // Assurez-vous de mettre un ID valide
        dispatch(new RetryPhotoUpload($client));
        return 'Job dispatched!';
    });

    Route::get('/upload-image', function () {
        dispatch(new UploadImageJob());
        return 'Image upload job dispatched!';
    });

    // Route::post('/test-upload', [TestController::class, 'testUpload']);
    // Route::get('/test', [TestController::class, 'testEndpoint']);
});

// Routes protégées par l'authentification
Route::middleware('auth:api')->prefix('v1')->group(function () {
    Route::get('/articles', [ArticleController::class, 'verification']);
    Route::get('/article/{id}', [ArticleController::class, 'show']);
    Route::post('/articles/libelle', [ArticleController::class, 'findByLibelle']);

    // Routes protégées par la politique d'administrateur
    Route::middleware('can:viewAny,App\Models\User')->group(function () {

                Route::put('/articles/{id}', [ArticleController::class, 'updateOne']);
                Route::post('/storeArticle', [ArticleController::class, 'store']);
                Route::post('/stock', [ArticleController::class, 'updateStock']);
                 Route::post('/storeUser', [UserController::class, 'storeUser']);
                Route::get('/users', [UserController::class, 'getUsers']);
                Route::post('/store', [UserController::class, 'store']);
                Route::get('/users/{id}', [UserController::class, 'show']);
                Route::get('/client/{telephone}', [ClientController::class, 'getByTelephone']);
                Route::get('/dettes', [DetteController::class, 'index']);
                Route::post('/dettes', [DetteController::class, 'store']);
                Route::get('/dettes/{id}/paiements', [DetteController::class, 'getPaiements']);
                Route::post('/dettes/{id}/paiements', [DetteController::class, 'addPaiement']);
                Route::get('/dettes/{id}/articles', [DetteController::class, 'getDetailsWithArticles']);  
                Route::get('/dettes/{id}/paiement', [DetteController::class, 'getPaiements']);
                Route::post('/storeClient', [ClientController::class, 'store']);

    });

    Route::get('/clients/{id}', [ClientController::class, 'showClient']);
  
    Route::get('/clients', [ClientController::class, 'index']);
    Route::get('/dettes/{id}', [DetteController::class, 'show']);

    Route::get('/dettes/{id}', [DetteController::class, 'show']);

   


});






