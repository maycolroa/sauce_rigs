<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\Contracts\EvaluationContractHistory;

class EvaluationContractHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $histories = EvaluationContractHistory::select(
            'sau_ct_evaluation_contract_histories.*',
            'sau_users.name as name'
        )
        ->join('sau_users', 'sau_users.id', 'sau_ct_evaluation_contract_histories.user_id');

        if ($request->has('modelId') && $request->get('modelId'))
            $histories->where('sau_ct_evaluation_contract_histories.evaluation_id', '=', $request->get('modelId'));

        return Vuetable::of($histories)
                    ->make();
    }
}
