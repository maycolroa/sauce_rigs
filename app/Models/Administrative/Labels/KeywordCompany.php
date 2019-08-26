<?php

namespace App\Models\Administrative\Labels;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class KeywordCompany extends Model
{
    use CompanyTrait;

    protected $table = 'sau_keyword_company';

    public $timestamps = false;

    protected $fillable = [
        'keyword_id',
        'company_id',
        'display_name'
    ];

    public function keyword()
    {
        return $this->belongsTo('App\Models\General\Keyword', 'keyword_id');
    }
}
