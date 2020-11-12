<?php
declare(strict_types=1);

namespace App\Infrastructure\CircuitBreaker;


use Ackintosh\Ganesha\Builder;
use App\Domain\Services\MailerCircuitBreaker;
use Illuminate\Support\Facades\Redis;

class GaneshaMailerCircuitBreaker implements MailerCircuitBreaker
{
    /**
     * @var \Ackintosh\Ganesha
     */
    private $circuit;

    /**
     * GaneshaMailerCircuitBreaker constructor.
     */
    public function __construct()
    {
        $this->circuit = Builder::withRateStrategy()
            ->adapter($this->getAdapter())
            ->failureRateThreshold(25)
            ->intervalToHalfOpen(10)
            ->minimumRequests(10)
            ->timeWindow(30)
            ->build();
    }

    private function getAdapter()
    {
        $connection = config('circuit_breaker.default');
        switch ($connection) {
            case "apc":
            case "apcu": return new \Ackintosh\Ganesha\Storage\Adapter\Apcu();

            case "redis":
            default:
                $redis         = Redis::getFacadeRoot();
                return new \Ackintosh\Ganesha\Storage\Adapter\Redis(
                    $redis->client()
                );
        }

    }

    public function isAvailable(string $service): bool
    {
        return $this->circuit->isAvailable($service);
    }

    public function success(string $service): void
    {
        $this->circuit->success($service);
    }

    public function failure(string $service): void
    {
        $this->circuit->failure($service);
    }
}
