<?php


namespace App\Exceptions;


use Throwable;

class NoMailServiceAvailable extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct("No mail service available;" . $message, $code, $previous);
    }
}
