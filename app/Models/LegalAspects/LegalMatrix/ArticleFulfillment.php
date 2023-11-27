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
        'responsible',
        'qualification_masive',
        'workplace',
        'date_qualification_edit'
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

    public function histories()
    {
        return $this->hasMany(ArticleFulfillmentHistory::class, 'fulfillment_id');
    }

    public function scopeBetweenDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_lm_articles_fulfillment.date_qualification_edit', $dates);
            return $query;
        }
    }
}
