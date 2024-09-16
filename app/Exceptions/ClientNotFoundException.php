<?php

namespace App\Exceptions;

use Exception;

class ClientNotFoundException extends Exception
{
    public function __construct($message = "Client non trouvé", $code = 404, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
