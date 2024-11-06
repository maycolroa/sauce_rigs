<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class InformContractItemFile extends Model
{
    protected $table = 'sau_ct_inform_contract_item_files';

    protected $fillable = [
        'inform_id',
        'item_id',
        'name_file',
        'file',
        'type_file'
    ];

    public function inform()
    {
        return $this->belongsTo(InformContract::class, 'inform_id');
    }

    public function path_base($storageLocation = true)
    {
        $path = "legalAspects/contracts/inform/files/";
        return $storageLocation ? storage_path("app/public/{$path}") : $path;
    }

    public function path_client($storageLocation = true)
    {
        return "{$this->path_base($storageLocation)}{$this->inform->company_id}";
    }

    public function path_donwload()
    {
        return "{$this->path_client(false)}/{$this->file}";
    }

    public function path_image()
    {
        return Storage::disk('s3')->url($this->path_donwload());
    }
}