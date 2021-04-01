<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\LegalAspects\Contracts\ListCheckQualification;
use App\Models\LegalAspects\Contracts\ListCheckResumen;
use App\Models\LegalAspects\Contracts\ListCheckChangeHistory;

class AddColumnListQualificationIdSauCtListCheckQualificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       /* Schema::table('sau_ct_file_item_contract', function ($table) {
            $table->unsignedInteger('list_qualification_id')->nullable();

            $table->foreign('list_qualification_id')->references('id')->on('sau_ct_list_check_qualifications')->onUpdate('cascade')->onDelete('cascade');
        });

        $file_contract = DB::table('sau_ct_file_item_contract')->get();

        foreach ($file_contract as $value)
        {
            $insert = FileUpload::select('sau_ct_list_check_qualifications.id')
            ->withoutGlobalScopes()
            ->join('sau_ct_file_item_contract', 'sau_ct_file_item_contract.file_id', 'sau_ct_file_upload_contracts_leesse.id')
            ->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_ct_file_upload_contracts_leesse.user_id')
            ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_user_information_contract_lessee.information_id')
            ->join('sau_ct_list_check_qualifications', 'sau_ct_list_check_qualifications.contract_id', 'sau_ct_information_contract_lessee.id')
            ->where('sau_ct_file_upload_contracts_leesse.id', $value->file_id)
            ->first();

            DB::table('sau_ct_file_item_contract')->where('file_id', $value->file_id)->where('item_id', $value->item_id)->update([
                'list_qualification_id' => $insert->id
            ]);
        }

        Schema::table('sau_ct_item_qualification_contract', function ($table) {
            $table->unsignedInteger('list_qualification_id')->nullable();

            $table->foreign('list_qualification_id')->references('id')->on('sau_ct_list_check_qualifications')->onUpdate('cascade')->onDelete('cascade');
        });

        $lists_check = ItemQualificationContractDetail::get();

        foreach ($lists_check as $value) 
        {
            $qualification = ListCheckQualification::select('id')->where('contract_id', $value->contract_id)->first();

            $value->update([
                'list_qualification_id' => $qualification->id
            ]);
        }

        Schema::table('sau_ct_list_check_resumen', function ($table) {
            $table->unsignedInteger('list_qualification_id')->nullable();

            $table->foreign('list_qualification_id')->references('id')->on('sau_ct_list_check_qualifications')->onUpdate('cascade')->onDelete('cascade');
        });*/

        $resumen = ListCheckResumen::get();

        foreach ($resumen as $key => $value) 
        {
            $qualification = ListCheckQualification::select('id')->where('contract_id', $value->contract_id)->first();

            if ($qualification)
                $value->update([
                    'list_qualification_id' => $qualification->id
                ]);
        }

        Schema::table('sau_ct_lisk_check_change_histories', function ($table) {
            $table->unsignedInteger('list_qualification_id')->nullable();

            $table->foreign('list_qualification_id')->references('id')->on('sau_ct_list_check_qualifications')->onUpdate('cascade')->onDelete('cascade');
        });

        $changeHistory = ListCheckChangeHistory::get();

        foreach ($changeHistory as $key => $value) 
        {
            $qualification = ListCheckQualification::select('id')->where('contract_id', $value->contract_id)->first();

            if ($qualification)
                $value->update([
                    'list_qualification_id' => $qualification->id
                ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sau_ct_file_item_contract', function ($table) {
            $table->dropForeign('sau_ct_file_item_contract_list_qualification_id_foreign');
            
            $table->dropColumn('list_qualification_id');
        });

        Schema::table('sau_ct_item_qualification_contract', function ($table) {
            $table->dropForeign('sau_ct_item_qualification_contract_list_qualification_id_foreign');

            $table->dropColumn('list_qualification_id');
        });

        Schema::table('sau_ct_list_check_resumen', function ($table) {
            $table->dropForeign('sau_ct_list_check_resumen_list_qualification_id_foreign');
            
            $table->dropColumn('list_qualification_id');
        });

        Schema::table('sau_ct_lisk_check_change_histories', function ($table) {
            $table->dropForeign('sau_ct_lisk_check_change_histories_list_qualification_id_foreign');

            $table->dropColumn('list_qualification_id');
        });
    }
}
