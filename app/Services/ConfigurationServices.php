<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\DB;

class ConfigurationServices
{
    /**
     * responds value configuration 
     * @param  String  $Key
     * @return String
     */
    public function getConfiguration($key)
    {
        
        $configuration = DB::table('sau_configuration')->select('value')->where('key',$key)->get();
        
        if($configuration->count() == 0){
          throw new Exception('Parameter not found in configuration table');
        }

        $value = json_decode($configuration[0]->value);
        if(gettype($value) == "object" || gettype($value) == 'array'){
          return $value;
        }else{
          return $configuration[0]->value;
        }
    }
}