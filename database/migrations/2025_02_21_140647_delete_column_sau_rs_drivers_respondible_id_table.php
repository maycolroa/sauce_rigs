<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumnSauRsDriversRespondibleIdTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('sau_rs_drivers', function (Blueprint $table) {   
            $table->dropForeign('sau_rs_drivers_responsible_id_foreign');
            $table->dropColumn('responsible_id');
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
           $table->unsignedInteger('responsible_id')->nullable();

            $table->foreign('responsible_id')
                ->references('id')
                ->on('sau_employees')
                ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }
}
