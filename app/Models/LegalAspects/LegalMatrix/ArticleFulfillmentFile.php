<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ArticleFulfillmentFile extends Model
{
    use CompanyTrait;

    protected $table = 'sau_lm_articles_fulfillment_files';

    protected $fillable = [
        'article_id',
        'company_id',
        'fulfillment_id',
        'file',
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function qualification()
    {
        return $this->belongsTo(ArticleFulfillment::class);
    }
}
