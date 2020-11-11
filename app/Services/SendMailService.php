<?php


namespace App\Services;


use App\Exceptions\MailerRequestException;
use App\Exceptions\NoMailServiceAvailable;
use App\Models\Values\Mail;
use Illuminate\Support\Facades\Log;

class SendMailService
{
    /**
     * @var \App\Services\MailerCircuitBreaker
     */
    private $circuitBreaker;
    /**
     * @var \App\Services\Mailer[]
     */
    private $mailers;


    /**
     * SendMailService constructor.
     * @param \App\Services\MailerCircuitBreaker $circuitBreaker
     * @param \App\Services\Mailer ...$mailers
     */
    public function __construct(MailerCircuitBreaker $circuitBreaker, Mailer ...$mailers)
    {
        $this->circuitBreaker = $circuitBreaker;
        $this->mailers = $mailers;
    }

    public function send(Mail $mail)
    {
        foreach ($this->mailers as $mailer ) {
            $service = $mailer->getName();
            if (!$this->circuitBreaker->isAvailable($service)) {
                continue;
            }
            try {
                $mailer->send($mail);
                $this->circuitBreaker->success($service);
                $this->log($mail, $service);
                return;
            } catch (MailerRequestException $e) {
                $this->circuitBreaker->failure($service);
            }
        }

        throw new NoMailServiceAvailable();
    }

    public function log(Mail $mail, string $service)
    {
        Log::channel('daily_mailing')->info('Mail sent', [
            'from'      => $mail->getFrom(),
            'to'        => $mail->getTo(),
            'cc'        => $mail->getCc(),
            'subject'   => $mail->getSubject(),
            'service'   => $service
        ]);
    }
}
