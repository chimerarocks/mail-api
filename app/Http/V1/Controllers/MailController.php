<?php
declare(strict_types=1);

namespace App\Http\V1\Controllers;

use App\Http\Controller;
use App\Http\V1\Requests\MailPost;
use App\Jobs\SendMailJob;
use App\Models\Values\Mail;
use Illuminate\Support\Facades\Queue;

class MailController extends Controller
{
    public function send(MailPost $mailPost)
    {
        $mail = new Mail(
            $mailPost->get('from'),
            $mailPost->get('to'),
            $mailPost->get('subject'),
            $mailPost->get('body'),
            $mailPost->get('cc'),
            $mailPost->get('format')
        );
        Queue::push(new SendMailJob($mail));
        return response($mailPost, 201);
    }
}
