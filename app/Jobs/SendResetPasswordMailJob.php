<?php

namespace App\Jobs;

use App\Mail\SendResetPasswordMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class SendResetPasswordMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $email;
    protected $otp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $email, string $otp)
    {
        $this->email = $email;
        $this->otp = $otp;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        Mail::to($this->email)->send(new SendResetPasswordMail($this->otp));
    }
}
