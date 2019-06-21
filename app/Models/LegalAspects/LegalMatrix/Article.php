<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Article extends Model
{
    use CompanyTrait;

    protected $table = 'sau_lm_articles';

    protected $fillable = [
        'name',
        'description',
        'law_id',
        'repelead',
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
}
