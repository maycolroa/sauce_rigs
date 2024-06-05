<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Inform extends Model
{
    use CompanyTrait;

    protected $table = 'sau_ct_informs';

    protected $fillable = [
        'name',
        'company_id',
        'creator_user_id'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array
     */
    protected $dates = [
        'time_edit',
    ];

    public function themes()
    {
        return $this->hasMany(InformTheme::class, 'inform_id');
    }

    public function informContracts()
    {
        return $this->hasMany(InformContract::class, 'inform_id');
    }

}