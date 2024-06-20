<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $from = "TEST";
    public string $to;
    public string $text;

    /**
     * Create a new job instance.
     */
    public function __construct($to, $text, $from = null)
    {
        if ($from) {
            $this->from = $from;
        }
        $this->to = $to;
        $this->text = $text;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        // в этом месте нужно добавит отправку sms через api провайдера
        Log::build(
            [
                'driver' => 'single',
                'path' => storage_path('logs/auth_error.log'),
            ]
        )->info(implode(';', [$this->from, $this->to, $this->text]));
    }
}
