<?php
declare(strict_types=1);

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

        $fieldIndex = 0;
        foreach ($data as $field => $value) {
            if (is_array($value)) {
                foreach ($value as $index => $each) {
                    $fields[] = $field . '.' . $index;
                }
                unset($fields[$fieldIndex]);
            }
            $fieldIndex++;
        }
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
