<?php

namespace App\Jobs;

use App\Models\Values\Mail;
use App\Services\SendMailService;
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
     * @param \App\Models\Values\Mail $mail
     */
    public function __construct(Mail $mail)
    {
        $this->mail = $mail;
    }

    /**
     * Execute the job.
     *
     * @param \App\Services\SendMailService $sendMailService
     * @return void
     * @throws \Exception
     */
    public function handle(SendMailService $sendMailService)
    {
        $sendMailService->send($this->mail);
    }
}
