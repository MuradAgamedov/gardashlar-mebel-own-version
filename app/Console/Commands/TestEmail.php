<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use App\Mail\OTPMail;

class TestEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test:email {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test email sending functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');
        $otp = '123456';

        $this->info("Testing email sending to: {$email}");
        $this->info("OTP Code: {$otp}");

        try {
            Mail::to($email)->send(new OTPMail($otp));
            $this->info('Email sent successfully!');
            return 0;
        } catch (\Exception $e) {
            $this->error('Failed to send email: ' . $e->getMessage());
            $this->error('Stack trace: ' . $e->getTraceAsString());
            return 1;
        }
    }
} 