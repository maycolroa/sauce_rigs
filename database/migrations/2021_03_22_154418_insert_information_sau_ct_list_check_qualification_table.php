<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\LegalAspects\Contracts\ListCheckQualification;

class InsertInformationSauCtListCheckQualificationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $lists_check = ItemQualificationContractDetail::select(
            'sau_ct_item_qualification_contract.contract_id',
            'sau_ct_information_contract_lessee.company_id')
        ->join('sau_ct_information_contract_lessee', 'sau_ct_information_contract_lessee.id', 'sau_ct_item_qualification_contract.contract_id')
        ->withoutGlobalScopes()
        ->groupBy('sau_ct_item_qualification_contract.contract_id', 'sau_ct_information_contract_lessee.company_id')
        ->get();

        foreach ($lists_check as $key => $value) 
        {
            $qualification = new ListCheckQualification;
            $qualification->contract_id = $value->contract_id;
            $qualification->company_id = $value->company_id;
            $qualification->user_id = 1;
            $qualification->validity_period = '2021-2022';
            $qualification->state = true;
            $qualification->save(); 
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
