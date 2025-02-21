<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\IndustrialSecure\RoadSafety\Driver;
use App\Models\Administrative\Employees\Employee;
use App\Models\IndustrialSecure\RoadSafety\TagsResponsibles;

class ChangeInformationResponsibleSauRsDriversTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $drivers = Driver::get();

        foreach ($drivers as $key => $driver) 
        {
            $responsible = Employee::withoutGlobalScopes()->find($driver->responsible_id);

            $driver->responsible = $responsible->name;
            $driver->save();

            $tag = new TagsResponsibles;
            $tag->name = $responsible->name;
            $tag->company_id = $responsible->company_id;
            $tag->save();
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
