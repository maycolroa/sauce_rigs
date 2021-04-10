<?php

namespace App\Http\Controllers\System\Labels;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\General\Keyword;
use App\Http\Requests\System\Labels\LabelRequest;

class LabelController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:labels_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:labels_u, {$this->team}", ['only' => 'update']);
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

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function data(Request $request)
    {
        $labels = Keyword::select('*');

        return Vuetable::of($labels)
                    ->make();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $label = Keyword::findOrFail($id);
            $label->display_name = $this->getValueLocation($label, false);

            return $this->respondHttp200([
                'data' => $label,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\System\Labels\LabelRequest  $request
     * @param  Keyword  $label
     * @return \Illuminate\Http\Response
     */
    public function update(LabelRequest $request, Keyword $label)
    {
        $label->fill($request->all());
        $label->display_name = $this->getValueLocation($label);
        
        if (!$label->update())
            return $this->respondHttp500();
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el label'
        ]);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function multiselect(Request $request)
    {
        if($request->has('keyword'))
        {
            $keyword = "%{$request->keyword}%";
            $keywords = Keyword::select("id", "display_name")
                ->where(function ($query) use ($keyword) {
                    $query->orWhere('display_name', 'like', $keyword);
                })
                ->orderBy('display_name')
                ->take(50)->pluck('id', 'display_name');

            return $this->respondHttp200([
                'options' => $this->multiSelectFormat($keywords)
            ]);
        }
        else
        {
            $keywords = Keyword::selectRaw("
                sau_keywords.id as id,
                sau_keywords.display_name as display_name
            ")->pluck('id', 'display_name');
        
            return $this->multiSelectFormat($keywords);
        }
    }

    private function getValueLocation(Keyword $keyword, $concat = true)
    {
        $label = $keyword->display_name;

        if (in_array($keyword->id, [1,2,3,4,6,7,8,9]))
        {
            $label = trim(preg_replace('/^((1.)|(2.)|(3.)|(4.))/', "", $label));

            if ($concat)
            {
                if (in_array($keyword->id, [1,2]))
                    $label = "1. {$label}";

                if (in_array($keyword->id, [3,4]))
                    $label = "2. {$label}";

                if (in_array($keyword->id, [6,7]))
                    $label = "3. {$label}";

                if (in_array($keyword->id, [8,9]))
                    $label = "4. {$label}";
            }
        }

        return $label;
    }
}
