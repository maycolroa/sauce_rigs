<?php

namespace App\Models\General;

use Illuminate\Database\Eloquent\Model;

class NewsletterSendUser extends Model
{    
    protected $table = 'sau_users_newsletter_send';

    protected $fillable = [
        'email',
        'newsletter_id',
        'state'
    ];
}
