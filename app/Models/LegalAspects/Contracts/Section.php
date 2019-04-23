<?php

namespace App\Models\LegalAspects\Contracts;

use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    protected $table = 'sau_ct_section';

    public $timestamps = false;

    protected $fillable = [
        'section_name'
    ];

    public function categories()
    {
        return $this->hasMany(SectionCategory::class, 'section_id');
    }
}