<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class InformTheme extends Model
{
    protected $table = 'sau_ct_informs_themes';

    protected $fillable = [
        'inform_id',
        'description'
    ];

    public function inform()
    {
        return $this->belongsTo(Inform::class, 'inform_id');
    }

    public function items()
    {
        return $this->hasMany(InformThemeItem::class, 'evaluation_theme_id');
    }
}