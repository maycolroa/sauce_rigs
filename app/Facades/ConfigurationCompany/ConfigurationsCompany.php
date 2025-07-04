<?php

namespace App\Facades\ConfigurationCompany;

use Illuminate\Support\Facades\App;
use App\Models\Administrative\Configurations\ConfigurationCompany;
use Exception;
use Session;

class ConfigurationsCompany
{
    /**
     * Id of the company
     *
     * @var Integer
     */
    private $company;

    /**
     * Key attribute
     *
     * @var String
     */
    private $key;

    /**
     * Value attribute
     *
     * @var String
     */
    private $value;

    /**
     * The possible keys and their descriptions
     *
     * @var Array
     */
    private $keys;

    public function __construct()
    {
        $this->company = Session::get('company_id') ? Session::get('company_id') : null;

        $this->keys = [
            'location_level_form' => 'Nivel localización en formulario',
            'days_alert_expiration_date_action_plan' => 'Días de alerta por fecha de vencimiento cercana para los planes de acción',
            'days_alert_expiration_date_contract_file_upload' => 'Días de alerta por fecha de vencimiento cercana para los archivos cargados en el módulo de contratistas',
            'show_action_plans' => 'Mostrar planes de acción en el formulario de matriz de peligros',
            'days_alert_without_activity' => 'Dias de alerta para contratistas sin actividad',
            'validate_qualification_list_check' => 'Validar calificaciones en la lista de chequeo',
            'inventory_management' => 'Utilizar inventario en el modulo de EPP',
            'text_letter_epp' => 'Texto carta de entrega epp',
            'expired_elements_asigned' => 'Notificar por vencimiento de elementos asignados',
            'days_alert_expiration_date_elements' => 'Dias de alerta por vencimiento de elementos asignados',
            'users_notify_element_expired' => 'Usuarios a notificar el vencimiento de los elementos asignados',
            'users_notify_expired_absenteeism_expired' => 'Usuarios a notificar el vencimiento de las incapacidades',
            'expired_absenteeism' => 'Notificar por vencimiento de reposos ausentismo',
            'days_alert_expiration_date_absenteeism' => 'Dias de primera alerta por vencimiento de incapacidad',
            'days_alert_expiration_date_absenteeism_2' => 'Dias de segunda alerta por vencimiento de incapacidad',
            'location_level_form_table_inspectiona' => 'Nivel de localización a mostrar en la tabla de inspecciones',
            'stock_minimun' => 'Notificar por existencia por debajo del stock minimo configurado por elemento',
            'users_notify_stock_minimun' => 'Usuarios a notifica por existencia minima',
            'mandatory_action_plan_inspections' => 'Parametro para pedir o no planes de accion segun la calificacion del item dentro de las inspecciones no planeadas',
            'mandatory_level_risk_inspections' => 'Parametro para pedir o no campo nivel de riesgo del item dentro de las inspecciones planeadas',
            'filter_inspections' => 'Activar filtrado de inspecciones segun configuracion de usuarios',
            'location_level_form_user_inspection_filter' => 'Nivel de localización a mostrar para el filtrado de inspecciones',
            'contracts_delete_file_upload' => 'Parametro para establecer el permiso para borrado de archivos cargados',
            'reports_opens_notify' => 'Notificar por reportes con muchos dias abiertos',
            'days_alert_expiration_report_notify' => 'Numero de dias para enviar la notificacion por reportes abiertos',
            'users_notify_expired_report' => 'Usuarios a notifica por los reportes',
            'reports_resumen_month' => 'Activar envio de resumen mensual de eventos pendientes a vencerse en Reincorporaciones',
            'text_letter_dotation' => 'Texto carta de entrega epp dotacion',
            'text_letter_team' => 'Texto de carta de entrega de epp equipo',
            'users_notify_incapacitated' => 'Usuarios a notificar las incapacidades que superan el numero de dias configurados',
            'notify_incapacitated' => 'Activar las notificacion sobre incapacidades',
            'days_notify_incapacitated' => 'Primera notificacion por incapacidad',
            'days_notify_incapacitated_2' => 'Segunda notificacion por incapacidad',
            'days_notify_incapacitated_3' =>  'Tercera notificacion por incapacidad',
            'users_notify_report_license' => 'Usuarios a notificar el reporte de licencias',
            'filters_date_license' => 'Filtros de fechas a aplicar para el envio del reporte de licencias',
            'license_reports_sends' => 'Reportes seleccionados para realizar el envio',
            'roles_newsletter' => 'Roles a los cuales se les enviara el boletin',
            'contracts_use_proyect' => 'Activar el uso de proyectos para contratistas',
            'company_there_integration_contract' => 'La compañia utiliza integracion en el modulo de contratistas',
            'danger_matrix_block_old_year' => 'Bloquea las matrices de peligros pertenecientes a años anteriores al actual',
            'contract_validate_inactive_employee' => 'Pedir motivo y documento al momento de la inactivacion',
            'contract_notify_file_expired' => 'Activar las notificaciones al contratante sobre los archivos proximos a vencerse',
            'contract_notify_file_expired_user' => 'Usuario contratante a notificar sobre los archivos proximos a vencerse',
            'legal_matrix_risk_opportunity' => 'Activa la funcionalidad de riesgos y oportunidades en las calificaciones de las leyes',
            'criticality_level_inspections' => 'Muestra nuevo campo en el formulario de inspecciones planeadas para asignar un nivel de criticidad a los items',
            'users_notify_criticality_level_inspections' => 'Uusarios a los cuales notificar la alerta por criticidad de alguno de los items',
            'contracts_view_responsibles' => 'Permitir que solo los responsables de las contratistas puedan realizar alguna acción sobre ellas y todos los demas usuarios solo podran ver la informació que brinda la tabla'
        ];
    }

