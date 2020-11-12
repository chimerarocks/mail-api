<?php


namespace Tests\Data\Value;


use App\Domain\Values\Mail;

class ValidMail extends Mail
{

    /**
     * ValidMail constructor.
     */
    public function __construct()
    {
        parent::__construct(
            'no-reply@server.com',
            'jondoe@foo.com',
            'Subject of the email',
            'Body text',
            ['jackdoe@foo.com'],
            'text'
        );
    }
}
