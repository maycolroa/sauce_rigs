<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Facades\Configuration;
use Illuminate\Support\Facades\Auth;
use Session;

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

    public function appsWhithModules()
    {
      if(Auth::check())
      {
        $companies = Auth::user()->companies;

        dd($companies);
        return [
            "IndustrialHygiene" => [
                "display_name" => "Higiene Industrial",
                "image" => "IndustrialHygiene",
                "modules" => [
                  [
                    "name"=>"linkDirecto", 
                    "display_name"=>"Link Directo"
                  ]
                ]
            ],
            "PreventiveOccupationalMedicine" => [
                "display_name" => "Medicina Laboral y Preventiva",
                "image" => "PreventiveOccupationalMedicine",
                "modules" => [
                    [
                      "name"=>"BiologicalMonitoring", 
                      "display_name"=>"Monitoreo Biológico",
                      "subModules" => [
                        ["name"=>"Audiometry", "display_name"=>"Audiometrias"],
                        ["name"=>"Spirometry", "display_name"=>"Espirometrias"]
                      ]
                    ],
                    [
                      "name"=>"MedicalConcepts", 
                      "display_name"=>"Conceptos Medicos",
                      "subModules" => [
                        ["name"=>"SubMo1", "display_name"=>"Sub Modulo 1"],
                        ["name"=>"SubMo2", "display_name"=>"Sub Modulo 2"]
                      ]
                    ],
                    [
                      "name"=>"linkDirecto", 
                      "display_name"=>"Link Directo"
                    ]
                ]
            ],
            "LegalAspects" => [
                "display_name" => "Aspectos Legales",
                "image" => "LegalAspects",
                "modules" => [
                  [
                    "name"=>"linkDirecto", 
                    "display_name"=>"Link Directo"
                  ]
                ]
            ],
            "TrainingQualification" => [
                "display_name" => "Formación y Capacitación",
                "image" => "TrainingQualification",
                "modules" => [
                ]
            ]
        ];
      }

      return $this->respondHttp401();
    }
}