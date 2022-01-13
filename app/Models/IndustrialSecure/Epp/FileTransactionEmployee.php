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
        return "{$this->path_base($storageLocation)}{$this->transaction->company_id}";
    }

    public function path_donwload()
    {
        return "{$this->path_client(false)}/{$this->file}";
    }

    public function path_image()
    {
        return Storage::disk('s3')->url($this->path_donwload());
    }

    public function path_base_return($storageLocation = true)
    {
        $path = "industrialSecure/epp/transaction/return/files/";
        return $storageLocation ? storage_path("app/public/{$path}") : $path;
    }

    public function path_client_return($storageLocation = true)
    {
        return "{$this->path_base_return($storageLocation)}{$this->company_id}";
    }

    public function path_donwload_return()
    {
        return "{$this->path_client_return(false)}/{$this->firm_employee}";
    }

    public function path_image_return()
    {
        return Storage::disk('s3')->url($this->path_donwload_return());
    }
}