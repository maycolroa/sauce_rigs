<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\PreventiveOccupationalMedicine\Reinstatements\Check;

class CorrectRecordsChecksChiaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $values = [];

        $checks = Check::whereRaw('cie10_code_2_id is null and disease_origin_2 is not null')->withoutGlobalScopes()->get();

        foreach ($checks as $key => $value) 
        {
            $content = [
                'cie10_code_2_id' => $value->cie10_code_3_id,
                'cie10_code_3_id' => $value->cie10_code_4_id,
                'cie10_code_4_id' => $value->cie10_code_5_id,
                'cie10_code_5_id' => NULL,
            ];

            $value->update($content);

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
