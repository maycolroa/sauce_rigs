<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use App\Models\Administrative\Employees\Employee;

class AddColumnInformationTokenSauEmployeeTrainigsRsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $employees = Employee::withoutGlobalScopes()->whereNull('token')->get();

        foreach ($employees as $key => $employee)
        {
            $token = Hash::make($employee->email.$employee->identification);
            $tok = str_replace("/", "a", $token);
            $token = $tok;

            $employee->forceFill([
                'token' => $token
            ])->save();
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
