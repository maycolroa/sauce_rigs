<?php

namespace App\Models\IndustrialSecure\DangerousConditions\Inspections;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Scopes\InspectionsFilterScope;

class InspectionItemsQualificationAreaLocation extends Model
{
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new InspectionsFilterScope);
    }

    const DISK = 's3_DConditions';

    protected $table = "sau_ph_inspection_items_qualification_area_location";

    protected $fillable = [
        'item_id',
        'qualification_id',
        'employee_regional_id',
        'employee_headquarter_id',
        'employee_process_id',
        'employee_area_id',
        'qualifier_id',	
        'find',
        'qualification_date',
        'photo_1',
        'photo_2'
    ];

    public $timestamps = false;

    public function item()
    {
        return $this->belongsTo(InspectionSectionItem::class, 'item_id');
    }

    public function regional()
    {
        return $this->belongsTo('App\Models\Administrative\Regionals\EmployeeRegional', 'employee_regional_id');
    }

    public function headquarter()
    {
        return $this->belongsTo('App\Models\Administrative\Headquarters\EmployeeHeadquarter', 'employee_headquarter_id');
    }

    public function process()
    {
        return $this->belongsTo('App\Models\Administrative\Processes\EmployeeProcess', 'employee_process_id');
    }

    public function area()
    {
        return $this->belongsTo('App\Models\Administrative\Areas\EmployeeArea', 'employee_area_id');
    }

    public function qualifier()
    {
        return $this->belongsTo('App\Models\Administrative\Users\User', 'qualifier_id');
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

    /**
     * filters checks through the given regionals
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $regionals
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInRegionals($query, $regionals, $typeSearch = 'IN')
    {
        if (COUNT($regionals) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $regionals);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_items_qualification_area_location.employee_regional_id', $regionals);
        }

        return $query;
    }

    /**
     * filters checks through the given headquarters
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $headquarters
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInHeadquarters($query, $headquarters, $typeSearch = 'IN')
    {
        $ids = [];

        foreach ($headquarters as $key => $value)
        {
            $ids[] = $value;
        }

        if (COUNT($ids) > 0)
        {
            $ids = explode(",", implode(",", $ids));

            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_items_qualification_area_location.employee_headquarter_id', $ids);
        }

        return $query;
    }

    /**
     * filters checks through the given processes
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $processes
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInProcesses($query, $processes, $typeSearch = 'IN')
    {
        if (COUNT($processes) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $processes);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_items_qualification_area_location.employee_process_id', $processes);
        }

        return $query;
    }

    /**
     * filters checks through the given areas
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $areas
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInAreas($query, $areas, $typeSearch = 'IN')
    {
        if (COUNT($areas) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspection_items_qualification_area_location.employee_area_id', $areas);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_items_qualification_area_location.employee_area_id', $areas);
        }

        return $query;
    }

    /**
     * filters checks through the given qualifiers
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $qualifiers
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInQualifiers($query, $qualifiers, $typeSearch = 'IN')
    {
        if (COUNT($qualifiers) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspection_items_qualification_area_location.qualifier_id', $qualifiers);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_items_qualification_area_location.qualifier_id', $qualifiers);
        }

        return $query;
    }

    public function scopeInItems($query, $items, $typeSearch = 'IN')
    {
        if (COUNT($items) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspection_items_qualification_area_location.item_id', $items);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_items_qualification_area_location.item_id', $items);
        }

        return $query;
    }

    public function scopeInLevelRisk($query, $levels, $typeSearch = 'IN')
    {
        if (COUNT($levels) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspection_items_qualification_area_location.level_risk', $levels);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_items_qualification_area_location.level_risk', $levels);
        }

        return $query;
    }

    public function scopeInQualification($query, $levels, $typeSearch = 'IN')
    {
        if (COUNT($levels) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_inspection_items_qualification_area_location.qualification_id', $levels);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_inspection_items_qualification_area_location.qualification_id', $levels);
        }

        return $query;
    }

     /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInUserFirm($query, $user_firm, $typeSearch = 'IN')
    {
        if (COUNT($user_firm) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn('sau_ph_qualification_inspection_firm.user_id', $user_firm);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn('sau_ph_qualification_inspection_firm.user_id', $user_firm);
        }

        return $query;
    }


    /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_ph_inspection_items_qualification_area_location.qualification_date', $dates);
            return $query;
        }
    }

    /**
     * filters checks through the given date
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $dates
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeBetweenInspectionDate($query, $dates)
    {
        if (COUNT($dates) == 2)
        {
            $query->whereBetween('sau_ph_inspections.created_at', $dates);
            return $query;
        }
    }

    /**
     * filters checks through the given themes
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $themes
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInThemes($query, $themes, $typeSearch = 'IN', $alias = 'sau_ph_inspection_sections')
    {
        $ids = [];

        foreach ($themes as $key => $value)
        {
            $ids[] = $value;
        }

        if (COUNT($ids) > 0)
        {
            $ids = explode(",", implode(",", $ids));

            if ($typeSearch == 'IN')
                $query->whereIn("{$alias}.id", $ids);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn("{$alias}.id", $ids);
        }

        return $query;
    }

    /**
     * filters checks through the given inspections
     * @param  Illuminate\Database\Eloquent\Builder $query
     * @param  array $inspections
     * @return Illuminate\Database\Eloquent\Builder
     */
    public function scopeInInspections($query, $inspections, $typeSearch = 'IN', $alias = 'sau_ph_inspections')
    {
        if (COUNT($inspections) > 0)
        {
            if ($typeSearch == 'IN')
                $query->whereIn("{$alias}.id", $inspections);

            else if ($typeSearch == 'NOT IN')
                $query->whereNotIn("{$alias}.id", $inspections);
        }

        return $query;
    }

    public function path_base()
    {
        return "inspections_images/";
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
