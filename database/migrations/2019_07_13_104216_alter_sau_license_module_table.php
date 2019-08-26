<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterSauLicenseModuleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_license_module', function (Blueprint $table) {
            $table->dropColumn('id');
            $table->dropColumn('created_at');
            $table->dropColumn('updated_at');

            $table->dropForeign('sau_license_module_license_id_foreign');

            $table->foreign('license_id')
                ->references('id')
                ->on('sau_licenses')
                ->onDelete('cascade');

            $table->dropForeign('sau_license_module_module_id_foreign');

            $table->foreign('module_id')
                    ->references('id')
                    ->on('sau_modules')
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
        Schema::table('sau_license_module', function (Blueprint $table) {
            $table->increments('id')->first();
            $table->timestamps();
            
            $table->dropForeign('sau_license_module_license_id_foreign');

            $table->foreign('license_id')
                ->references('id')
                ->on('sau_licenses');

            $table->dropForeign('sau_license_module_module_id_foreign');

            $table->foreign('module_id')
                        ->references('id')
                        ->on('sau_modules')
                        ->onDelete('cascade');
        });
    }
}
