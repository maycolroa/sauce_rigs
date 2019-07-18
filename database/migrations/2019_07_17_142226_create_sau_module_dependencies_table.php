<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauModuleDependenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_module_dependencies', function (Blueprint $table) {
            $table->unsignedInteger('module_id');
            $table->unsignedInteger('module_dependence_id');

            $table->foreign('module_id')->references('id')->on('sau_modules')->onDelete('cascade');
            $table->foreign('module_dependence_id')->references('id')->on('sau_modules')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_module_dependencies');
    }
}
