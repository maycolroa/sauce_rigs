<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class InformThemeItem extends Model
{
    protected $table = 'sau_ct_inform_theme_item';

    protected $fillable = [
        'evaluation_theme_id',
        'description'
    ];

    public function theme()
    {
        return $this->belongsTo(InformTheme::class, 'evaluation_theme_id');
    }
}