<?php

namespace App\Http\Controllers\LegalAspects;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\LegalAspects\ContractLessee;

class ContractLesseeController extends Controller
{
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
            $contract = ContractLessee::findOrFail($id);

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
        $contracts = ContractLessee::selectRaw("
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
