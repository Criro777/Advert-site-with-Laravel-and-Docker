<?php

namespace App\Console\Commands\User;

use App\Entity\User;
use App\Services\Auth\RegisterService;
use Illuminate\Console\Command;

class VerifyCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:verify {email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verify user email';

    /**
     * @var RegisterService
     */
    public $service;

    /**
     * Create a new command instance.
     *
     * @param RegisterService $service
     * @return void
     */
    public function __construct(RegisterService $service)
    {
        parent::__construct();
        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return bool
     */
    public function handle(): bool
    {
        $email = $this->argument('email');

        /** @var User $user */
        if (!$user = User::where('email', $email)->first()) {
            $this->error('Undefined user with email ' . $email);
            return false;
        }

        try {
            $this->service->verify($user->id);
        } catch (\DomainException $e) {
            $this->error($e->getMessage());
            return false;
        }

        $this->info('User is successfully verified');
        return true;
    }
}
