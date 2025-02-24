<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnRequiredExpiretionDateDriverDocumentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::table('sau_rs_drivers_documents', function (Blueprint $table) {   
            $table->string('required_expiration_date')->nullable()->after('updated_at')->default('SI');
       });

       /*Schema::table('sau_rs_vehicles', function (Blueprint $table) {   
            $table->string('required_due_date_soat')->nullable()->after('expedition_date_soat')->default('SI');
            $table->string('required_due_date_mechanical_tech')->nullable()->after('expedition_date_mechanical_tech')->default('SI');
            $table->string('required_due_date_policy')->nullable()->after('expedition_date_policy')->default('SI');
       });*/
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_rs_drivers_documents', function (Blueprint $table) {            
            $table->dropColumn('required_expiration_date');
        });

        /*Schema::table('sau_rs_vehicles', function (Blueprint $table) {   
            $table->dropColumn('required_due_date_soat');
            $table->dropColumn('required_due_date_mechanical_tech');
            $table->dropColumn('required_due_date_policy');
       });*/
    }
}
