<?php

namespace App\Administrative\Configurations\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class QualificationCompany extends Model
{
    use CompanyTrait;

    protected $table = 'sau_dm_qualification_company';

    protected $fillable = [
        'company_id',
        'qualification_id'
    ];

    public function qualification()
    {
        return $this->belongsTo(Qualification::class, 'qualification_id');
    }
}
