<?php
declare(strict_types=1);

namespace App\Services;


use App\Models\Values\Mail;

interface Mailer
{
    public function getName(): string;

    public function send(Mail $mail): void;
}
