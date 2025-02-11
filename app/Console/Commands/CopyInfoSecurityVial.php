<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IndustrialSecure\RoadSafety\Training\Training;
use App\Models\IndustrialSecure\RoadSafety\Training\TrainingQuestions;

class CopyInfoSecurityVial extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'copy-info-security-vial';

    /**
     * The console command description.
     *
     * @var string     t998799
     */
    protected $description = 'Copiar informacion de preguntas de capacitaciones de seguridad vial de una empresa a otra';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $training_origin = Training::where('company_id', 24)->withoutGlobalScopes()->get();

        foreach ($training_origin as $key => $training) 
        {
            $new = Training::withoutGlobalScopes()->firstOrCreate(
                [
                    'name' => $training->name,
                    'company_id' => 130
                ], 
                [
                    'company_id' => 130,
                    'name' => $training->name,
                    'creator_user' => 2360,
                    'modifier_user' => 2360,
                    'number_questions_show' => $training->number_questions_show,
                    'min_calification' => $training->min_calification,
                    'max_calification' => $training->max_calification,
                    'number_attemps' => $training->number_attemps,
                    'active' => $training->active
                ]
            );

            foreach ($training->questions as $key => $question) 
            {
                $new_q = TrainingQuestions::firstOrCreate(
                    [
                        'training_id' => $new->id,
                        'description' => $question->description
                    ],
                    [
                        'training_id' => $new->id,
                        'description' => $question->description,
                        'type_question_id' => $question->type_question_id,
                        'answer_options' => $question->answer_options,
                        'value_question' => $question->value_question
                    ]
                );
            }
        }
    }
}
