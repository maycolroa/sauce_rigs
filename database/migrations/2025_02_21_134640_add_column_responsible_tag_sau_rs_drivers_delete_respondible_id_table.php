<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnResponsibleTagSauRsDriversDeleteRespondibleIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('sau_rs_drivers', function (Blueprint $table) {   
            /*$table->dropForeign('sau_rs_drivers_responsible_id_foreign');
            $table->dropColumn('responsible_id');       */ 
            $table->string('responsible')->nullable()->after('date_license');
       });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_rs_drivers', function (Blueprint $table) {            
            $table->dropColumn('responsible');
            /*$table->unsignedInteger('responsible_id')->nullable();

            $table->foreign('responsible_id')
                ->references('id')
                ->on('sau_employees')
                ->onUpdate('cascade')
                ->onDelete('cascade');*/
        });
    }
}
