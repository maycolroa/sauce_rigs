<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Traits\ResponseTrait;
use Auth;

class ApiController extends Controller
{
	use ResponseTrait;
	
    protected $user;

    public function __construct()
    {
        $this->user = Auth::guard('api')->user();
    }

    public function inforCompanyComplete($company_id)
    {
        $infor_company = Company::find($company_id);

        if (isset($infor_company->nombre_actividad_economica_sede_principal) && $infor_company->nombre_actividad_economica_sede_principal)
        {
            return true;
        }
        else
            return false;
    }

}
