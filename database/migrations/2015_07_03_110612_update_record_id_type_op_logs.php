<?php

use Illuminate\Database\Migrations\Migration;

class UpdateRecordIdTypeOpLogs extends Migration {
    
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE operation_logs MODIFY COLUMN record_id VARCHAR(255);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //DB::statement('ALTER TABLE operation_logs MODIFY COLUMN record_id INTEGER UNSIGNED;');
    }

}
