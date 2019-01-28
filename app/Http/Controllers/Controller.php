<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use App\Traits\UtilsTrait;
use App\Traits\ResponseTrait;
use App\Traits\PermissionTrait;
use App\Traits\LocationFormTrait;
use App\Traits\ActionPlanTrait;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests, UtilsTrait, ResponseTrait, PermissionTrait, LocationFormTrait, ActionPlanTrait;
}
