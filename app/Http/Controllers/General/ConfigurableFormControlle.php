<?php

namespace App\Http\Controllers\General;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Traits\ConfigurableFormTrait;

class ConfigurableFormControlle extends Controller
{
    use ConfigurableFormTrait;

    public function formModel(Request $request)
    {
        return $this->getFormModel($request->key);
    }
}
