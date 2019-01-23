<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Session;

class MultiSelectController extends Controller
{
    /**
     * returns the possible states of activities
     *
     * @return Array
     */
    public function actionPlanStates()
    {
        return $this->multiSelectFormat($this->getActionPlanStates());
    }
}