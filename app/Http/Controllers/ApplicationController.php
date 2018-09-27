<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Facades\Configuration;
use Illuminate\Support\Facades\Auth;
use Session;
use App\Administrative\License;

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

    /**
     * Returns an object with the applications and modules permissions 
     * according to the active licenses for the user in session
     *
     * @return array
     */
    public function appsWhithModules()
    {
      if(Auth::check())
      {
        $data = [];
        $licenses = License::whereRaw('? BETWEEN started_at AND ended_at', [date('Y-m-d')])->get();
        $arr_sub_mod = [];

        foreach ($licenses as $value)
        {
          $app = $value->module->application;
          $mod = $value->module;
          $arr_mod = [];

          if (!isset($data[$app->name]))
          {
            $data[$app->name]["display_name"] = $app->display_name;
            $data[$app->name]["image"]        = $app->image;
            $data[$app->name]["modules"]      = [];
          }

          $subMod_name = explode("/", $mod->name);
          $subMod_display_name = explode("/", $mod->display_name);

          if (COUNT($subMod_name) == 1) //Modulo
          {
            array_push($data[$app->name]["modules"], ["name"=>$mod->name, "display_name"=>$mod->display_name]);
          }
          else //Submodulo
          {
            if (!isset($arr_sub_mod[$app->name][$subMod_name[0]]))
            {
              array_push($data[$app->name]["modules"], ["name"=>$subMod_name[0], "display_name"=>$subMod_display_name[0], "subModules"=>[]]);
            }

            $arr_sub_mod[$app->name][$subMod_name[0]][] = [
              "name"=> $subMod_name[1], "display_name" => $subMod_display_name[1]
            ];
          }

          foreach ($data as $keyApp => $value)
          {
            foreach ($value["modules"] as $keyMod => $value2)
            {
              if (isset($value2["subModules"]))
              {
                $data[$keyApp]["modules"][$keyMod]["subModules"] = $arr_sub_mod[$keyApp][$value2["name"]];
              }
            }
          }
        }
        return $data;
        
        /*return [
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
        ];*/
      }

      return $this->respondHttp401();
    }
}