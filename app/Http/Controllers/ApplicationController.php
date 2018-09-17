<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Facades\Configuration;

class ApplicationController extends Controller
{
    public function index()
    {
        return view('application');
    }

    public function multiselect(Request $request){
      if($request->select){
        return $this->multiSelectFormat(Configuration::getConfiguration($request->select));
      }
      return $this->respondHttp500();
    }
}