<?php

namespace Tests\Feature;

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
            'format'  => 'Markdown',
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

    public function test_should_returns_bad_request_status_code_when_required_fields_are_not_sent()
    {
        $invalidRequestData = [
            'from'    => '',
            'to'      => '',
            'subject' => '',
            'body'    => ''
        ];
        $invalidRequestData = $invalidRequestData + $this->validRequestData;

        $response = $this->json('POST', route('v1.mail'), $invalidRequestData);

        $this->assertInvalidFields($response, $invalidRequestData, 'required');
    }

    public function test_should_returns_bad_request_status_code_when_type_of_fields_are_sent_with_a_wrong_type()
    {
        $invalidRequestData = [
            'from'    => 'not_an_email',
            'to'      => 'not_an_email',
            'format'  => 'invalid_type'
        ];
        $invalidRequestData = $invalidRequestData + $this->validRequestData;

        $response = $this->json('POST', route('v1.mail'), $invalidRequestData);

        $this->assertInvalidFields($response, $invalidRequestData, 'email');
        $this->assertInvalidFields($response, $invalidRequestData, 'in');
    }

    public function test_should_returns_internal_server_error()
    {
        $response = $this->json('POST', route('v1.mail'), $this->validRequestData);

        $response->assertStatus(500);
    }
}
