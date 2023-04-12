<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class NewsletterSend extends Model
{
    //use CompanyTrait;
    
    protected $table = 'sau_newsletters_sends';

    protected $fillable = [
        'subject',
        'image',
        'date_send',
        'hour',
        'active',
        'send',
    ];

    /**
     * verifies if the check is open
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }
}
