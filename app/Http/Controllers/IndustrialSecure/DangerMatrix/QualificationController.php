<?php

namespace App\Http\Controllers\IndustrialSecure\DangerMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\IndustrialSecure\DangerMatrix\Qualification;
use App\Models\IndustrialSecure\DangerMatrix\QualificationCompany;
use App\Traits\DangerMatrixTrait;

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

        if (!$conf)
        {
            $conf = Qualification::where('name', $this->getDefaultCalificationDm())->first();
        }
        else
            $conf = $conf->qualification;

        $data['type'] = $conf->name;
        $data['matriz_calification'] = $this->getMatrixCalification($conf->name);

        foreach ($conf->types as $type)
        {
            $arr = [];
            $arr["type_id"] = $type->id;
            $arr["description"] = $type->description;
            $arr["type_input"] = $type->type_input;
            $arr["grouped"] = $type->grouped;
            $arr["readonly"] = $type->readonly;

            $values = [];
            $help = '';

            if ($type->type_input == 'select')
            {
                if ($type->grouped == 'NO')
                {
                    foreach ($type->values as $value)
                    {
                        $values[$value->value] = $value->value;

                        if ($value->description)
                            $help .= $value->value.'. '.$value->description."\n";
                    }

                    $values = $this->multiSelectFormat(collect($values));
                }
                else
                {
                    $help_list = [];

                    foreach ($type->values as $value)
                    {
                        $values[$value->group_by][$value->value] = $value->value;

                        if ($value->description)
                            $help_list[$value->group_by][] = $value->value.'. '.$value->description."\n";
                    }

                    foreach ($values as $index => $value)
                    {
                        $values[$index] = $this->multiSelectFormat(collect($value));
                        $help_list[$index] = implode("\n", $help_list[$index]);
                    }

                    $help = $help_list;
                }
            }

            $arr["values"] = $values;
            $arr["help"] = $help;

            array_push($data['data'], $arr);
        }

        return $data;
    }
}
