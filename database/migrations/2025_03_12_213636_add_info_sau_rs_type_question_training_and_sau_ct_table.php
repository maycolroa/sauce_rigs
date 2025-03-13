<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\IndustrialSecure\RoadSafety\Training\TrainingTypeQuestion;
use App\Models\LegalAspects\Contracts\TrainingTypeQuestion AS TrainingTypeQuestionLegalAspects;

class AddInfoSauRsTypeQuestionTrainingAndSauCtTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $dataRs = [
            [
                'name' => 'selection_simple',
                'description' => 'Selección simple'
            ],
            [
                'name' => 'true_false',
                'description' => 'Verdadero y Falso'
            ],
            [
                'name' => 'selection_multiple',
                'description' => 'Selección múltiple'
            ],
            [
                'name' => 'pairing',
                'description' => 'Apareamiento'
            ]
        ];

        foreach ($dataRs as $key => $value) 
        {
            TrainingTypeQuestion::firstOrCreate($value);
        }

        $dataCt = [
            [
                'name' => 'free_text',
                'description' => 'Texto libre',
                'company_id' => 702
            ],
            [
                'name' => 'free_text_image',
                'description' => 'Imagen - Descripción',
                'company_id' => 702
            ],
            [
                'name' => 'pairing_image',
                'description' => 'Apareamiento - Imagen',
                'company_id' => 702
            ],
        ];

        foreach ($dataCt as $key => $value) 
        {
            TrainingTypeQuestionLegalAspects::firstOrCreate($value);
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
