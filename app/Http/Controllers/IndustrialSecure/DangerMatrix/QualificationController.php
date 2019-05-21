<?php

namespace App\Http\Controllers\IndustrialSecure\DangerMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\IndustrialSecure\DangerMatrix\Qualification;
use App\Models\IndustrialSecure\DangerMatrix\QualificationCompany;
use App\Traits\DangerMatrixTrait;
use Session;

class QualificationController extends Controller
{
    use DangerMatrixTrait;
    
        /**
     * returns the ratings in the format accepted by the Vue component
     *
     * @return Array
     */
    public function getQualificationsComponent()
    {
        $conf = QualificationCompany::select('qualification_id')->first();
        $data = [
            'type' => '',
            'data' => []
        ];

        if ($conf)
        {
            if ($conf->qualification)
            {
                $data['type'] = $conf->qualification->name;
                $data['matriz_calification'] = $this->getMatrixCalification($conf->qualification->name);

                foreach ($conf->qualification->types as $type)
                {
                    $arr = [];
                    $arr["type_id"] = $type->id;
                    $arr["description"] = $type->description;
                    $arr["type_input"] = $type->type_input;
                    $arr["readonly"] = $type->readonly;

                    $values = [];
                    $help = '';

                    if ($type->type_input == 'select')
                    {
                        foreach ($type->values as $value)
                        {
                            $values[$value->value] = $value->value;

                            if ($value->description)
                                $help .= $value->value.'. '.$value->description."\n";
                        }
                    }

                    $arr["values"] = $this->multiSelectFormat(collect($values));
                    $arr["help"] = $help;

                    array_push($data['data'], $arr);
                }
            }
        }

        return $data;
    }
}
