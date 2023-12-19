<?php

namespace App\Http\Controllers\System\Helpers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\System\Helpers\Helper;
use App\Models\System\Helpers\HelperFile;
use App\Http\Requests\System\Helpers\HelperRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Validator;
use DB;


class HelperController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:helpers_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:helpers_u, {$this->team}", ['only' => 'update']);
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
        $helpers = Helper::select(
            'sau_helpers.*', 
            'sau_users.name AS user', 
            'sau_modules.display_name AS module'
        )
        ->join('sau_users', 'sau_users.id', 'sau_helpers.user_id')
        ->join('sau_modules', 'sau_modules.id', 'sau_helpers.module_id')
        ->orderBy('sau_helpers.id', 'DESC');

        return Vuetable::of($helpers)
                    ->make();
    }

    public function store(HelperRequest $request)
    {
        Validator::make($request->all(), [
            "files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value))
                    {
                        $ext = strtolower($value->getClientOriginalExtension());
                        
                        if ($ext != 'pdf'  && $ext != 'mp4' && $ext != 'pptx' && $ext != 'ppt')
                        {
                            $fail('Archivo debe ser un pdf, mp4, ppt, pptx');
                        }
                    }
                }

            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $helper = new Helper($request->all());
            $helper->user_id = $this->user->id;

            if (!$helper->save())
                return $this->respondHttp500();

            $this->saveFile($helper, $request->get('files'));

            $this->saveLogActivitySystem('Sistemas - Ayudas', 'Se creo la ayuda '.$helper->title);

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
            //return $e->getMessage();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la ayuda'
        ]);
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
            $helper = Helper::findOrFail($id);

            $helper->multiselect_module = $helper->module->multiselect();

            $helper->files = $this->getFiles($helper->id);

            return $this->respondHttp200([
                'data' => $helper,
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
    public function update(HelperRequest $request, Helper $helper)
    {
        Validator::make($request->all(), [
            "files.*.file" => [
                function ($attribute, $value, $fail)
                {
                    if ($value && !is_string($value))
                    {
                        $ext = strtolower($value->getClientOriginalExtension());
                        
                        if ($ext != 'pdf'  && $ext != 'mp4' && $ext != 'pptx' && $ext != 'ppt')
                        {
                            $fail('Archivo debe ser un pdf, mp4, ppt, pptx');
                        }
                    }
                }

            ]
        ])->validate();

        $helper->fill($request->all());

        $this->saveFile($helper, $request->get('files'));

        if (!$helper->update())
            return $this->respondHttp500();

        $this->saveLogActivitySystem('Sistemas - Ayudas', 'Se actualizo la ayuda '.$helper->title);
        
        return $this->respondHttp200([
            'message' => 'Se actualizo la ayuda'
        ]);
    }

    /**
     * Returns an array for a select type input
     *
     * @param Request $request
     * @return Array
     */

    public function getFiles($helper)
    {
        $get_files = HelperFile::where('helper_id', $helper)->get();

        $files = [];

        if ($get_files->count() > 0)
        {               
            $get_files->transform(function($get_file, $index) {
                $get_file->key = Carbon::now()->timestamp + rand(1,10000);
                $get_file->name = $get_file->name;
                $get_file->old_name = $get_file->file;

                return $get_file;
            });

            $files = $get_files;
        }

        return $files;
    }

    public function saveFile($helper, $files)
    {
        if ($files && count($files) > 0)
        {
            $files_names_delete = [];

            foreach ($files as $keyF => $value) 
            {
                $create_file = true;

                if (isset($value['id']))
                {
                    $fileUpload = HelperFile::findOrFail($value['id']);

                    if ($value['old_name'] == $value['file'] )
                        $create_file = false;
                }
                else
                {
                    $fileUpload = new HelperFile();                    
                    $fileUpload->helper_id = $helper->id;
                    $fileUpload->name = $value['name'];
                }

                if ($create_file)
                {
                    $file_tmp = $value['file'];
                    $nameFile = base64_encode($this->user->id . now() . rand(1,10000) . $keyF) .'.'. $file_tmp->extension();
                    //Storage::disk('s3')->put('system/helpers/files/'. $nameFile, $file_tmp, 'public');
                    $file_tmp->storeAs($fileUpload->path_client(false), $nameFile, 's3');
                    $fileUpload->file = $nameFile;
                }

                if (!$fileUpload->save())
                    return $this->respondHttp500();
            }

            //Borrar archivos reemplazados
            foreach ($files_names_delete as $keyf => $file)
            {
                Storage::disk('s3')->delete($fileUpload->path_client(false)."/".$file);
            }
        }
    }

    public function download($id)
    {
        $helper = HelperFile::find($id);
        return Storage::disk('s3')->download($helper->path_client(false)."/". $helper->file, $helper->name);
    }

    public function destroy(Helper $helper)
    {
        $fileBk = $helper->replicate();

        $file_delete = HelperFile::where('helper_id', $helper->id)->get();

        if ($file_delete)
        {
            foreach ($file_delete as $keyf => $file)
            {
                $path = $file->file;
                Storage::disk('s3')->delete('system/helpers/files/'. $path);
            }
        }

        $this->saveLogActivitySystem('Sistemas - Ayudas', 'Se elimino la ayuda '.$fileBk->title);

        if (!$helper->delete())
            return $this->respondHttp500();
        
        return $this->respondHttp200([
            'message' => 'Se elimino la ayuda'
        ]);
    }        
}
