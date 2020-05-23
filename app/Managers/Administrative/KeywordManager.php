<?php

namespace App\Managers\Administrative;

use DB;
use Exception;
use App\Managers\BaseManager;
use App\Models\General\Keyword;
use App\Models\Administrative\Labels\KeywordCompany;

class KeywordManager extends BaseManager
{
    /**
     * Devuelve una instancia de QueryBuilder 
     *
     * @param array $request
     * @param int $company_id
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function dataQueryBuilder($request = [], $filters = [], $company_id = null)
    {
        $records = KeywordCompany::select(
            'sau_keywords.id AS id',
            'sau_keywords.display_name AS name',
            'sau_keyword_company.display_name AS display_name'
        )
        ->join('sau_keywords', 'sau_keywords.id', 'sau_keyword_company.keyword_id');
        
        if (!$request->has('orderBy'))
            $records->orderBy('name');

        if ($company_id)
            $records->company_scope = $company_id;

        return $records;
    }

    /**
     * Guarda o actualiza un registro
     *
     * @param array|collect $request
     * @return App\Models\Administrative\KeywordCompany
     */
    public function saveRecord($request, $company_id)
    {
        DB::beginTransaction();

        try
        {
            $key = KeywordCompany::where('keyword_id', $request->keyword_id);
            $key->company_scope = $company_id;
            $key->delete();

            if ($request->has("old_keyword_id"))
            {
                $key = KeywordCompany::where('keyword_id', $request->old_keyword_id);
                $key->company_scope = $company_id;
                $key->delete();
            }

            $record = new KeywordCompany();
            $record->fill($request->all());
            $record->company_id = $company_id;

            if (!$record->save())
                return false;

            DB::commit();

            return $record;

        } catch (Exception $e) {
            DB::rollback();
            $this->printLogError($e);
            return false;
        }

    }

    /**
     * Devuelve una instancia del registro
     *
     * @param int $id
     * @param int $company_id
     * @return App\Models\Administrative\KeywordCompany
     */
    public function getRecord($id, $company_id = null)
    {
        try
        {
            $record = KeywordCompany::where('keyword_id', $id);

            if ($company_id)
                $record->company_scope = $company_id;

            $record = $record->first();
            $record->old_keyword_id = $record->keyword_id;
            $record->multiselect_keyword = $record->keyword->multiselect();

            return $record;

        } catch (Exception $e) {
            $this->printLogError($e);
            return false;
        }
    }

    /**
     * Eliminar el registro indicado
     *
     * @param int $id
     * @param int $company_id
     * @return boolean
     */
    public function deleteRecord($id, $company_id = null)
    {
        try
        {
            $record = KeywordCompany::where('keyword_id', $id);
            $record->company_scope = $company_id;
            return $record->delete();
        }
        catch (Exception $e) {
            $this->printLogError($e);
            return false;
        }
    }

    /**
     * Devuelve un arreglo con las palabras personalizadas
     *
     * @param int $company
     * @return array
     */
    public function getKeywords($company)
    {
        $keywords = Keyword::select(
            'sau_keywords.name AS name',
            DB::raw('IF (sau_keyword_company.display_name IS NULL, sau_keywords.display_name, sau_keyword_company.display_name) AS display_name')
        )
        ->leftJoin("sau_keyword_company", function ($join) use ($company) {
            $join->on("sau_keyword_company.keyword_id", "sau_keywords.id")
                ->whereRaw("(sau_keyword_company.company_id = {$company} OR sau_keyword_company.company_id IS NULL)");
        })
        ->pluck('display_name', 'name');
        
        return $keywords;
    }
}