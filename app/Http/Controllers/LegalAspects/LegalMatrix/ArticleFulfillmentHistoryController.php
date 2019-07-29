<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillmentHistory;

class ArticleFulfillmentHistoryController extends Controller
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
        $histories = ArticleFulfillmentHistory::select(
            'sau_lm_articles_fulfillment_histories.*',
            'sau_users.name as name'
        )
        ->join('sau_users', 'sau_users.id', 'sau_lm_articles_fulfillment_histories.user_id');

        if ($request->has('modelId') && $request->get('modelId'))
            $histories->where('sau_lm_articles_fulfillment_histories.fulfillment_id', '=', $request->get('modelId'));

        return Vuetable::of($histories)
                    ->make();
    }
}
