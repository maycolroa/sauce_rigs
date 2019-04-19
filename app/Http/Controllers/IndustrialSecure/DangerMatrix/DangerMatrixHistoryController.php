<?php

namespace App\Http\Controllers\IndustrialSecure\DangerMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\IndustrialSecure\DangerMatrix\ChangeHistory;
use Session;

class DangerMatrixHistoryController extends Controller
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
        $histories = ChangeHistory::select(
            'sau_dm_change_histories.*',
            'sau_users.name as name'
        )
        ->join('sau_users', 'sau_users.id', 'sau_dm_change_histories.user_id');

        if ($request->has('modelId') && $request->get('modelId'))
            $histories->where('sau_dm_change_histories.danger_matrix_id', '=', $request->get('modelId'));

        return Vuetable::of($histories)
                    ->make();
    }
}
