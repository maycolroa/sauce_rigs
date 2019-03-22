<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCtSubobjectiveTypeRatingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_subobjective_type_rating', function (Blueprint $table) {
            $table->unsignedInteger('subobjective_id');
            $table->unsignedInteger('type_rating_id');
            $table->string('apply');
            $table->string('value');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_subobjective_type_rating');
    }
}
