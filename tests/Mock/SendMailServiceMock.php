<?php
declare(strict_types=1);

namespace Tests\Mock;


use App\Services\Mailer;
use App\Services\MailerCircuitBreaker;
use App\Services\SendMailService;

trait SendMailServiceMock
{
    public function mockSendMailService($withArgs)
    {
        $circuit    = \Mockery::mock(MailerCircuitBreaker::class);
        $mailer     = \Mockery::mock(Mailer::class);

        $mailer
            ->shouldReceive('getName')
            ->andReturn('mailer')
        ;

        $circuit
            ->shouldReceive('isAvailable')
            ->with('mailer')
            ->andReturn(true)
        ;

        if ($withArgs) {
            $mailer
                ->shouldReceive('send')
                ->once()
                ->withArgs($withArgs)
            ;
        } else {
            $mailer
                ->shouldReceive('send')
            ;
        }

        $circuit
            ->shouldReceive('success')
            ->with('mailer')
        ;

        $circuit
            ->shouldReceive('failure')
            ->with('mailer')
        ;

        app()->bind(
            MailerCircuitBreaker::class,
            function() use ($circuit) {
                return $circuit;
            }
        );

        app()->when(SendMailService::class)
            ->needs(Mailer::class)
            ->give( function($app) use ($mailer) {
                return [$mailer];
            })
        ;
    }
}
