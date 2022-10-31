<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauPhUserLocationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ph_user_regionals', function (Blueprint $table) {
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("employee_regional_id");
            $table->unsignedInteger('company_id');

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
            $table->foreign('employee_regional_id')->references('id')->on('sau_employees_regionals')->onDelete('cascade');
        });

        Schema::create('sau_ph_user_headquarters', function (Blueprint $table) {
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("employee_headquarter_id");
            $table->unsignedInteger('company_id');

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
            $table->foreign('employee_headquarter_id')->references('id')->on('sau_employees_headquarters')->onDelete('cascade');
        });

        Schema::create('sau_ph_user_processes', function (Blueprint $table) {
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("employee_process_id");
            $table->unsignedInteger('company_id');

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
            $table->foreign('employee_process_id')->references('id')->on('sau_employees_processes')->onDelete('cascade');
        });

        Schema::create('sau_ph_user_areas', function (Blueprint $table) {
            $table->unsignedInteger("user_id");
            $table->unsignedInteger("employee_area_id");
            $table->unsignedInteger('company_id');

            $table->foreign('company_id')->references('id')->on('sau_companies')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('sau_users')->onDelete('cascade');
            $table->foreign('employee_area_id')->references('id')->on('sau_employees_areas')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ph_user_regionals');
        Schema::dropIfExists('sau_ph_user_headquarters');
        Schema::dropIfExists('sau_ph_user_processes');
        Schema::dropIfExists('sau_ph_user_areas');
    }
}
