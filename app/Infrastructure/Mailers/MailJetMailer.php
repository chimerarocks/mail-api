<?php
declare(strict_types=1);

namespace App\Infrastructure\Mailers;

use App\Domain\Exceptions\MailerRequestException;
use App\Domain\Services\Mailer;
use App\Domain\Values\Mail;
use Illuminate\Mail\Markdown;
use Mailjet\Resources;

class MailJetMailer implements Mailer
{
    public function getName(): string
    {
        return 'mailjet';
    }

    public function send(Mail $mail): void
    {
        $mj = new \Mailjet\Client(
            env('MAILJET_KEY'),
            env('MAILJET_SECRET'),
            true,
            [
                'version' => 'v3.1'
            ]
        );

        $message = [
            'From' => [
                'Email' => $mail->getFrom(),
            ],
            'To' => array_merge([
                    [
                        'Email' => $mail->getTo()
                    ]
                ], array_map(function($email) { return ['Email' => $email]; }, $mail->getCc())
            ),
            'Subject' => $mail->getSubject(),
            'CustomID' => "MailApi"
        ];

        $messageBody = $this->makeBody($mail);

        $message = $message + $messageBody;

        $body = [
            'Messages' => [
                $message
            ]
        ];

        $response = $mj->post(Resources::$Email, ['body' => $body]);
        if (!$response->success()) {
            throw new MailerRequestException($response->getBody(), $response->getStatus());
        }
    }

    public function makeBody(Mail $mail): array
    {
        switch ($mail->getFormat()) {
            case Mail::TYPE_TEXT:
                return ['TextPart' => $mail->getBody()];
            case Mail::TYPE_HTML:
                return ['HtmlPart' => $mail->getBody()];
            case Mail::TYPE_MARKDOWN:
                return ['HtmlPart' => Markdown::parse($mail->getBody())->toHtml()];
        }
    }
}
