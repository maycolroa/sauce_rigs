<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnFirmEmployeeSauCtTrainingEmployeeAttempTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('sau_ct_training_employee_attempts', function (Blueprint $table) {            
            $table->string('firm')->nullable()->after('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
       Schema::table('sau_ct_training_employee_attempts', function (Blueprint $table) {            
        $table->dropColumn('firm');
        });
    }
}
