<?php

namespace App\Models\PreventiveOccupationalMedicine\Absenteeism;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;

class Talend extends Model
{
    use CompanyTrait;

    protected $table = "sau_absen_talends";

    protected $fillable = [
        'name',
        'path',
        'file',
        'file_original_name',
        'company_id',
        'state'
    ];

    /**
     * verifies if the check is open
     * @return boolean
     */
    public function isActive()
    {
        return $this->state == 'SI';
    }
    
    public function scopeActive($query, $state = true)
    {
        if ($state)
            $query->where('sau_absen_talends.state', 'SI');
        else
            $query->where('sau_absen_talends.state', 'NO');

        return $query;
    }

    public function path_base($storageLocation = true)
    {
        $path = "talends/preventiveOccupationalMedicine/absenteeism/";
        return $storageLocation ? storage_path("app/{$path}") : $path;
    }

    public function path_client($storageLocation = true)
    {
        return "{$this->path_base($storageLocation)}{$this->company_id}";
    }

    public function path_donwload()
    {
        return "{$this->path_client(false)}/{$this->file}";
    }

    public function path_from_extract()
    {
        return "{$this->path_client()}/{$this->file}";
    }

    public function path_to_extract()
    {
        return "{$this->path_client()}/{$this->file_original_name}";
    }

    public function path_sh()
    {
        return "{$this->path_client()}/{$this->file_original_name}/{$this->file_original_name}/{$this->file_original_name}_run.sh";
    }
}
