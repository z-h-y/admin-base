<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameCodeToValueCodecs extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('codecs', function(Blueprint $table)
		{
			$table->renameColumn('code', 'value');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('codecs', function(Blueprint $table)
		{
			$table->renameColumn('value', 'code');
		});
	}

}
