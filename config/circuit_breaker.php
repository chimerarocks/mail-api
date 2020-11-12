<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Circuit Breaker Default Connection
    |--------------------------------------------------------------------------
    |
    | This option controls the default circuit breaker connection based on cache stores.
    | This connection is used when another is not explicitly specified when executing the circuit.
    |
    | Supported: "apcu", "memcached", "redis"
    |
    */

    'default' => env('CIRCUIT_BREAKER_CONNECTION', 'redis'),
];
