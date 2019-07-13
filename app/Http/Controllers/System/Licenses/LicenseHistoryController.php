<?php

namespace App\Http\Controllers\System\Licenses;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\System\Licenses\LicenseHistory;

class LicenseHistoryController extends Controller
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
        $histories = LicenseHistory::select(
            'sau_license_histories.*',
            'sau_users.name as name'
        )
        ->join('sau_users', 'sau_users.id', 'sau_license_histories.user_id');

        if ($request->has('modelId') && $request->get('modelId'))
            $histories->where('sau_license_histories.license_id', '=', $request->get('modelId'));

        return Vuetable::of($histories)
                    ->make();
    }
}
