<?php
declare(strict_types=1);

namespace App\Domain\Services;


use App\Domain\Values\Mail;

interface Mailer
{
    public function getName(): string;

    public function send(Mail $mail): void;
}
