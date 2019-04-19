<?php

namespace App\Http\Controllers\IndustrialSecure\DangerMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\IndustrialSecure\DangerMatrix\Qualification;
use App\Models\IndustrialSecure\DangerMatrix\QualificationCompany;
use Session;

class QualificationController extends Controller
{
        /**
     * returns the ratings in the format accepted by the Vue component
     *
     * @return Array
     */
    public function getQualificationsComponent()
    {
        $conf = QualificationCompany::select('qualification_id')->first();
        $data = [];

        if ($conf)
        {
            if ($conf->qualification)
            {
                foreach ($conf->qualification->types as $type)
                {
                    $arr = [];
                    $arr["type_id"] = $type->id;
                    $arr["description"] = $type->description;

                    $values = [];
                    $help = '';

                    foreach ($type->values as $value)
                    {
                        $values[$value->value] = $value->id;

                        if ($value->description)
                            $help .= $value->value.'. '.$value->description."\n";
                    }

                    $arr["values"] = $this->multiSelectFormat(collect($values));
                    $arr["help"] = $help;

                    array_push($data, $arr);
                }
            }
        }

        return $data;
    }
}
