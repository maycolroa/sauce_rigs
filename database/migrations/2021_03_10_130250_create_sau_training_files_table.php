<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\LegalAspects\Contracts\Training;
use App\Models\LegalAspects\Contracts\TrainingFiles;

class CreateSauTrainingFilesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sau_ct_training_files', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('training_id');
            $table->string('name');
            $table->string('file');
            $table->timestamps();

            $table->foreign('training_id')->references('id')->on('sau_ct_trainings')->onUpdate('cascade')->onDelete('cascade');
        });

        $trainings = Training::whereNotNull('file')
        ->withoutGlobalScopes()->get();

        foreach ($trainings as $key => $value) 
        {
            $file = new TrainingFiles;
            $file->training_id = $value->id;
            $file->name = $value->name;
            $file->file = $value->file;
            $file->save();
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sau_ct_training_files');
    }
}
