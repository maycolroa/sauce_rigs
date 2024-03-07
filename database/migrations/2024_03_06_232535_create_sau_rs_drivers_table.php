<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauRsDriversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_rs_drivers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('employee_id');
            $table->string('type_license');
            $table->date('date_license');
            $table->unsignedInteger('vehicle_id');
            $table->unsignedInteger('responsible_id');
            $table->timestamps();

            $table->foreign('employee_id')->references('id')->on('sau_employees')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('vehicle_id')->references('id')->on('sau_rs_vehicles')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('responsible_id')->references('id')->on('sau_employees')->onUpdate('cascade')->onDelete('cascade');
        });

        Schema::create('sau_rs_drivers_documents', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('driver_id');
            $table->string('name');
            $table->string('file');
            $table->timestamps();

            $table->foreign('driver_id')->references('id')->on('sau_rs_drivers')->onDelete('cascade');
        });

        Schema::create('sau_rs_tag_type_license', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');

            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_rs_drivers_documents');
        Schema::dropIfExists('sau_rs_drivers');
        Schema::dropIfExists('sau_rs_tag_type_license');
    }
}
