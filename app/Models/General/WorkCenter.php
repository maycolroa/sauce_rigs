<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class WorkCenter extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_company_work_centers';

    protected $fillable = [
        'activity_economic',
        'direction',
        'telephone',
        'email',
        'departament_id',
        'city_id',
        'zona',
        'company_id'
    ];

    public function departament()
    {
        return $this->belongsTo(Departament::class, 'departament_id');
    }

    public function city()
    {
        return $this->belongsTo(Municipality::class, 'city_id');
    }

    public function multiselect()
    {
        return [
          'name' => $this->activity_economic,
          'value' => $this->id
        ];
    }
}
