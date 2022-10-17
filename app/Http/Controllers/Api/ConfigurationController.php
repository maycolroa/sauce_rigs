<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use App\Models\General\Company;
use App\Facades\Configuration;
use App\Http\Requests\Api\CompanyRequiredRequest;
use App\Models\Administrative\Users\User;
use App\Models\IndustrialSecure\DangerousConditions\ImageApi;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\General\Team;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use Auth;
use Illuminate\Support\Facades\Storage;

class ConfigurationController extends ApiController
{
    public function saveImageApi(Request $request)
    {
      DB::beginTransaction();
          
        try
        {
          $img = $this->base64($request->image);
          $fileName = $img['name'];
          $file = $img['image'];

          $image = new ImageApi;
          $image->file = $fileName;
          $image->type = $request->type;
          $image->hash = $request->hash;
          $image->save();

          if (!$image->save())
                return $this->respondHttp500();

          if ($image->type == 1)
            (new Report)->store_image_api($fileName, $file);
          else if ($image->type == 4)
            Storage::disk('s3')->put('industrialSecure/epp/transaction/files/'.$this->company.'/' . $imageName, $file, 'public');
          else
            (new InspectionItemsQualificationAreaLocation)->store_image_api($fileName, $file);

          DB::commit();

        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            DB::rollback();
            return $this->respondHttp500();
        }

        return $this->respondHttp200([
          'data' => $request->id
        ]);
    }

    public function base64($file)
    {
      $image_64 = $file;

      $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];

      $replace = substr($image_64, 0, strpos($image_64, ',')+1); 

      $image = str_replace($replace, '', $image_64); 

      $image = str_replace(' ', '+', $image); 

      $imageName = base64_encode($this->user->id . rand(1,10000) . now()) . '.' . $extension;

      $imagen = base64_decode($image);

      return ['name' => $imageName, 'image' => $imagen];

    } 

  /**
   * Get state terms and conditions
   *
   * @return \Illuminate\Http\Response
   */
  public function termsConditions(CompanyRequiredRequest $request)
  {
      $company = Company::find($request->company_id);

      $data = [
        'terms_conditions' => $company->ph_terms_conditions
      ];

      return $this->respondHttp200([
          'data' => $data
      ]);
  }

  /**
   * Get text terms and conditions
   *
   * @return \Illuminate\Http\Response
   */
  public function getTermsConditions(CompanyRequiredRequest $request)
  {
      $application = Configuration::getConfiguration('ph_text_terms_conditions');

      $data = [
        'text_terms_conditions' => $application
      ];

      return $this->respondHttp200([
          'data' => $data
      ]);
  }


    /**
     * change state terms and conditions
     *
     * @return \Illuminate\Http\Response
     */
    public function changeStateTermsConditions(CompanyRequiredRequest $request)
    {
        $company = Company::find($request->company_id);

        $company->ph_terms_conditions = $request->terms_condition;
        $company->save();

        return $this->respondHttp200();
    }

    public function getUsersActionPlan(CompanyRequiredRequest $request)
    {
        $team = Team::where('name', $request->company_id)->first();

        $users = User::selectRaw("
                    sau_users.id as id,
                    sau_users.name as name
                ")->active();
        $users->company_scope = $request->company_id;

        if ($this->user->hasRole('Arrendatario', $team->id) || $this->user->hasRole('Contratista', $team->id))
        {
            $users->join('sau_user_information_contract_lessee', 'sau_user_information_contract_lessee.user_id', 'sau_users.id')
                  ->where('sau_user_information_contract_lessee.information_id', $this->getContractIdUser($this->user->id));
        }
        else
        {
            $users->join('sau_company_user', 'sau_company_user.user_id', 'sau_users.id');
        }
        
        $users = $users->get();

        $isSuper = $this->user->hasRole('Superadmin', $team->id);

        if (!$isSuper)
        {
            $users = $users->filter(function ($user, $key) use ($team) {
                return !$user->hasRole('Superadmin', $team->id);
            });
        }

        $users = $users->pluck('id', 'name');

        return $this->multiSelectFormat($users);
    }

    /**
     * Get state access positions
     *
     * @return \Illuminate\Http\Response
     */
    public function statePositions()
    {    
      $company = Company::find($this->user->company_id);

      $data = [
        'state_positions' => $company->ph_state_positions
      ];

      return response(json_encode([
          'response' => 'ok',
          'data' => $data
      ], JSON_UNESCAPED_UNICODE), 200);
    }
}
