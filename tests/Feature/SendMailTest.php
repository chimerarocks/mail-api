<?php
declare(strict_types=1);

namespace Tests\Feature;

use App\Application\Jobs\SendMailJob;
use App\Domain\Services\Mailer;
use App\Domain\Services\MailerCircuitBreaker;
use App\Domain\Services\SendMailService;
use App\Domain\Values\Mail;
use Illuminate\Support\Facades\Queue;
use Tests\Mock\SendMailServiceMock;
use Tests\TestCase;
use Tests\Traits\ResponsesTest;

class SendMailTest extends TestCase
{
    use ResponsesTest, SendMailServiceMock;

    /**
     * @var array
     */
    private $validRequestData;

    private $postMailSuccessStatusCode;

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
    }

    /**
        Scenario: The mail should be sent in a asynchronous way
        Given There is a valid mail
        When I send the mail
        Then It should be put in a queue to be sent asynchronously
    */
    public function test_should_queue_mail()
    {
        Queue::fake();
        $response    = $this->json('POST', route('v1.mail'), $this->validRequestData);
        $this->assertJsonResponseStatusAndPayload($response, $this->postMailSuccessStatusCode);

        Queue::assertPushed(SendMailJob::class);
    }

    /**
        Scenario: If a mail service is unavailable there should be a fallback to a secondary service and so on
        Given a mail service is unavailable
        When I send the mail
        Then the mail should fail
        And it should be se sent through another mail service available
     */
    public function test_should_send_mail_through_another_mail_service()
    {
        $requestData = $this->validRequestData;

        $circuit            = \Mockery::mock(MailerCircuitBreaker::class);
        $mailer             = \Mockery::mock(Mailer::class);
        $mailerFallback     = \Mockery::mock(Mailer::class);

        $mailer
            ->shouldReceive('getName')
            ->once()
            ->andReturn('mailer')
        ;

        $circuit
            ->shouldReceive('isAvailable')
            ->once()
            ->with('mailer')
            ->andReturn(false)
        ;

        $mailerFallback
            ->shouldReceive('getName')
            ->once()
            ->andReturn('mailerFallback')
        ;

        $circuit
            ->shouldReceive('isAvailable')
            ->once()
            ->with('mailerFallback')
            ->andReturn(true)
        ;

        $mailerFallback
            ->shouldReceive('send')
            ->once()
            ->withArgs(function (Mail $mail) use ($requestData) {
                $this->assertSentMailContent($mail, $requestData);
                return true;
            })
        ;

        $circuit
            ->shouldReceive('success')
            ->once()
            ->with('mailerFallback')
        ;

        app()->bind(
            MailerCircuitBreaker::class,
            function() use ($circuit) {
                return $circuit;
            }
        );

        app()->when(SendMailService::class)
            ->needs(Mailer::class)
            ->give( function($app) use ($mailer, $mailerFallback) {
                return [$mailer, $mailerFallback];
            })
        ;

        $response    = $this->json('POST', route('v1.mail'), $this->validRequestData);
        $this->assertJsonResponseStatusAndPayload($response, $this->postMailSuccessStatusCode);
    }

    /**
        Scenario: It should be able to use the API through a CLI command
        Given There is a valid mail
        And It the Client is sending it through a CLI
        When I send the mail
        Then the response should be success
        And the mail sent
     */
    public function test_should_be_able_to_use_api_through_cli()
    {
        $requestData = $this->validRequestData;

        $this->mockSendMailService(function (Mail $mail) use ($requestData) {
            $this->assertSentMailContent($mail, $requestData);
            return true;
        });

        $params = '"' . $this->validRequestData['from'] . '"';
        $params .= ' "' . $this->validRequestData['to'] . '"';
        $params .= ' "' . $this->validRequestData['subject'] . '"';
        $params .= ' "' . $this->validRequestData['body'] . '"';
        $params .= ' --cc="' . implode(',', $this->validRequestData['cc']) . '"';
        $params .= ' --format="' . $this->validRequestData['format'] . '"';
        $this->artisan('mail:send ' . $params)
            ->assertExitCode(0);
    }

    protected function assertSentMailContent(Mail $mail, $requestData)
    {
        $mailTo = $mail->getTo();
        $mailFrom = $mail->getFrom();
        $mailCc = $mail->getCc();
        $mailBody = $mail->getBody();
        $mailSubject = $mail->getSubject();

        if ($mailTo != $requestData['to']) {
            throw new \Exception("Mail 'to' field must be {$requestData['to']}, but it is {$mailTo}");
        }

        if ($mailFrom != $requestData['from']) {
            throw new \Exception("Mail 'from' field must be {$requestData['from']}, but it is {$mailFrom}");
        }

        if ($mailCc != $requestData['cc']) {
            throw new \Exception("Mail 'cc' field must be {$requestData['cc']}, but it is {$mailCc}");
        }

        if ($mailBody != $requestData['body']) {
            throw new \Exception("Mail 'body' field must be {$requestData['body']}, but it is {$mailBody}");
        }

        if ($mailSubject != $requestData['subject']) {
            throw new \Exception("Mail 'subject' field must be {$requestData['subject']}, but it is {$mailSubject}");
        }
    }
}
