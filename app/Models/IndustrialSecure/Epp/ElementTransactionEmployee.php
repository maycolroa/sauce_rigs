<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class ElementTransactionEmployee extends Model
{
    protected $table = 'sau_epp_transactions_employees';

    protected $fillable = [
        'employee_id',
        'position_employee',
        'type',
        'observations',
        'firm_employee',
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }

    public function elements()
    {
        return $this->belongsToMany('App\Models\IndustrialSecure\Epp\Element', 'sau_epp_transaction_employee_element', 'transaction_employee_id');
    }

    public function path_base($storageLocation = true)
    {
        $path = "industrialSecure/epp/transaction/delivery/files/";
        return $storageLocation ? storage_path("app/public/{$path}") : $path;
    }

    public function path_client($storageLocation = true)
    {
        return "{$this->path_base($storageLocation)}{$this->elements->company_id}";
    }

    public function path_donwload()
    {
        return "{$this->path_client(false)}/{$this->image}";
    }

    public function path_image()
    {
        return Storage::disk('s3')->url($this->path_donwload());
    }
}
