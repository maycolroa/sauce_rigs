<?php

namespace App\Models\PreventiveOccupationalMedicine\Absenteeism;

use App\Traits\CompanyTrait;
use App\Traits\UtilsTrait;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;

class Report extends Model
{
    use CompanyTrait;
    use UtilsTrait;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sau_absen_reports';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name_show',
        'name_report',
        'user',
        'site',
        'company_id',
        'state',
        'type',
        'es_bsc',
        'module_id',
    ];

    /**
     * Código para poder ver los informes de tableau
     * @var string
     */
    private $PostCode;

    public function users()
    {
        return $this->belongsToMany("app\Models\Administrative\Users\User",'sau_absen_report_user', 'report_id', 'user_id');
    }

    public function getTableauCode()
    {
        $ip = "";
        if ($this->isThot()) {
            $ip = Config::get('app.server_ip')[0];
        }else{
            $ip = Config::get('app.server_ip')[1];
        }

        $url = "https://$ip/trusted";
        
        $postfields = [
            'username' => urlencode($this->user),
            'target_site' => urlencode($this->site),
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_POST, 1);
        // Edit: prior variable $postFields should be $postfields;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postfields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // On dev server only!
        $this->PostCode = curl_exec($ch);
        
        //close connection
        curl_close($ch);
        return $this;
    }

    /**
     * Genera la URL para visualizar el informe de Tableau
     * @return string
     */
    public function generateReportURL()
    {
        $ip = "";
        if ($this->isThot()) {
            $ip = Config::get('app.server_ip')[0];
        }else{
            $ip = Config::get('app.server_ip')[1];
        }
        return 'https://' . $ip . "/trusted/{$this->PostCode}/t/{$this->site}/views/{$this->name_report}?:customViews=no";
    }
}