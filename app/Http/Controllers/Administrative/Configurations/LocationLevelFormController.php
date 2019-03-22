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
      $data = [];

      if ($request->has('application') && $request->has('module'))
      {
        $data = $this->getLocationFormConfModule($request->get('application'), $request->get('module'));
      }

      return $data;
    }
}
