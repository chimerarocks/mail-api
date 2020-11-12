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
        /** @var \Illuminate\Redis\RedisManager $redis */
        $redis         = Redis::getFacadeRoot();

        $this->circuit = Builder::withRateStrategy()
            ->adapter(new \Ackintosh\Ganesha\Storage\Adapter\Redis($redis->client()))
            ->failureRateThreshold(50)
            ->intervalToHalfOpen(10)
            ->minimumRequests(10)
            ->timeWindow(30)
            ->build();
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
