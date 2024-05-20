<?php

namespace App\Models\IndustrialSecure\DangerMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ComplementaryMethodology extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_dm_complementary_methodologies';

    protected $fillable = [
        'company_id',
        'name',
        'file',
        'type',
        'observations',
        'user_id'
    ];

    public function users()
    {
        return $this->belongsTo("App\Models\Administrative\Users\User",'user_id');
    }
}
