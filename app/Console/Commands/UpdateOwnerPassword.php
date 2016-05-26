<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Utils\Utils;
use App\Models\User;

class UpdateOwnerPassword extends Command {

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin-base:update-owner-password {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update owner\'s password.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire()
    {
        $password = $this->argument('password');
        if (strlen($password) < 8) {
            return $this->error('Password length must greater than 8!');
        }
        if (!Utils::checkComplexPassword($password)) {
            return $this->error('Password must contain numbers and characters!');
        }

        $this->error('Dangerous!! Please make sure that you really really want to do this!!');

        if ($this->confirm('Are you really want to update the owner\'s password?'))
        {
            $owner = User::where('name', '=', 'owner')->first();
            if (!$owner) {
                return $this->error('Can\'t find the owner!');
            }

            $owner->password = $password;
            $owner->prepare();
            $owner->save();
        }

    }
}
