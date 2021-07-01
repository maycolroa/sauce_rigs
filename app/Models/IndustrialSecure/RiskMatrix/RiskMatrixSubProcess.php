<?php

namespace App\Models\IndustrialSecure\RiskMatrix;

use Illuminate\Database\Eloquent\Model;

class RiskMatrixSubProcess extends Model
{
    protected $table = 'sau_rm_risk_matrix_subprocess';

    protected $fillable = [
        'risk_matrix_id',
        'sub_process_id'
    ];

    public $timestamps = false;

    public function subProcess()
    {
        return $this->belongsTo(SubProcess::class, 'sub_process_id');
    }

    public function risks()
    {
        return $this->hasMany(SubProcessRisk::class, 'rm_subprocess_id');
    }
}
