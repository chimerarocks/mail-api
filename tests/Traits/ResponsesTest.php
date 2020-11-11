<?php
declare(strict_types=1);

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;

trait ResponsesTest
{
    protected function assertJsonResponseStatusAndPayload(TestResponse $response, int $expectedStatusCode)
    {
        $this->assertStatusCode($response, $expectedStatusCode);
        $response->assertJsonStructure();
    }

    protected function assertStatusCode(TestResponse $response, int $expectedStatusCode)
    {
        if ($response->status() !== $expectedStatusCode) {
            throw new \Exception("Response status must be {$expectedStatusCode}, given {$response->status()}:\n {$response->content()}");
        }
    }
}
