<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\IndustrialSecure\RoadSafety\Driver;
use App\Models\IndustrialSecure\RoadSafety\DriverDocument;
use App\Models\IndustrialSecure\RoadSafety\Position;
use App\Models\Administrative\Employees\Employee;

class AddInfoSauRsDriverDocumentsPoditionDocuemntIdTable extends Migration
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
            $employee = Employee::withoutGlobalScopes()->find($driver->employee_id);

            $positionRs = Position::withoutGlobalScopes()->where('employee_position_id', $employee->employee_position_id)->first();

            if ($positionRs)
            {
                foreach ($positionRs->documents as $key2 => $document) 
                {
                    $driverDocument = DriverDocument::where('driver_id', $driver->id)->where('name', $document->name)->get();

                    if ($driverDocument)
                    {
                        foreach ($driverDocument as $key => $value) 
                        {
                            $value->position_document_id = $document->id;
                            $value->save();
                        }
                    }
                }
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
