<?php
declare(strict_types=1);

namespace Tests\API;

use App\Domain\Values\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;
use Tests\Traits\ResponsesTest;
use Tests\Traits\ValidationsTest;

class MailTest extends TestCase
{
    use ValidationsTest, ResponsesTest;

    /**
     * @var array
     */
    private $validRequestData;

    private $postMailSuccessStatusCode;

    private $postMailValidationFailedStatusCode;

    protected function setUp(): void
    {
        parent::setUp();
        $this->validRequestData = [
            'from'    => 'no-reply@server.com',
            'to'      => 'jondoe@foo.com',
            'cc'      => ['jackdoe@foo.com'],
            'format'  => Mail::TYPE_TEXT,
            'subject' => 'Subject of the email',
            'body'    => 'Body text'
        ];
        $this->postMailSuccessStatusCode          = 201;
        $this->postMailValidationFailedStatusCode = 422;
    }

    public function test_should_returns_success_status_code()
    {
        Queue::fake();
        $response = $this->json('POST', route('v1.mail'), $this->validRequestData);

        $this->assertJsonResponseStatusAndPayload($response, $this->postMailSuccessStatusCode);
    }

    public function test_should_returns_success_status_code_when_optional_field_are_not_sent()
    {
        Queue::fake();
        $this->validRequestData['cc']     = '';
        $this->validRequestData['format'] = '';

        $response = $this->json('POST', route('v1.mail'), $this->validRequestData);

        $this->assertJsonResponseStatusAndPayload($response, $this->postMailSuccessStatusCode);

        // testing with nulls
        $this->validRequestData['cc']     = null;
        $this->validRequestData['format'] = null;

        $response = $this->json('POST', route('v1.mail'), $this->validRequestData);

        $this->assertJsonResponseStatusAndPayload($response, $this->postMailSuccessStatusCode);

        // testing without fields
        unset($this->validRequestData['cc']);
        unset($this->validRequestData['format']);

        $this->assertJsonResponseStatusAndPayload($response, $this->postMailSuccessStatusCode);
    }

    public function test_should_returns_error_status_code_when_required_fields_are_not_sent()
    {
        Queue::fake();
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
        Queue::fake();
        $invalidEmailsRequestData = [
            'from'    => 'not_an_email',
            'to'      => 'not_an_email',
            'cc'      => ['not_an_email']
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
