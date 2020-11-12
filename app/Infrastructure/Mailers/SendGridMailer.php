<?php
declare(strict_types=1);

namespace App\Infrastructure\Mailers;

use App\Domain\Exceptions\MailerRequestException;
use App\Domain\Services\Mailer;
use App\Domain\Values\Mail;
use Illuminate\Mail\Markdown;

class SendGridMailer implements Mailer
{
    public function getName(): string
    {
        return 'sendgrid';
    }

    public function send(Mail $mail): void
    {
        $recipients = $mail->getCc();
        array_push($recipients, $mail->getTo());

        $email = new \SendGrid\Mail\Mail();
        $email->setFrom($mail->getFrom());
        $email->setSubject($mail->getSubject());
        $email->addTo(implode(',', $recipients));

        $this->addContent($email, $mail);

        $sendgrid = new \SendGrid(env('SENDGRID_API_KEY'));
        try {
            $sendgrid->send($email);
        } catch (\Exception $e) {
            throw new MailerRequestException($e->getMessage(), $e->getCode());
        }
    }

    public function addContent(\SendGrid\Mail\Mail $sendGridMail, Mail $mail): void
    {
        switch ($mail->getFormat()) {
            case Mail::TYPE_TEXT:
                $sendGridMail->addContent("text/plain", $mail->getBody());
                break;
            case Mail::TYPE_HTML:
                $sendGridMail->addContent("text/html", $mail->getBody());
                break;
            case Mail::TYPE_MARKDOWN:
                $sendGridMail->addContent("text/html", Markdown::parse($mail->getBody())->toHtml());
                break;
        }
    }
}
