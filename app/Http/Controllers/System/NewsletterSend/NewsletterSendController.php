<?php

namespace App\Http\Controllers\System\NewsletterSend;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Vuetable\Facades\Vuetable;
use App\Models\Administrative\Users\User;
use App\Models\Administrative\Roles\Role;
use App\Models\General\NewsletterSend;
use App\Models\General\Team;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\System\NewsletterSend\NewsletterSendRequest;
use DB;
use Validator;


class NewsletterSendController extends Controller
{
    /**
     * creates and instance and middlewares are checked
     */
    function __construct()
    {
        parent::__construct();
        $this->middleware('auth');
        $this->middleware("permission:newsletterSend_c, {$this->team}", ['only' => 'store']);
        $this->middleware("permission:newsletterSend_r, {$this->team}", ['except' =>'multiselect']);
        $this->middleware("permission:newsletterSend_u, {$this->team}", ['only' => 'update']);
        $this->middleware("permission:newsletterSend_d, {$this->team}", ['only' => 'destroy']);
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
        $newsletters = NewsletterSend::select(
            '*',
            DB::raw("case when sau_newsletters_sends.active is true then 'SI' else 'NO' end as active_send"),
            DB::raw("case when sau_newsletters_sends.send is true then 'SI' else 'NO' end as send_2")
        );

        return Vuetable::of($newsletters)
            ->addColumn('switchStatus', function ($newsletter) {
                return true; 
            })
            ->addColumn('system-newslettersend-edit', function ($newsletter) {
                if ($newsletter->active || $newsletter->send)
                    return false; 
                else
                    return true;
            })
            ->make();
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\NewsletterSendRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(NewsletterSendRequest $request)
    {
        Validator::make($request->all(), [
            "image" => [
                function ($attribute, $value, $fail)
                {
                    if (!is_string($value) && $value->getClientMimeType() != 'image/png')
                        $fail('Imagen debe ser PNG');
                },
            ]
        ])->validate();

        DB::beginTransaction();
        
        try
        {
            $newsletterSend = new NewsletterSend;
            $newsletterSend->subject = $request->subject;

            $file = $request->image;
            $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->extension();
            $file->storeAs('newsletters/', $nameFile, 's3');
            $newsletterSend->image = $nameFile;
            $newsletterSend->image_name = $file->getClientOriginalName();
        
            if (!$newsletterSend->save())
                return $this->respondHttp500();

            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo el boletin'
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
            $newsletterSend = NewsletterSend::findOrFail($id);
            $newsletterSend->path = Storage::disk('s3')->url('newsletters/'. $newsletterSend->image);

            return $this->respondHttp200([
                'data' => $newsletterSend,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  App\Http\Requests\IndustrialSecure\Activities\NewsletterSendRequest  $request
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function update(NewsletterSendRequest $request, NewsletterSend $newsletterSend)
    {
        Validator::make($request->all(), [
            "image" => [
                function ($attribute, $value, $fail)
                {
                    if (!is_string($value) && $value->getClientMimeType() != 'image/png')
                        $fail('Imagen debe ser PNG');
                },
            ]
        ])->validate();

        DB::beginTransaction();

        try
        {
            $file = $request->image;
            Storage::disk('s3')->delete('newsletters/'. $newsletterSend->image);
            $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $file->extension();
            $file->storeAs('newsletters/', $nameFile, 's3');
            $newsletterSend->image = $nameFile;
            $newsletterSend->image_name = $file->getClientOriginalName();
            $newsletterSend->subject = $request->subject;
           
            if(!$newsletterSend->update())
                return $this->respondHttp500();
                
            DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo el boletin'
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Company  $company
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $newsletterSend = NewsletterSend::findOrFail($id);
        
        if(!$newsletterSend->delete())
        {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se elimino el boletin'
        ]);
    }

    /**
     * toggles the check state between open and close
     * @param  Company $company
     * @return \Illuminate\Http\Response
     */

    public function toggleState(NewsletterSend $newsletter)
    {
        $data = ['active' => !$newsletter->active];

        if (!$newsletter->update($data)) {
            return $this->respondHttp500();
        }
        
        return $this->respondHttp200([
            'message' => 'Se cambio el estado del boletin'
        ]);
    }

    public function download($id)
    {
        $newsletter = NewsletterSend::find($id);
        return Storage::disk('s3')->download('newsletters/'. $newsletter->image, $newsletter->image_name);
    }
}
