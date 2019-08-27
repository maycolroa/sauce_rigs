<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use Session;

class Interest extends Model
{
    protected $table = 'sau_lm_interests';

    protected $fillable = [
        'name',
        'company_id'
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class,'sau_lm_article_interest');
    }

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }

    public function scopeCompany($query)
    {
        return $query->where('sau_lm_interests.company_id', Session::get('company_id'));
    }

    public function scopeSystem($query)
    {
        return $query->whereNull('sau_lm_interests.company_id');
    }

    public function scopeAlls($query, $company_id = null)
    {
        if (!$company_id)
            $company_id = Session::get('company_id');

        return $query->where('sau_lm_interests.company_id', $company_id)
                     ->orWhereNull('sau_lm_interests.company_id');
    }
}