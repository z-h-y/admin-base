<?php namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class Renew extends Command {

	/**
	 * The console command name.
	 *
	 * @var string
	 */
	protected $name = 'admin-base:renew';

	/**
	 * The console command description.
	 *
	 * @var string
	 */
	protected $description = 'Redo the migration and seed. (Only for local!)';

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
		if (env('APP_ENV') !== 'local' && env('APP_ENV') !== 'test') {
			$this->error('This command is only available in local/test environment!');
			return;
		}

        $this->error('Dangerous!! All your data would be deleted!! Please backup your database first!!');

		if ($this->confirm('Are you already backup your database?'))
		{
			if ($this->confirm('Are you really sure to do the renew? All your data would be deleted!!'))
			{
                $this->call('migrate:reset');
                $this->call('migrate');
                $this->call('db:seed');
			}
		}
	}
}
