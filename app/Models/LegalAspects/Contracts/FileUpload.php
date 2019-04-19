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

    public function itemInfo(){
        return $this->belongsToMany(SectionCategoryItems::class,'sau_ct_file_item_contract', 'item_id', 'file_id');
    }

    public function itemSyncInfo(){
        return $this->belongsToMany(FileUploadItemsDetail::class,'sau_ct_file_item_contract', 'item_id', 'file_id');
    }

    public function contracts()
    {
        return $this->belongsToMany(ContractLessee::class,'sau_ct_file_upload_contract', 'file_upload_id', 'contract_id');
    }
}
