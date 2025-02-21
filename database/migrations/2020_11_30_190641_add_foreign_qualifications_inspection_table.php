<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddForeignQualificationsInspectionTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_ph_inspection_items_qualification_area_location', function (Blueprint $table) {
            $table->dropForeign('sau_qualification_id_foreign');

            $table->foreign('qualification_id', 'sau_qualification_id_foreign')
                ->references('id')
                ->on('sau_ph_qualifications_inspections')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
