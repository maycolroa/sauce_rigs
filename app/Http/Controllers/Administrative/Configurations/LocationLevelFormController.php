<?php

namespace App\Http\Controllers\Administrative\Configurations;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LocationLevelFormController extends Controller
{
    /**
     * returns the configuration for a specific module
     *
     * @return Array
     */
    public function getConfModule(Request $request)
    {
      return $this->getLocationFormConfModule();
    }

    public function getConfUser(Request $request)
    {
      return $this->getLocationFormConfUser($this->company);
    }
}
