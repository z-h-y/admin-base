<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOperationLogs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('operation_logs', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();
			$table->string('username')->nullable();
			$table->string('user_roles')->nullable();
            $table->string('table');
            $table->integer('record_id')->unsigned();
            $table->string('event');
            $table->string('content');
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('operation_logs');
	}

}
