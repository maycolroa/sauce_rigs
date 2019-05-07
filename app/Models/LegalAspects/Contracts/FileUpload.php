<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class FileUpload extends Model
{
    use CompanyTrait;
    
    protected $table = "sau_ct_file_upload_contracts_leesse";

    protected $fillable = ['name','expirationDate','file'];

    //the attribute define the table for scope company execute
    public $scope_table_for_company_table = 'sau_ct_information_contract_lessee';

    public function items()
    {
        return $this->belongsToMany(SectionCategoryItems::class, 'sau_ct_file_item_contract', 'file_id', 'item_id');
    }

    public function contracts()
    {
        return $this->belongsToMany(ContractLesseeInformation::class,'sau_ct_file_upload_contract', 'file_upload_id', 'contract_id');
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
}
