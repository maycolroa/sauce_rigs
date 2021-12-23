<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnReincReinstatementCondition extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_reinc_tags_reinstatement_condition', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::create('sau_reinc_tags_informant_role', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->unsignedInteger('company_id');

            $table->foreign('company_id')->references('id')->on('sau_companies')->onUpdate('cascade')->onDelete('cascade');

            $table->timestamps();
        });

        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->string('reinstatement_condition')->nullable();
            $table->string('recommendations_validity')->nullable();
        });

        Schema::table('sau_reinc_tracings', function (Blueprint $table) {
            $table->string('informant_role')->after('description')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_reinc_tags_reinstatement_condition');
        Schema::dropIfExists('sau_reinc_tags_informant_role');
        
        Schema::table('sau_reinc_checks', function (Blueprint $table) {
            $table->dropColumn('reinstatement_condition');
            $table->dropColumn('recommendations_validity');
        });

        Schema::table('sau_reinc_tracings', function (Blueprint $table) {
            $table->dropColumn('informant_role');
        });
    }
}