    /**
     * Set the id of the company
     *
     * @param Integer $company
     * @return $this
     */
    public function company($company)
    {
        if (!is_numeric($company))
            throw new \Exception('Invalid company format');

        $this->company = $company;

        return $this;
    }

    /**
     * Set the key
     *
     * @param String $key
     * @return $this
     */
    public function key($key)
    {
        if (!is_string($key))
            throw new \Exception('Invalid key format');

        $this->key = $key;

        return $this;
    }

    /**
     * Set the value
     *
     * @param String $value
     * @return $this
     */
    public function value($value)
    {
        if (empty($value))
            throw new \Exception('Invalid value format');

        $this->value = $value;

        return $this;
    }

    /**
     * Find the configuration by its key and return its value
     *
     * @param String $key
     * @return Array
     * @return String
     */
    public function findByKey($key)
    {
        if (!$key)
            throw new \Exception('key required');

        if (empty($this->company))
            throw new \Exception('A valid company was not entered');

        $configuration = ConfigurationCompany::select('value')->where('key', $key);
        $configuration->company_scope = $this->company;
        $configuration = $configuration->first();
    
        if (!$configuration)
            throw new Exception('Parameter not found in configuration company table');

        $value = json_decode($configuration->value);

        if(gettype($value) == "object" || gettype($value) == 'array')
            return $value;

        return $configuration->value;
    }

    /**
     * Find the configuration by its key and return its value for all the companies
     *
     * @param String $key
     * @return Array
     */
    public function findAllCompanyByKey($key)
    {
        if (!$key)
            throw new \Exception('key required');

        $configurations = ConfigurationCompany::select('company_id', 'value')->where('key', $key)->withoutGlobalScopes()->get();

        /*if ($configurations->count() == 0)
            throw new Exception('Parameter not found in configuration company table');*/
        
        $data = [];

        foreach ($configurations as $keyItem => $valueItem)
        {
            $value = json_decode($valueItem->value);

            if(gettype($value) == "object" || gettype($value) == 'array')
                $configurations[$keyItem]->value = $value;

            array_push($data, [
                "company_id" => $valueItem->company_id,
                "value" => $valueItem->value
            ]);
        }

        return $data;
    }

    /**
     * Returns an array with all configurations and their values, if any
     *
     * @return Array
     */
    public function findAll()
    {
        $data = [];

        foreach ($this->keys as $key => $value) 
        {
            try
            {
                $value = $this->findByKey($key);
            } catch(Exception $e){
                $value = '';
            }

            $data[$key] = $value;
        }

        return $data;
    }

    /**
     * Save the company configuration according to the defined key
     * 
     */
    public function save()
    {
        if (empty($this->key))
            throw new \Exception('A valid key has not been entered.');

        if (empty($this->value))
            throw new \Exception('A valid value has not been entered.');

        $configuration = ConfigurationCompany::where('key', $this->key);
        
        if ($this->key == 'roles_newsletter')
            $configuration->company_scope = 1;
        else
            $configuration->company_scope = $this->company;

        $configuration = $configuration->first();

        if (!$configuration)
        {
            ConfigurationCompany::create([
                'company_id' => $this->company,
                'key' => strtolower($this->key),
                'value' => $this->value,
                'observation' => isset($this->keys[$this->key]) ? $this->keys[$this->key] : ''
            ]);
        }
        else
        {
            $configuration->update([
                'value' => $this->value,
                'observation' => isset($this->keys[$this->key]) ? $this->keys[$this->key] : ''
            ]);
        }

        $this->key = null;
        $this->value = null;
    }
}