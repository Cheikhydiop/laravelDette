<?php

require 'vendor/autoload.php'; // Assurez-vous que Composer a généré l'autoloader

use MongoDB\Client as MongoClient;

// Remplacez l'URI de connexion par le vôtre
$uri = 'mongodb+srv://cheikh:cheikh1234@cluster0.vz18zv2.mongodb.net/?retryWrites=true&w=majority&appName=Cluster0';
$databaseName = 'pro2.0';
$collectionName = 'archived_debts'; // Changez le nom de la collection si nécessaire

// Créez une instance du client MongoDB
$client = new MongoClient($uri);

// Accédez à la base de données et à la collection
$database = $client->$databaseName;
$collection = $database->$collectionName;

// Récupérez tous les documents dans la collection
$documents = $collection->find();

var_dump($documents);

// Affichez les documents
foreach ($documents as $document) {
    print_r($document);
}
