<?php

namespace Tests\Traits;

use Illuminate\Testing\TestResponse;

trait ValidationsTest
{
    protected function assertInvalidFields(
        TestResponse $response,
        array $data,
        string $rule,
        array $ruleParams = []
    )
    {
        $fields = array_keys($data);
        $response->assertStatus(422)
            ->assertJsonValidationErrors($fields);

        foreach ($fields as $field) {
            $fieldName = str_replace('_', ' ', $field);
            $response->assertJsonFragment([
                \Lang::get("validation.$rule", ['attribute' => $fieldName] + $ruleParams)
            ]);
        }
    }
}
