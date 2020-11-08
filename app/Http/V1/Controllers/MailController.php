<?php

namespace App\Http\V1\Controllers;

use App\Http\Controller;
use App\Http\V1\Requests\MailPost;

class MailController extends Controller
{
    public function send(MailPost $mailPost)
    {
        return response($mailPost, 201);
    }
}
