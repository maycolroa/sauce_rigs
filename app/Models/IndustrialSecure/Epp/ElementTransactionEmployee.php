<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Traits\CompanyTrait;


class ElementTransactionEmployee extends Model
{
    use CompanyTrait;

    protected $table = 'sau_epp_transactions_employees';

    protected $fillable = [
        'employee_id',
        'position_employee_id',
        'type',
        'observations',
        'firm_employee',
        'company_id',
        'location_id'
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
        return $this->belongsToMany('App\Models\IndustrialSecure\Epp\ElementBalanceSpecific', 'sau_epp_transaction_employee_element', 'transaction_employee_id', 'element_id');
    }

    public function employee()
    {
        return $this->belongsTo('App\Models\Administrative\Employees\Employee', 'employee_id');
    }

    public function position()
    {
        return $this->belongsTo('App\Models\Administrative\Positions\EmployeePosition', 'position_employee_id');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    public function user()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User', 'user_id');
    }

    public function path_base($storageLocation = true)
    {
        $path = "industrialSecure/epp/transaction/files/";
        return $storageLocation ? storage_path("app/public/{$path}") : $path;
    }

    public function path_client($storageLocation = true)
    {
        return "{$this->path_base($storageLocation)}{$this->company_id}";
    }

    public function path_donwload()
    {
        return "{$this->path_client(false)}/{$this->firm_employee}";
    }

    public function path_image()
    {
        return Storage::disk('s3')->url($this->path_donwload());
    }
}
