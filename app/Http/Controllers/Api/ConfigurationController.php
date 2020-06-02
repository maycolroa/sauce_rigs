<?php

namespace App\Http\Controllers\Api;

use DB;
use Illuminate\Http\Request;
use App\Models\General\Company;
use App\Facades\Configuration;
use App\Http\Requests\Api\CompanyRequiredRequest;
use Auth;

class ConfigurationController extends ApiController
{
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


    /**
     * Get state access positions
     *
     * @return \Illuminate\Http\Response
     */
    public function getPositions(Request $request)
    {
      try{

        $company = Company::find($this->user->company_id);
        if(!$company->ph_state_positions)
        {
          return response(json_encode([
            'response' => 'ok',
            'data' => ''
         ], JSON_UNESCAPED_UNICODE), 200);
        }

        $positionsQuery = User::join('ph_reports', 'ph_users.id', '=', 'ph_reports.user_id')
            ->join('sec_company', 'ph_users.company_id', '=', 'sec_company.id')
            ->select(DB::raw('ph_users.id,ph_users.identification, ph_users.name, count(distinct ph_reports.id) * sec_company.ph_points_positions as points'))
            ->where('sec_company.id',$this->user->company_id)
            ->groupBy(DB::raw('ph_users.id,ph_users.identification, ph_users.name, sec_company.ph_points_positions'))
            ->orderBy(DB::raw('count(distinct ph_reports.id) * sec_company.ph_points_positions'), 'desc');

        if(isset($request->startDate) && isset($request->endDate)){
          $positionsQuery->whereBetween('ph_reports.created_at', [$request->startDate, $request->endDate]);
        }

        $posicion;
        $puntos;
        foreach ($positionsQuery->get() as $key => $item) {
          if($item->id == $this->user->id){
            $posicion = $key;
            $puntos = $item->points;
            break;
          }
        }

        if(!isset($posicion) && !isset($puntos)){
          return response(json_encode([
            'response' => 'ok',
            'data' => [
              'posicion' => null,
              'puntos' => 0
            ]
          ], JSON_UNESCAPED_UNICODE), 200);
        }

        return response(json_encode([
          'response' => 'ok',
          'data' => [
            'posicion' => $posicion +1,
            'puntos' => $puntos
          ]
        ], JSON_UNESCAPED_UNICODE), 200);
      }
      catch(\Exception $e){
        \Log::error($e);
        
        return response(json_encode([
          'response' => 'error',
          'data' => ''
        ], JSON_UNESCAPED_UNICODE), 500);
      }
    }

    /**
     * Get all locations actives by company
     *
     * @return \Illuminate\Http\Response
     */
    public function listResponsibles(){
      $responsibles = User::select(
          'ph_users.id as id',
          'ph_users.name as name'
      )
      ->join('ph_role_user', 'ph_role_user.user_id' ,'=', 'ph_users.id')
      ->whereIn('ph_role_user.role_id', ['2','4'])
      ->where('ph_users.company_id', $this->user->company_id)->get();

      return response(json_encode([
        'response' => 'ok',
        'data' => [
          'responsibles' => $responsibles,
        ]
      ], JSON_UNESCAPED_UNICODE), 200);
    }
}
