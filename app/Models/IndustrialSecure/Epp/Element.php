<?php

namespace App\Models\IndustrialSecure\Epp;

use Illuminate\Database\Eloquent\Model;
use App\Traits\CompanyTrait;
use Illuminate\Support\Facades\Storage;


class Element extends Model
{
    use CompanyTrait;

    protected $table = 'sau_epp_elements';

    protected $fillable = [
        'code',
        'name',
        'description',
        'type',
        'mark',
        'applicable_standard',
        'observations',
        'operating_instructions',
        'state',
        'reusable',
        'image',
        'company_id',
        'identify_each_element',
        'expiration_date'
    ];

    public function multiselect()
    {
        return [
            'name' => $this->name,
            'value' => $this->id
        ];
    }

    public function path_base($storageLocation = true)
    {
        $path = "industrialSecure/epp/element/files/";
        return $storageLocation ? storage_path("app/public/{$path}") : $path;
    }

    public function path_client($storageLocation = true)
    {
        return "{$this->path_base($storageLocation)}{$this->company_id}";
    }

    public function path_donwload()
    {
        return "{$this->path_client(false)}/{$this->image}";
    }

    public function path_image()
    {
        return Storage::disk('s3')->url($this->path_donwload());
    }
}
