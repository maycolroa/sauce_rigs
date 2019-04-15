<?php

namespace App\Models\LegalAspects;

use Illuminate\Database\Eloquent\Model;

class FileUpload extends Model
{
    protected $table = "sau_ct_file_upload_contracts_leesse";

    protected $fillable = ['name','expirationDate','file'];

    public function itemInfo(){
        return $this->belongsToMany('App\Models\LegalAspects\SectionCategoryItems','sau_ct_file_item_contract', 'item_id', 'file_id');
    }

    public function itemSyncInfo(){
        return $this->belongsToMany('App\Models\LegalAspects\FileUploadItemsDetail','sau_ct_file_item_contract', 'item_id', 'file_id');
    }
}
