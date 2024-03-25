<?php

namespace App\Models\IndustrialSecure\RoadSafety\Inspections;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Scopes\InspectionsFilterScope;

class InspectionItemsQualificationLocation extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new InspectionsFilterScope);
    }

    const DISK = 's3';

    protected $table = "sau_rs_inspection_items_qualifications_locations";

    protected $fillable = [
        'inspection_qualification_id',
        'theme_id',
        'item_id',
        'qualification_id',
        'qualify',
        'find',
        'level_risk',
        'photo_1',
        'photo_2'
    ];

    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(InspectionSectionItem::class, 'item_id');
    }

    public function theme()
    {
        return $this->belongsTo(InspectionSection::class, 'theme_id');
    }

    public function qualified()
    {
        return $this->belongsTo(InspectionQualified::class, 'inspection_qualification_id');
    }

    public function qualification()
    {
        return $this->belongsTo('App\Models\IndustrialSecure\DangerousConditions\Inspections\Qualifications', 'sau_ph_qualifications_inspections');
    }
    
    public function image($imageNumber, $imageName = null)
    {
        if ($imageName == null) {
            return $this->attributes["photo_{$imageNumber}"];
        }

        $this->attributes["photo_{$imageNumber}"] = $imageName;
    }

    public function scopeInItems($query, $items, $typeSearch = 'IN')
    {
        if (COUNT($items) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_rs_inspection_items_qualifications_locations.item_id', $items);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_inspection_items_qualifications_locations.item_id', $items);
        }

        return $query;
    }

    public function scopeInLevelRisk($query, $levels, $typeSearch = 'IN')
    {
        if (COUNT($levels) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_rs_inspection_items_qualifications_locations.level_risk', $levels);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_inspection_items_qualifications_locations.level_risk', $levels);
        }

        return $query;
    }

    public function scopeInQualification($query, $levels, $typeSearch = 'IN')
    {
        if (COUNT($levels) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_rs_inspection_items_qualifications_locations.qualification_id', $levels);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_rs_inspection_items_qualifications_locations.qualification_id', $levels);
        }

        return $query;
    }

    public function scopeInActionPlan($query, $ap, $typeSearch = 'IN')
    {
        if ($typeSearch == 'IN')
        {
            if (count($ap) > 0 && $ap[0] == 'SI')
                $query->whereNotNull('sau_action_plans_activities.id');
            else
                $query->whereNull('sau_action_plans_activities.id');
        }
        else if ($typeSearch == 'NOT IN')
        {
            if (count($ap) > 0 && $ap[0] == 'NO')
                $query->whereNull('sau_action_plans_activities.id');
            else
                $query->whereNotNull('sau_action_plans_activities.id');
        }

        return $query;
    }

    public function path_base()
    {
        return "rs_inspections_images/";
    }

    public function donwload_img($key)
    {
        return Storage::disk($this::DISK)->download("{$this->path_base()}{$this->$key}");
    }

    public function path_image($key)
    {
        if ($this->$key && $this->img_exists($key))
            return Storage::disk($this::DISK)->url("{$this->path_base()}{$this->$key}");
    }

    public function img_exists($key)
    {
        return Storage::disk($this::DISK)->exists("{$this->path_base()}{$this->$key}");
    }

    public function img_delete($key)
    {
        if ($this->$key && $this->img_exists($key))
           Storage::disk($this::DISK)->delete("{$this->path_base()}{$this->$key}");
    }

    public function store_image($key, $fileName, $file)
    {
        Storage::disk($this::DISK)->put("{$this->path_base()}{$fileName}", $file, 'public');
        $this->update([$key => $fileName]);
    }

    public function store_image_api($fileName, $file)
    {
        return Storage::disk($this::DISK)->put("{$this->path_base()}{$fileName}", $file, 'public');
    }
}
