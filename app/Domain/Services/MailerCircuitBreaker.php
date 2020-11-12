<?php
declare(strict_types=1);

namespace App\Domain\Services;


interface MailerCircuitBreaker
{
    public function isAvailable(string $service): bool;

    public function success(string $service): void;

    public function failure(string $service): void;
}
