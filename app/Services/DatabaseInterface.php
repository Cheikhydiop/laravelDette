<?php

namespace App\Services;

interface DatabaseInterface
{
    public function insertDocument(array $data);
    public function getDocuments();
}
