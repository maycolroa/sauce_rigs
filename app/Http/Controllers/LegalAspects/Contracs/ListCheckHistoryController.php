<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\Contracts\ListCheckChangeHistory;
use App\Models\LegalAspects\Contracts\ListCheckQualification;

class ListCheckHistoryController extends Controller
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
        $histories = ListCheckChangeHistory::select(
            'sau_ct_lisk_check_change_histories.*',
            'sau_users.name as name'
        )
        ->join('sau_users', 'sau_users.id', 'sau_ct_lisk_check_change_histories.user_id');

        if ($request->has('modelId') && $request->get('modelId'))
        {
            $histories->where('sau_ct_lisk_check_change_histories.list_qualification_id', '=', $request->get('modelId'));
        }

        return Vuetable::of($histories)
                    ->make();
    }
}
