<?php

namespace App\Http\Controllers\LegalAspects;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use App\Http\Requests\LegalApects\Contracts\ContractRequest;
use App\Http\Requests\LegalApects\Contracts\ContractCompleteInfoRequest;
use App\Http\Requests\LegalApects\Contracts\ListCheckItemsContractRequest;
use App\Models\LegalAspects\ContractLesseeInformation;
use App\Models\LegalAspects\SectionCategoryItems;
use App\Models\LegalAspects\Qualifications;
use App\User;
use App\Models\Role;
use App\Traits\UserTrait;
use Session;
use Illuminate\Support\Facades\Auth;

class ContractLesseeController extends Controller
{
    use UserTrait;
    /**
     * Display index.
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
        $user = User::find(Auth::user()->id);
        if (count($user->contractInfo) > 0) {
            $sql = SectionCategoryItems::select(
                'sau_ct_section_category_items.*',
                'sau_ct_standard_classification.standard_name as name'
            )
            ->join('sau_ct_items_standard', 'sau_ct_items_standard.item_id', 'sau_ct_section_category_items.id')
            ->join('sau_ct_standard_classification', 'sau_ct_standard_classification.id', 'sau_ct_items_standard.standard_id');
            if ($user->contractInfo[0]->classification == "upa" && $user->contractInfo[0]->number_workers <= 10 && $user->contractInfo[0]->risk_class == "Clase de riesgo I, II y III") {
                return $sql->where('sau_ct_standard_classification.standard_name', '=', '3 estandares')->get();
            } else if ($user->contractInfo[0]->classification == "empresa" && $user->contractInfo[0]->number_workers <= 10 && $user->contractInfo[0]->risk_class == "Clase de riesgo I, II y III") {
                return $sql->where('sau_ct_standard_classification.standard_name', '=', '7 estandares')->get();
            } else if ($user->contractInfo[0]->classification == "empresa" && $user->contractInfo[0]->number_workers > 10 && $user->contractInfo[0]->number_workers <= 50 && $user->contractInfo[0]->risk_class == "Clase de riesgo I, II y III") {
                return $sql->where('sau_ct_standard_classification.standard_name', '=', '21 estandares')->get();
            } else if ($user->contractInfo[0]->classification == "empresa" && $user->contractInfo[0]->number_workers > 10 && $user->contractInfo[0]->number_workers <= 50 && $user->contractInfo[0]->risk_class == "Clase de riesgo IV y V") {
                return $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            } else if ($user->contractInfo[0]->classification == "upa" && $user->contractInfo[0]->number_workers <= 10 && $user->contractInfo[0]->risk_class == "Clase de riesgo IV y V") {
                return $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            } else if ($user->contractInfo[0]->classification == "empresa" && $user->contractInfo[0]->number_workers > 50) {
                return $sql->where('sau_ct_standard_classification.standard_name', '=', '60 estandares')->get();
            }
        } else {
            return $this->respondHttp500([
                'message' => 'El usuario auntenticado no tiene una información de contratistas'
            ]);
        }
    }

    
    public function store(ContractRequest $request)
    {   
        $user = $this->createUser($request);
        $contractLesseeInformation = new ContractLesseeInformation;

        if ($user == $this->respondHttp500() || $user == null) {
             return $this->respondHttp500();
        }

        $user->companies()->sync(Session::get('company_id'));
        $role_id = Role::where('name', '=', $request->role)->pluck("id");
        $user->syncRoles([$role_id[0]]);
        $contractLesseeInformation->company_id = Session::get('company_id');
        $contractLesseeInformation->nit = $request->nit;
        $contractLesseeInformation->type = $request->role;
        $contractLesseeInformation->classification = $request->classification;
        $contractLesseeInformation->business_name = $request->name_business;
        $contractLesseeInformation->social_reason = $request->social_reason;
        if ($request->high_risk) { $contractLesseeInformation->high_risk_work = 1; }
        if (!$contractLesseeInformation->save()) {  return $this->respondHttp500(); }

        $user->contractInformation()->sync($contractLesseeInformation);

        return $this->respondHttp200([
            'message' => 'Se creo el usuario'
        ]);
        
    }

    /**
     * Update the given user.
     *
     * @param  Request  $request
     * @param  string  $id
     * @return Response
     */
    public function update(ContractCompleteInfoRequest $request, $id)
    {
        $user = User::find($id);
        $contractInfoComplete = $user->contractInfo[0]->fill($request->all());

        if(!$contractInfoComplete->save()){
            return $this->respondHttp500();
        }
  
        return $this->respondHttp200([
            'message' => 'Se actualizo la información'
        ]);
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function qualifications(Request $request)
    {
        $qualifications = new Qualifications;
        return $qualifications->pluck("description","id");
    }

    /**
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function saveQualificationItems(ListCheckItemsContractRequest $request)
    {
        // $user = User::find(Auth::user()->id);
        // $contract_id = $user->contractInfo[0]->id;
        // foreach ($request->items_calificated as $items) {
        //     foreach ($items as $item) {
        //         $item['item_id'] = $item['id'];
        //         $item['qualification_id'] = $item['qualification'];
        //         $item['contract_id'] = $contract_id;
        //         $user->itemsCalificatedContract()->sync($item);
        //     }
        // }
        die($request);
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
            $contract = ContractLesseeInformation::findOrFail($id);

            return $this->respondHttp200([
                'data' => $contract,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    public function multiselect(Request $request)
    {
        $keyword = "%{$request->keyword}%";
        $contracts = ContractLesseeInformation::selectRaw("
            sau_ct_information_contract_lessee.id as id,
            sau_ct_information_contract_lessee.nit as nit
        ")
        ->where(function ($query) use ($keyword) {
            $query->orWhere('nit', 'like', $keyword);
        })
        ->take(30)->pluck('id', 'nit');
        return $this->respondHttp200([
            'options' => $this->multiSelectFormat($contracts)
        ]);
    }
}
