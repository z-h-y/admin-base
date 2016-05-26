<?php

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

		$this->call('CodecsSeeder');
		$this->call('UserSeeder');
		$this->call('RoleSeeder');
		$this->call('PermissionSeeder');

		/****** App's seeder - START ******/



		/****** App's seeder - END ******/

		// TestSeeder必须在最后
		$this->call('TestSeeder');
	}

}
