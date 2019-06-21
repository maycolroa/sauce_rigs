<?php

namespace App\Models\LegalAspects\LegalMatrix;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class ArticleFulfillment extends Model
{
    use CompanyTrait;

    protected $table = 'sau_lm_articles_fulfillment';

    protected $fillable = [
        'article_id',
        'company_id',
        'fulfillment_value_id',
        'observations',
        'file',
        'responsible'
    ];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function fulfillment_value()
    {
        return $this->belongsTo(FulfillmentValues::class);
    }
}
