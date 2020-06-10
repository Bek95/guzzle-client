<?php


namespace App\Exceptions;


class PasswordException extends \Exception
{
    public function __construct(string $phoneNumber )
    {
        $message = 'Password not Valid for the account : ' . $phoneNumber;

        parent::__construct($message, "404");
    }

}
