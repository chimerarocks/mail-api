<?php
declare(strict_types=1);

namespace App\Application\Console\Commands;

use App\Application\Http\V1\Requests\MailPost;
use App\Application\Jobs\SendMailJob;
use App\Domain\Values\Mail;
use App\Domain\Services\SendMailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Queue;

class SendMailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'mail:send {from} {to} {subject} {body} {--cc=} {--format=} {--sync} {--file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(SendMailService $sendMailService)
    {
        $from    = $this->argument('from');
        $to      = $this->argument('to');
        $subject = $this->argument('subject');
        $body    = $this->argument('body');
        $cc      = $this->option('cc');
        $format  = $this->option('format');

        if (is_string($cc)) {
            $cc = explode(',', $cc);
        }

        if ($this->option('file')) {
            if (!file_exists($body)) {
                $this->error("Unable to read file: " . $body);
                return 1;
            }
            $body = file_get_contents($body);
        }

        $validator = MailPost::validateData([
            'from'      => $from,
            'to'        => $to,
            'subject'   => $subject,
            'body'      => $body,
            'cc'        => $cc,
            'format'    => $format
        ]);

        if ($validator->fails()) {
            $this->error($validator->errors());
            return 1;
        }

        $mail = new Mail(
            $from,
            $to,
            $subject,
            $body,
            $cc,
            $format,
        );

        if ($this->option('sync')) {
            $sendMailService->send($mail);
            return 0;
        }

        Queue::push(new SendMailJob($mail));

        return 0;
    }
}
