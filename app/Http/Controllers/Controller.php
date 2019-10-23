<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Traits\UtilsTrait;
use App\Traits\ResponseTrait;
use App\Traits\PermissionTrait;
use App\Traits\LocationFormTrait;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Auth;
use App\Models\General\Team;
use Session;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, UtilsTrait, ResponseTrait, PermissionTrait, LocationFormTrait;

    protected $team;
    protected $company;
    protected $user;

    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $team = Team::where('name', Session::get('company_id'))->first();

        $this->team = $team ? $team->id : null;
        $this->company = Session::get('company_id');
        $this->user = Auth::user();
    }
}
