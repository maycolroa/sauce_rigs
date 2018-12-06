<?php

namespace App\Http\Controllers\Administrative\Employees;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Inform\Administrative\Employees\InformManagerEmployee;
use Session;

class EmployeeInformController extends Controller
{
    public function index()
    {
        return view('application');
    }

    /**
     * returns the inform data according to
     * multiple conditions, like filters
     *
     * @return \Illuminate\Http\Response
     */
    public function data(Request $request)
    {
        $employee_id = $request->employee_id;
        
        $informManager = new InformManagerEmployee($employee_id);
        
        return $this->respondHttp200($informManager->getInformData());
    }
}
