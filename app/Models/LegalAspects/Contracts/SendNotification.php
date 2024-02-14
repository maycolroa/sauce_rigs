<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class SendNotification extends Model
{
    use CompanyTrait;
    
    protected $table = 'sau_ct_send_notifications';

    protected $fillable = [
        'company_id',
        'subject',
        'company_id',
        'image',
        'date_send',
        'hour',
        'active',
        'send',
    ];

    public function activities()
    {
        return $this->belongsToMany(ActivityContract::class, 'sau_ct_notification_activities','notification_id', 'activity_id');
    }

    public function contracts()
    {
        return $this->belongsToMany(ContractLesseeInformation::class, 'sau_ct_notification_contracts', 'notification_id', 'contract_id');
    }

    /**
     * verifies if the check is open
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    public function isSend()
    {
        return $this->send;
    }
}
