<?php

namespace App\Console\Commands;

use App\Mail\InvitePromoterMail;
use App\Models\Invitation;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class AddPromoterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add-promoter {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add promoter';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $email = $this->argument('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('email is invalid');
            return 1;
        }

        if (User::where('email', $email)->exists()) {
            $this->error('email already exists');
            return 1;
        }

        $invitation = Invitation::create([
            'email' => $email,
            'register_link' => URL::temporarySignedRoute(
                'register',
                now()->addMinutes(30),
            )
        ]);

        Mail::to($email)->queue(new InvitePromoterMail($invitation));
    }
}
