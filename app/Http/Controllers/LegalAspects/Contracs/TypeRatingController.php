<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\LegalAspects\Contracts\TypeRating;
use App\Http\Requests\LegalAspects\Contracts\TypeRatingRequest;

class TypeRatingController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:contracts_typesQualification_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:contracts_typesQualification_r, {$this->team}", ['except' =>'getAllTypesRating']);
        $this->middleware("permission:contracts_typesQualification_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:contracts_typesQualification_d, {$this->team}", ['only' => 'destroy']);
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
        $types = TypeRating::select('*');

        return Vuetable::of($types)
                    ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\TypesRating\TypeRatingRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(TypeRatingRequest $request)
    {
        $rating = new TypeRating($request->all());
        $rating->company_id = $this->company;

        $this->saveLogActivitySystem('Contratistas - Evaluaciones', 'Se creo el proceso a evaluar '.$rating->name);
        
        if(!$rating->save()){
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el tipo de calificación'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try
        {
            $typeRating = TypeRating::findOrFail($id);

            return $this->respondHttp200([
                'data' => $typeRating,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\LegalAspects\TypesRating\TypeRatingRequest $request
     * @param  App\LegalAspects\TypeRating $typeRating
     * @return \Illuminate\Http\Response
     */
    public function update(TypeRatingRequest $request, TypeRating $typeRating)
    {
        $typeRating->fill($request->all());
        
        if(!$typeRating->update()){
          return $this->respondHttp500();
        }

        $this->saveLogActivitySystem('Contratistas - Evaluaciones', 'Se edito el proceso a evaluar '.$rating->name);
        
        return $this->respondHttp200([
            'message' => 'Se actualizo el tipo de calificación'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  App\LegalAspects\TypeRating $typeRating
     * @return \Illuminate\Http\Response
     */
    public function destroy(TypeRating $typeRating)
    {
        if (count($typeRating->items) > 0)
        {
            return $this->respondWithError('No se puede eliminar el tipo de calificación porque hay items asociados a el');
        }

        $this->saveLogActivitySystem('Contratistas - Evaluaciones', 'Se elimino el proceso a evaluar '.$rating->name);

        if(!$typeRating->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el tipo de calificación'
        ]);
    }

    public function getAllTypesRating()
    {
        $types = TypeRating::get();
        return $types;
    }

    public function multiselect(Request $request)
    {
        
        $qualificationtypes = TypeRating::select(
            'sau_ct_types_ratings.id as id',
            'sau_ct_types_ratings.name as name'
        )
        ->pluck('id', 'name');
    
        return $this->multiSelectFormat($qualificationtypes);
    }
}
