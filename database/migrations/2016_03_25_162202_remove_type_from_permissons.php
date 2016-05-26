<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RemoveTypeFromPermissons extends Migration
{
    /**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
        Schema::table('permissions', function(Blueprint $table)
		{
			$table->dropColumn('type');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
        Schema::table('permissions', function(Blueprint $table)
		{
			$table->integer('type')->nullable();
		});
	}
}
