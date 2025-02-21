<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnTypeSauPhInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ph_inspections', function (Blueprint $table) {
            $table->unsignedInteger('type_id')->after('state')->default(1);

            $table->foreign('type_id')->references('id')->on('sau_ph_type_inspections')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ph_inspections', function (Blueprint $table) {            
            $table->dropForeign('sau_ph_inspections_type_id_foreign');
            $table->dropColumn('type_id');
        });
    }
}
