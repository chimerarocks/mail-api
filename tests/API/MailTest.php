<?php

namespace Tests\Feature;

use App\Models\Values\MailFormat;
use Tests\TestCase;
use Tests\Traits\ValidationsTest;

class MailTest extends TestCase
{
    use ValidationsTest;

    /**
     * @var array
     */
    private $validRequestData;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validRequestData = [
            'from'    => 'no-reply@server.com',
            'to'      => 'jondoe@foo.com',
            'cc'      => 'jackdoe@foo.com',
            'format'  => MailFormat::TYPE_TEXT,
            'subject' => 'Subject of the email',
            'body'    => 'Body text'
        ];
    }

    public function test_should_returns_success_status_code()
    {
        $response = $this->json('POST', route('v1.mail'), $this->validRequestData);

        if ($response->status() !== 201) {
            throw new \Exception("Response status must be 201, given {$response->status()}:\n {$response->content()}");
        }
        $response->assertJsonStructure();
    }

    public function test_should_returns_success_status_code_when_optional_field_are_not_sent()
    {
        $this->validRequestData['cc']     = '';
        $this->validRequestData['format'] = '';

        $response = $this->json('POST', route('v1.mail'), $this->validRequestData);

        if ($response->status() !== 201) {
            throw new \Exception("Response status must be 201, given {$response->status()}:\n {$response->content()}");
        }
        $response->assertJsonStructure();

        // testing with nulls
        $this->validRequestData['cc']     = null;
        $this->validRequestData['format'] = null;

        $response = $this->json('POST', route('v1.mail'), $this->validRequestData);

        if ($response->status() !== 201) {
            throw new \Exception("Response status must be 201, given {$response->status()}:\n {$response->content()}");
        }
        $response->assertJsonStructure();

        // testing without fields
        unset($this->validRequestData['cc']);
        unset($this->validRequestData['format']);

        if ($response->status() !== 201) {
            throw new \Exception("Response status must be 201, given {$response->status()}:\n {$response->content()}");
        }
        $response->assertJsonStructure();
    }

    public function test_should_returns_error_status_code_when_required_fields_are_not_sent()
    {
        $invalidRequestData = [
            'from'    => '',
            'to'      => '',
            'subject' => '',
            'body'    => ''
        ];

        $response = $this->json('POST', route('v1.mail'), $invalidRequestData);

        $this->assertInvalidFields($response, $invalidRequestData, 'required');
    }

    public function test_should_returns_error_status_code_when_type_of_fields_are_sent_with_a_wrong_type()
    {
        $invalidEmailsRequestData = [
            'from'    => 'not_an_email',
            'to'      => 'not_an_email',
            'cc'      => 'not_an_email'
        ];
        $requestData = $invalidEmailsRequestData + $this->validRequestData;

        $response = $this->json('POST', route('v1.mail'), $requestData);

        $this->assertInvalidFields($response, $invalidEmailsRequestData, 'email');

        $invalidEnumRequestData = [
            'format'      => 'not_valid_type'
        ];
        $requestData = $invalidEnumRequestData + $this->validRequestData;

        $response = $this->json('POST', route('v1.mail'), $requestData);
        $this->assertInvalidFields($response, $invalidEnumRequestData, 'in');
    }
}
