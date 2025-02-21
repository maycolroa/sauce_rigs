<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\FileModuleState;

class AddInforModuleSauCtFileUploadLessesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $files = FileUpload::withoutGlobalScopes()->whereNull('module')->limit(5000)->get();

        foreach ($files as $key => $file) 
        {
            $module = FileModuleState::where('file_id', $file->id)->first();

            if ($module)
            {
                $file->module = $module->module;
                $file->save();
            }
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
