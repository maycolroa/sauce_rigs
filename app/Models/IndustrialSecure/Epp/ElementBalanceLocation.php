<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;


class ElementBalanceLocation extends Model
{
    protected $table = 'sau_epp_elements_balance_ubication';

    protected $fillable = [
        'element_id',
        'location_id',
        'quantity',
        'quantity_available',
        'quantity_allocated'
    ];

    public function element()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\Epp\Element', 'element_id');
    }

    public function scopeInElement($query, $elements, $typeSearch = 'IN')
    {
        if (COUNT($elements) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_epp_elements_balance_ubication.element_id', $elements);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_epp_elements_balance_ubication.element_id', $elements);
        }

        return $query;
    }

    public function scopeInLocation($query, $locations, $typeSearch = 'IN')
    {
        if (COUNT($locations) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_epp_elements_balance_ubication.location_id', $locations);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_epp_elements_balance_ubication.location_id', $locations);
        }

        return $query;
    }
}
