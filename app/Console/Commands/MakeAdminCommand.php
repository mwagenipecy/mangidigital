<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdminCommand extends Command
{
    protected $signature = 'make:admin {email : The user email}';

    protected $description = 'Set a user as admin and approved by email';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();

        if (! $user) {
            $this->error('User not found.');
            $this->line('If you just ran db:seed, use: php artisan make:admin test@example.com');

            return self::FAILURE;
        }

        $user->update([
            'is_admin' => true,
            'status' => User::STATUS_APPROVED,
        ]);

        $this->info("User {$user->email} is now an admin and approved.");

        return self::SUCCESS;
    }
}
