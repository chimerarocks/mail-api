<?php

namespace App\Application\Providers;

use App\Domain\Services\Mailer;
use App\Domain\Services\MailerCircuitBreaker;
use App\Infrastructure\CircuitBreaker\GaneshaMailerCircuitBreaker;
use App\Infrastructure\Mailers\MailJetMailer;
use App\Infrastructure\Mailers\SendGridMailer;
use App\Domain\Services\SendMailService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            MailerCircuitBreaker::class,
            GaneshaMailerCircuitBreaker::class
        );

        $this->app->when(SendMailService::class)
            ->needs(Mailer::class)
            ->give( function($app) {
                return [
                    new MailJetMailer(),
                    new SendGridMailer()
                ];
            })
        ;
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
