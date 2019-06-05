<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LegalAspects\LegalMatrix\LawType;

class LawTypeController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        $this->middleware('auth');
        /*$this->middleware('permission:activities_c', ['only' => 'store']);
        $this->middleware('permission:activities_r', ['except' =>'multiselect']);
        $this->middleware('permission:activities_u', ['only' => 'update']);
        $this->middleware('permission:activities_d', ['only' => 'destroy']);*/
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('application');
    }

    public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $lawtypes = LawType::select("id", "name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('name', 'like', $keyword);
                })
                ->take(30)->pluck('id', 'name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($lawtypes)
            ]);
        }
    }
}
