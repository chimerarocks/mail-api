<?php

namespace App\Application\Jobs;

use App\Domain\Values\Mail;
use App\Domain\Services\SendMailService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $mail;

    /**
     * Create a new job instance.
     *
     * @param \App\Domain\Values\Mail $mail
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     *
     * @param \App\Domain\Services\SendMailService $sendMailService
     * @return void
     * @throws \Exception
     */
    public function handle(SendMailService $sendMailService)
    {
        $sendMailService->send($this->mail);
    }
}
