<?php

namespace App\Http\Controllers\LegalAspects\Contracs;

use Illuminate\Http\Request;
use App\Vuetable\Facades\Vuetable;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Administrative\Roles\Role;
use App\Models\LegalAspects\Contracts\CompanyLimitCreated;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Http\Requests\LegalAspects\Contracts\ContractRequest;
use App\Traits\ContractTrait;
use App\Traits\UserTrait;
use Session;
use DB;

class ContractLesseeController extends Controller
{
    use UserTrait;
    use ContractTrait;

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
        $contracts = ContractLesseeInformation::select('*');

        return Vuetable::of($contracts)
            ->make();
    }

    public function store(ContractRequest $request)
    {   
        DB::beginTransaction();

        try
        {
            if (!$this->checkLimit())
                return $this->respondWithError('Límite alcanzado..!! No puede crear más contratistas o arrendatarios hasta que inhabilite alguno de ellos.');

            $contract = new ContractLesseeInformation($request->all());
            $contract->company_id = Session::get('company_id');

            if (!$request->high_risk_work)
                $contract->high_risk_work = 'NO';

            if(!$contract->save()) {
                return $this->respondHttp500();
            }

            $user = $this->createUser($request);

            if ($user == $this->respondHttp500() || $user == null) {
                return $this->respondHttp500();
            }

            $user->syncRoles([$this->getIdRole($request->type)]);
            $contract->users()->sync($user);

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            //return $e->getMessage();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se creo la contratista'
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
            $contract = ContractLesseeInformation::findOrFail($id);

            return $this->respondHttp200([
                'data' => $contract,
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getInformation()
    {
        try
        {
            $contract = $this->getContractUser(Auth::user()->id);
            $contract->isInformation = true;

            return $this->respondHttp200([
                'data' => $contract
            ]);
        } catch(Exception $e){
            $this->respondHttp500();
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param App\Http\Requests\LegalAspects\Contracts\ContractRequest $request
     * @param App\Models\LegalAspects\Contracts\ContractLesseeInformation $typeRating
     * @return \Illuminate\Http\Response
     */
    public function update(ContractRequest $request, ContractLesseeInformation $contract)
    {
        DB::beginTransaction();

        try
        {
            if ($request->active == 'SI' && ($request->active != $contract->active))
            {
                if (!$this->checkLimit())
                    return $this->respondWithError('Límite alcanzado..!! No puede habilitar esta contratista o arrendatario hasta que inhabilite alguno de ellos.');
            }

            $contract->fill($request->all());
            
            if ($request->type == 'Arrendatario')
                $contract->classification = NULL;

            if ($request->has('isInformation'))
                $contract->completed_registration = 'SI';

            if(!$contract->update()) {
                return $this->respondHttp500();
            }

            $users = $this->getUsersContract($contract->id);

            foreach ($users as $user)
            {
                $user->syncRoles([$this->getIdRole($contract->type)]);

                if ($contract->active == 'NO')
                {
                    $user->active = 'NO';
                    $user->save();
                }
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollback();
            //return $e->getMessage();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
            'message' => 'Se actualizo la contratista'
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function multiselect(Request $request)
    {
        $keyword = "%{$request->keyword}%";
        $contracts = ContractLesseeInformation::selectRaw("
            sau_ct_information_contract_lessee.id as id,
            CONCAT(sau_ct_information_contract_lessee.nit, ' - ',sau_ct_information_contract_lessee.social_reason) as nit
        ")
        ->where(function ($query) use ($keyword) {
            $query->orWhere('nit', 'like', $keyword);
        })
        ->take(30)->pluck('id', 'nit');
        return $this->respondHttp200([
            'options' => $this->multiSelectFormat($contracts)
        ]);
    }

    private function getIdRole($role)
    {
        $role = Role::withoutGlobalScopes()->select('id')
            ->where('type_role', 'Definido')
            ->where('name', $role)
            ->first();

        if ($role)
            $role = $role->id;

        return $role;
    }

    private function checkLimit()
    {
        $limit = CompanyLimitCreated::select('value')->first();

        if ($limit)
            $limit = $limit->value;
        else 
            $limit = 6;

        $count_contracts = ContractLesseeInformation::where('active', 'SI')->count();

        if ($count_contracts < $limit)
            return true;

        return false;
    }
}
