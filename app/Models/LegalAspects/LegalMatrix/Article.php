<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use Session;

class Article extends Model
{
    use CompanyTrait;

    protected $table = 'sau_lm_articles';

    protected $fillable = [
        'name',
        'description',
        'law_id',
        'repealed',
        'sequence'
    ];

    public $scope_table_for_company_table = 'sau_lm_company_interest';

    public function law()
    {
        return $this->belongsTo(Law::class);
    }

    public function interests()
    {
        return $this->belongsToMany(Interest::class, 'sau_lm_article_interest');
    }

    public function histories()
    {
        return $this->hasMany(ArticleHistory::class, 'article_id');
    }

    public function scopeAlls($query, $company_id = null)
    {
        if (!$company_id)
            $company_id = Session::get('company_id');

        return $query->where('sau_lm_laws.company_id', $company_id)
                     ->orWhereNull('sau_lm_laws.company_id');
    }
}
