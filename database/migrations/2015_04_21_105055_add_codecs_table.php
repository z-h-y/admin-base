<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodecsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('codecs', function(Blueprint $table)
		{
			$table->increments('id')->unsigned();		
			$table->string('group');
			$table->integer('code')->unsigned();
			$table->string('name');
			$table->string('comment')->nullable();
			$table->boolean('active')->default(true);
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
		Schema::drop('codecs');
	}

}
