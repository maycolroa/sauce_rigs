<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use Carbon\Carbon;

class FileUpload extends Model
{
    use CompanyTrait;
    
    protected $table = "sau_ct_file_upload_contracts_leesse";

    protected $fillable = [
        'name',
        'expirationDate',
        'file',
        'user_id',
        'observations',
        'state',
        'reason_rejection',
        'apply_file',
        'apply_motive',
        'notificate', 
        'module'
    ];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_ct_information_contract_lessee';

    public function items()
    {
        return $this->belongsToMany(SectionCategoryItems::class, 'sau_ct_file_item_contract', 'file_id', 'item_id')->withPivot('list_qualification_id');
    }

    public function documents()
    {
        return $this->belongsToMany(ActivityDocument::class, 'sau_ct_file_document_employee', 'file_id', 'document_id')->withPivot('employee_id');
    }

    public function documentsContract()
    {
        return $this->belongsToMany(ContractDocument::class, 'sau_ct_file_document_contract', 'file_id', 'document_id')->withPivot('contract_id');
    }

    public function contracts()
    {
        return $this->belongsToMany(ContractLesseeInformation::class,'sau_ct_file_upload_contract', 'file_upload_id', 'contract_id');
    }

    /**
     * filters checks through the given contracts
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $contracts
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInContracts($query, $contracts, $typeSearch = 'IN')
    {
        if (COUNT($contracts) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ct_file_upload_contract.contract_id', $contracts);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ct_file_upload_contract.contract_id', $contracts);
        }

        return $query;
    }

    public function scopeInUsers($query, $users, $typeSearch = 'IN')
    {
        if (COUNT($users) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ct_file_upload_contracts_leesse.user_id', $users);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ct_file_upload_contracts_leesse.user_id', $users);
        }

        return $query;
    }

    public function scopeInModules($query, $modules, $typeSearch = 'IN')
    {
        if (COUNT($modules) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ct_file_upload_contracts_leesse.module', $modules);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ct_file_upload_contracts_leesse.module', $modules);
        }

        return $query;
    }

    /**
     * filters checks through the given items
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $items
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInItems($query, $items, $typeSearch = 'IN')
    {
        if (COUNT($items) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ct_section_category_items.id', $items);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ct_section_category_items.id', $items);
        }

        return $query;
    }

    public function scopeInProyects($query, $proyects, $typeSearch = 'IN')
    {
        if (COUNT($proyects) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ct_contract_employee_proyects.proyect_contract_id', $proyects);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ct_contract_employee_proyects.proyect_contract_id', $proyects);
        }

        return $query;
    }

    public function scopeInProyectsEmployeeContract($query, $proyects, $typeSearch = 'IN')
    {
        if (COUNT($proyects) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereRaw("(sau_ct_contract_employee_proyects.proyect_contract_id in $proyects) OR (sau_ct_contracts_proyects.proyect_id in $proyects)");

            else if ($typeSearch == 'NOT IN')
                $query->whereRaw("(sau_ct_contract_employee_proyects.proyect_contract_id not in $proyects) OR (sau_ct_contracts_proyects.proyect_id not in $proyects)");
        }

        return $query;
    }

    /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenCreated($query, $dates)
    {
        $dates_request = explode('/', $dates);
        $dates_query = [];

        if (COUNT($dates_request) == 2)
        {
            array_push($dates_query, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->format('Y-m-d 00:00:00'));
            array_push($dates_query, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->format('Y-m-d 23:59:59'));
        }

        if (COUNT($dates_query) == 2)
        {
            $query->whereBetween('sau_ct_file_upload_contracts_leesse.created_at', $dates_query);
        }

        return $query;
    }

    /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenUpdated($query, $dates)
    {
        $dates_request = explode('/', $dates);
        $dates_query = [];

        if (COUNT($dates_request) == 2)
        {
            array_push($dates_query, (Carbon::createFromFormat('D M d Y', $dates_request[0]))->format('Y-m-d 00:00:00'));
            array_push($dates_query, (Carbon::createFromFormat('D M d Y', $dates_request[1]))->format('Y-m-d 23:59:59'));
        }

        if (COUNT($dates_query) == 2)
        {
            $query->whereBetween('sau_ct_file_upload_contracts_leesse.updated_at', $dates_query);
        }

        return $query;
    }
}
