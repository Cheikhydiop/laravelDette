<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Firestore;

class FirebaseService implements DatabaseInterface
{
    protected $firestore;

    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(__DIR__.'/path/to/firebase_credentials.json');
        $this->firestore = $factory->createFirestore();
    }

    public function insertDocument(array $data)
    {
        try {
            $collection = $this->firestore->database()->collection('archives');
            $docRef = $collection->add($data);
            return $docRef->id();
        } catch (\Exception $e) {
            throw new \Exception('Firebase Error: ' . $e->getMessage());
        }
    }

    public function getDocuments()
    {
        try {
            $collection = $this->firestore->database()->collection('archives');
            return $collection->documents()->rows();
        } catch (\Exception $e) {
            throw new \Exception('Firebase Error: ' . $e->getMessage());
        }
    }
}
