<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class FileTransactionEmployee extends Model
{
    protected $table = 'sau_epp_files_transactions_employees';

    protected $fillable = [
        'transaction_employee_id',
        'file'
    ];

    public function transaction()
    {
        return $this->belongsTo(ElementTransactionEmployee::class, 'transaction_employee_id');
    }

    public function path_base($storageLocation = true)
    {
        $path = "industrialSecure/epp/transaction/delivery/files/";
        return $storageLocation ? storage_path("app/public/{$path}") : $path;
    }

    public function path_client($storageLocation = true)
    {
        return "{$this->path_base($storageLocation)}{$this->transaction->elements->company_id}";
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