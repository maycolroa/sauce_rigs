<?php

namespace App\Exports\IndustrialSecure\RiskMatrix;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\IndustrialSecure\RiskMatrix\RiskMatrixImportTemplate;
use App\Exports\IndustrialSecure\RiskMatrix\RiskCategoriesExcel;
use App\Models\IndustrialSecure\RiskMatrix\TagsRmCategoryRisk;
use App\Exports\WarningImportTemplate;

class RiskMatrixImportTemplateExcel implements WithMultipleSheets
{
    use Exportable;
    
    protected $data;
    protected $data_cat;
    protected $company_id;

    public function __construct($data, $company_id)
    {
        $this->data = $data;
        $this->company_id = $company_id;

        $this->data_cat = collect([]);

        $leyends = TagsRmCategoryRisk::select('name');
        $leyends->company_scope = $this->company_id;
        $leyends = $leyends->get();

        /*$leyends = [
            "Estratégico - Direccionamiento Estratégico/Gobernabilidad",
            "Estratégico - Modelo de Operación y Estructura",
            "Estratégico - Financiero/Sostenibilidad",
            "Estratégico - Político y Regulatorio",
            "Estratégico - Tecnológico y de Gestión de la Información",
            "Estratégico - Gestión Capital Humano",
            "Estratégico - Alianzas Estratégicas",
            "Estratégico - Portafolio de Productos/Servicios",
            "Estratégico - Investigación, innovación y desarrollo",
            "Estratégico - Reputacional",
            "Operativo - Proceso",
            "Operativo - Atención de pacientes",
            "Operativo - Ambiental",
            "Operativo - Fraude, corrupción, LA/FT",
            "Puro - Naturaleza",
            "Puro - Físico",
            "Puro - laboral",
            "Puro - Social"
        ];*/

        foreach ($leyends as $key => $value)
        {
            $this->data_cat->push(['leyend'=>$value->name]);
        }
    }

    /**
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        $sheets[] = new RiskMatrixImportTemplate($this->data, $this->company_id);
        $sheets[] = new WarningImportTemplate();
        $sheets[] = new RiskCategoriesExcel($this->data_cat);

        return $sheets;
    }
}
