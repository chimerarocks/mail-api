<?php

namespace App\Providers;

use App\Adapter\GaneshaMailerCircuitBreaker;
use App\Services\Mailer;
use App\Services\MailerCircuitBreaker;
use App\Services\Mailers\MailJetMailer;
use App\Services\Mailers\SendGridMailer;
use App\Services\SendMailService;
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
