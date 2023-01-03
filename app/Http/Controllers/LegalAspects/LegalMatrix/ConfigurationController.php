<?php

namespace App\Http\Controllers\LegalAspects\LegalMatrix;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\LegalAspects\LegalMatrix\QualificationColorDinamic;

class ConfigurationController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        /*$this->middleware("permission:configurations_epp_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:configurations_epp_c, {$this->team}");*/
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
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\Administrative\Configuration\ConfigurationRequest $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        QualificationColorDinamic::updateOrCreate(
            [
                'company_id' => $this->company
            ],
            [
                'sin_calificar' => $request['sin_calificar'],
                'cumple' => $request['cumple'],
                'no_cumple' => $request['no_cumple'],
                'en_estudio' => $request['en_estudio'],
                'parcial' => $request['parcial'],
                'no_aplica' => $request['no_aplica'],
                'informativo' => $request['informativo']
            ]
        );

        $this->saveLogActivitySystem('Matriz legal - Configuracion', 'Se creo o edito la configuración');

        return $this->respondHttp200([
            'message' => 'Se actualizó la configuración'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        try
        {
            $data = QualificationColorDinamic::where('company_id', $this->company)->first();

            if (!$data)
            {
                $data = [
                    'sin_calificar' => '',
                    'cumple' => '',
                    'no_cumple' => '',
                    'en_estudio' => '',
                    'parcial' => '',
                    'no_aplica' => '',
                    'informativo' => ''
                ];
            }

            return $this->respondHttp200([
                'data' => $data
            ]);

        } catch(Exception $e){
            $this->respondHttp500();
        }
    }
}
