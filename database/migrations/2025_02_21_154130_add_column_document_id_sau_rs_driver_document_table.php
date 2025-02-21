<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnDocumentIdSauRsDriverDocumentTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sau_rs_drivers_documents', function (Blueprint $table) {            
            $table->unsignedInteger('position_document_id')->nullable();
 
             $table->foreign('position_document_id')
                 ->references('id')
                 ->on('sau_rs_position_documents')
                 ->onUpdate('cascade')
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
       Schema::table('sau_rs_drivers_documents', function (Blueprint $table) {   
            $table->dropForeign('sau_rs_drivers_documents_position_document_id_foreign');
            $table->dropColumn('position_document_id');
        });
    }
}
