<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\LegalMatrix\ArticleHistory;

class ArticleHistoryController extends Controller
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
        $histories = ArticleHistory::select(
            'sau_lm_article_histories.*',
            'sau_users.name as name'
        )
        ->join('sau_users', 'sau_users.id', 'sau_lm_article_histories.user_id')
        ->orderBy('sau_lm_article_histories.id', 'DESC');

        if ($request->has('modelId') && $request->get('modelId'))
            $histories->where('sau_lm_article_histories.article_id', '=', $request->get('modelId'));

        return Vuetable::of($histories)
                    ->make();
    }
}
