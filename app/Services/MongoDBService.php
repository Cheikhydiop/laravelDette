<?php

namespace App\Services;

use MongoDB\Client as MongoClient;
use MongoDB\Exception\Exception;

class MongoDBService implements DatabaseInterface
{
    protected $mongoClient;
    protected $collection;

    public function __construct($connectionString, $databaseName, $collectionName)
    {
        $this->mongoClient = new MongoClient($connectionString);
        $this->collection = $this->mongoClient->selectDatabase($databaseName)->selectCollection($collectionName);
    }

    public function insertDocument(array $data)
    {
        try {
            $result = $this->collection->insertOne($data);
            return $result->getInsertedCount();
        } catch (Exception $e) {
            throw new \Exception('MongoDB Error: ' . $e->getMessage());
        }
    }

    public function getDocuments()
    {
        try {
            return $this->collection->find()->toArray();
        } catch (Exception $e) {
            throw new \Exception('MongoDB Error: ' . $e->getMessage());
        }
    }
}
