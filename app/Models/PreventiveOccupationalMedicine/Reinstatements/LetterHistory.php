<?php

namespace App\Models\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class LetterHistory extends Model
{
    use CompanyTrait;

    protected $table = 'sau_reinc_letter_recommendations_history';

    protected $fillable = [
        'company_id',
        'check_id',
        'send_date',
        'to',
        'from',
        'subject',
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }

    public function checks()
    {
        return $this->hasMany(Check::class, 'restriction_id');
    }
}
