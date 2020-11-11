<?php
declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Exceptions\MailerRequestException;
use App\Services\Mailer;
use App\Services\MailerCircuitBreaker;
use App\Services\SendMailService;
use Tests\Data\Value\ValidMail;
use Tests\TestCase;

class SendMailServiceTest extends TestCase
{
    public function testSend()
    {
        $circuit    = \Mockery::mock(MailerCircuitBreaker::class);
        $mailer     = \Mockery::mock(Mailer::class);

        $mailer
            ->shouldReceive('getName')
            ->once()
            ->andReturn('service1')
        ;

        $circuit
            ->shouldReceive('isAvailable')
            ->once()
            ->with('service1')
            ->andReturn(true)
        ;

        $mailer
            ->shouldReceive('send')
            ->once()
            ->getMock()
        ;

        $circuit
            ->shouldReceive('success')
            ->once()
            ->with('service1')
        ;

        $service = new SendMailService(
            $circuit,
            $mailer
        );

        $service->send(new ValidMail());
    }

    public function testSendWithManyMailers()
    {
        $circuit = \Mockery::mock(MailerCircuitBreaker::class);
        $mailer1 = \Mockery::mock(Mailer::class);
        $mailer2 = \Mockery::mock(Mailer::class);

        $mailer1->shouldReceive('getName')
            ->once()
            ->andReturn('service1')
        ;

        $circuit->shouldReceive('isAvailable')
            ->once()
            ->with('service1')
            ->andReturn(false);

        $mailer2
            ->shouldReceive('getName')
            ->once()
            ->andReturn('service2')
        ;

        $circuit->shouldReceive('isAvailable')
            ->once()
            ->with('service2')
            ->andReturn(true);

        $mailer2
            ->shouldReceive('send')
            ->once()
        ;

        $circuit
            ->shouldReceive('success')
            ->once()
            ->with('service2')
            ->andReturnNull()
        ;

        $service = new SendMailService(
            $circuit,
            $mailer1,
            $mailer2
        );

        $service->send(new ValidMail());
    }

    public function testSendThroughFallback()
    {
        $circuit = \Mockery::mock(MailerCircuitBreaker::class);
        $mailer1 = \Mockery::mock(Mailer::class);
        $mailer2 = \Mockery::mock(Mailer::class);

        $mailer1->shouldReceive('getName')
            ->once()
            ->andReturn('service1')
        ;

        $circuit->shouldReceive('isAvailable')
            ->once()
            ->with('service1')
            ->andReturn(true)
        ;

        $mailer1->shouldReceive('send')
            ->once()
            ->andThrow(MailerRequestException::class)
        ;

        $circuit
            ->shouldReceive('failure')
            ->once()
            ->with('service1')
            ->andReturnNull()
        ;

        $mailer2
            ->shouldReceive('getName')
            ->once()
            ->andReturn('service2')
        ;

        $circuit->shouldReceive('isAvailable')
            ->once()
            ->with('service2')
            ->andReturn(true);

        $mailer2
            ->shouldReceive('send')
            ->once()
        ;

        $circuit
            ->shouldReceive('success')
            ->once()
            ->with('service2')
            ->andReturnNull()
        ;

        $service = new SendMailService(
            $circuit,
            $mailer1,
            $mailer2
        );

        $service->send(new ValidMail());
    }

    public function testSendNoServiceAvailable()
    {
        $this->expectExceptionMessage("No mail service available");
        $circuit = \Mockery::mock(MailerCircuitBreaker::class);
        $mailer1 = \Mockery::mock(Mailer::class);
        $mailer2 = \Mockery::mock(Mailer::class);


        $mailer1->shouldReceive('getName')
            ->once()
            ->andReturn('service1')
        ;

        $circuit->shouldReceive('isAvailable')
            ->once()
            ->with('service1')
            ->andReturn(false)
        ;

        $mailer2
            ->shouldReceive('getName')
            ->once()
            ->andReturn('service2')
        ;

        $circuit->shouldReceive('isAvailable')
            ->once()
            ->with('service2')
            ->andReturn(false);

        $service = new SendMailService(
            $circuit,
            $mailer1,
            $mailer2
        );

        $service->send(new ValidMail());
    }
}
