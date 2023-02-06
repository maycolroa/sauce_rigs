<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSauCompanyGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_company_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name');
            $table->string('receive_report');
            $table->text('emails');
            $table->string('active')->default('SI');
            $table->timestamps();
        });

        Schema::table('sau_companies', function (Blueprint $table) {
            $table->unsignedInteger('company_group_id')->after('logo')->nullable();
            $table->foreign('company_group_id')->references('id')->on('sau_company_groups')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_companies', function (Blueprint $table) {
            $table->dropForeign('sau_companies_company_group_id_foreign');
            $table->dropColumn('company_group_id');
        });

        Schema::dropIfExists('sau_company_groups');

    }
}
