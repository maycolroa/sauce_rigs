<?php

namespace App\Vuetable;

use Session;
use Exception;
use App\Models\General\Team;
use App\Traits\LocationFormTrait;
use App\Traits\ConfigurableFormTrait;
use Illuminate\Support\Facades\Auth;
use App\Models\Administrative\Configurations\ConfigurationCompany;

class VuetableColumnManager
{
    use LocationFormTrait;
    use ConfigurableFormTrait;

    protected $team;
    protected $company;
    protected $user;
    protected $keywords;
    protected $configuration;

    /**
     * defines the availables tables
     *
     * IMPORTANT NOTE:
     * THERE MUST BE A METHOD THAT RETURNS THE DATA FROM THE COLUMNS WITH 
     * THE SAME EXACT NAME FOR EACH NAME WITHIN THIS ARRAY
     *
     * THE NAME ALSO MATCHES THE NAME OF THE TABLE CONFIGURATION IN THE JS FILE 
     * (THE "-" WILL BE SUBSTITUTED BY "EMPTY" SO THAT THEY 
     * WILL BE OMITTED FROM THE NAME IN THE JS)
     * 
     * @var array
     */
    const TABLES = [
        'administrativeusers',
        'administrativeroles',
        'administrativeemployees',
        'industrialsecuredangermatrix',
        'industrialsecuredangermatrixreport',
        'industrialsecuredangermatrixreporthistory',
        'legalAspectsfileUpload',
        'legalaspectslmlawsqualify',
        'reinstatementschecks',
        'reinstatementschecksform',
        'dangerousconditionsinspections',
        'dangerousconditionsinspectionsqualification',
        'dangerousconditionsinspectionsreport',
        'dangerousconditionsinspectionsreporttype2',
        'dangerousconditionsinspectionsreportgestion',
        'industrialsecureriskmatrix',
        'dangerousconditionsreport',
        'dangerousconditionsinspectionsrequestfirm',
        'industrialsecureriskmatrixreport',
        'industrialsecureriskmatrixreportresidual',
        'industrialsecureeppslocation',
        'industrialsecureroadsafetyvehicles',
        'industrialsecureroadsafetydrivers',
        'industrialsecureroadsafetyinspections',
        'roadsafetyinspectionsqualification',
        'legalaspectscontractsemployees',
        'legalaspectscontractsemployeesviewcontractor',
        'legalaspectsinformscontracts',
        'legalaspectsinformscontractslesse',
        'legalaspectscontractdocumentsconsultingemployeereport',
        'legalaspectscontractdocumentsconsultingemployeereportexpired',
        'legalaspectscontractdocumentsconsultingcontractreport',
        'legalaspectscontractdocumentsconsultingcontractreportexpired',
        'legalaspectscontractdocumentsconsultingemployeereportclosewinning',
        'legalaspectscontractdocumentsconsultingcontractreportclosewinning',
        'legalaspectscontractor'
    ];

    protected $customColumnsName;

    /**
     * create an instance and set the attribute class
     * @param array $regionals
     */
    function __construct($customColumnsName)
    {
        $this->customColumnsName = str_replace('-', '', $customColumnsName);
        $this->team = Team::where('name', Session::get('company_id'))->first();
        $this->company = Session::get('company_id');
        $this->user = Auth::user();
        $this->keywords = $this->user->getKeywords();

        $this->configuration = ConfigurationCompany::select('value')->where('key', 'contracts_use_proyect');
        $this->configuration->company_scope = $this->company;
        $this->configuration = $this->configuration->first();
    }

    /**
     * Returns the data for the information in the view according to
     * the paramter of the name of the table
     *
     * if the name of the table is not defined or there are no methods 
     * matching the name an exception will be returned
     * 
     * @return collection
     */
    public function getColumnsData()
    {
        if (empty($this->customColumnsName))
            throw new \Exception('the name of the table has not been defined');

        $columnsData = collect([]);

        if (in_array($this->customColumnsName, $this::TABLES))
        {
            $method = $this->customColumnsName;
            $columnsData->put('fields', $this->$method());
        }
        else
        {
            throw new \Exception('the name of the table is not defined in the class');
        }
        
        return $columnsData->toArray();
    }

    /**
     * returns the columns for the danger matrix
     * 
     * @return Array
     */
    public function industrialsecuredangermatrix()
    {
        $colums = [
            ['name' => 'sau_dangers_matrix.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_dangers_matrix.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_dangers_matrix.year', 'data'=> 'year', 'title'=> 'Año', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_users.name', 'data'=> 'supervisor', 'title'=> 'Usuario Modifica', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'date', 'data'=> 'date', 'title'=> 'Fecha de actualización', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'approved', 'data'=> 'approved', 'title'=> '¿Aprobada?', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function industrialsecureeppslocation()
    {
        $colums = [
            ['name' => 'sau_epp_locations.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_epp_locations.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function industrialsecureriskmatrix()
    {
        $colums = [
            ['name' => 'sau_rm_risks_matrix.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_rm_risks_matrix.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_users.name', 'data'=> 'supervisor', 'title'=> 'Supervisor', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'approved', 'data'=> 'approved', 'title'=> '¿Aprobada?', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    /**
     * returns the columns for the danger matrix
     * 
     * @return Array
     */
    public function industrialsecuredangermatrixreport()
    {
        $colums = [
            ['name' => 'sau_dangers_matrix.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_dm_dangers.name', 'data'=> 'name', 'title'=> 'Peligro', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => 'sau_dm_activity_danger.danger_description', 'data'=> 'danger_description', 'title'=> 'Descripción', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function industrialsecuredangermatrixreporthistory()
    {
        $colums = [
            ['name' => 'sau_dm_report_histories.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_dm_report_histories.danger', 'data'=> 'danger', 'title'=> 'Peligro', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => 'sau_dm_report_histories.danger_description', 'data'=> 'danger_description', 'title'=> 'Descripción', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    /**
     * returns the columns for the location according to the configuration of the company
     * 
     * @return Array
     */
    private function getColumnsLocations($table= '', $headers = [], $searchable = true)
    {
        $colums = [];

        $company = Session::get('company_id') ? Session::get('company_id') : null;

        $confLocation = $this->getLocationFormConfModule();

        $confLocationTableInspections = $this->getLocationFormConfTableInspections();

        $columnsHeader = [
            'regional' => $this->keywords['regional'],
            'headquarter' => $this->keywords['headquarter'],
            'area' => $this->keywords['area'],
            'process' => $this->keywords['process']
        ];

        if (isset($headers['regional']) && $headers['regional'])
            $columnsHeader['regional'] = $headers['regional'];
        
        if (isset($headers['headquarter']) && $headers['headquarter'])
            $columnsHeader['headquarter'] = $headers['headquarter'];

        if (isset($headers['area']) && $headers['area'])
            $columnsHeader['area'] = $headers['area'];

        if (isset($headers['process']) && $headers['process'])
            $columnsHeader['process'] = $headers['process'];


        if ($table == 'inspections')
        {
            if ($confLocationTableInspections['regional'] == 'SI')
                array_push($colums, [
                    'name'=>'sau_employees_regionals.name', 'data'=>'regional', 'title'=>$columnsHeader['regional'], 'sortable'=>true, 'searchable'=> false, 'detail'=>false, 'key'=>false
                ]);

            if ($confLocationTableInspections['headquarter'] == 'SI')
                array_push($colums, [
                    'name'=>'sau_employees_headquarters.name', 'data'=>'headquarter', 'title'=>$columnsHeader['headquarter'], 'sortable'=>true, 'searchable'=> false, 'detail'=>false, 'key'=>false
                ]);

            if ($confLocationTableInspections['process'] == 'SI')
                array_push($colums, [
                    'name'=>'sau_employees_processes.name', 'data'=>'procesos', 'title'=>$columnsHeader['process'], 'sortable'=>true, 'searchable'=> false, 'detail'=>false, 'key'=>false
                ]);

            if ($confLocationTableInspections['area'] == 'SI')
                array_push($colums, [
                    'name'=>'sau_employees_areas.name', 'data'=>'areas', 'title'=>$columnsHeader['area'], 'sortable'=>true, 'searchable'=> false, 'detail'=>false, 'key'=>false
                ]);
        }
        else
        {
            if ($confLocation['regional'] == 'SI')
                array_push($colums, [
                    'name'=>'sau_employees_regionals.name', 'data'=>'regional', 'title'=>$columnsHeader['regional'], 'sortable'=>true, 'searchable'=> $searchable, 'detail'=>false, 'key'=>false
                ]);

            if ($confLocation['headquarter'] == 'SI')
                array_push($colums, [
                    'name'=>'sau_employees_headquarters.name', 'data'=>'headquarter', 'title'=>$columnsHeader['headquarter'], 'sortable'=>true, 'searchable'=> $searchable, 'detail'=>false, 'key'=>false
                ]);

            if ($confLocation['process'] == 'SI')
                array_push($colums, [
                    'name'=>'sau_employees_processes.name', 'data'=>'process', 'title'=>$columnsHeader['process'], 'sortable'=>true, 'searchable'=> $searchable, 'detail'=>false, 'key'=>false
                ]);

            if ($confLocation['area'] == 'SI')
                array_push($colums, [
                    'name'=>'sau_employees_areas.name', 'data'=>'area', 'title'=>$columnsHeader['area'], 'sortable'=>true, 'searchable'=> $searchable, 'detail'=>false, 'key'=>false
                ]);
        }

        return $colums;
    }

    /**
     * returns the columns for the roles
     * 
     * @return Array
     */
    public function administrativeroles()
    {
        if ($this->user->can('roles_manage_defined', $this->team))
            $colums = [
                ['name' => 'sau_roles.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
                ['name' => 'sau_roles.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_roles.description', 'data'=> 'description', 'title'=> 'Descripción', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_roles.type_role', 'data'=> 'type_role', 'title'=> 'Tipo', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_modules.display_name', 'data'=> 'display_name', 'title'=> 'Módulo', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
            ];
        else 
            $colums = [
                ['name' => 'sau_roles.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
                ['name' => 'sau_roles.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_roles.description', 'data'=> 'description', 'title'=> 'Descripción', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ];

        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    /**
     * returns the columns for the files upload
     * 
     * @return Array
     */
    public function legalAspectsfileUpload()
    {
        if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
            $colums = [
                ['name' => 'sau_ct_file_upload_contracts_leesse.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
                /*['name' => 'sau_ct_file_upload_contracts_leesse.id', 'data'=> 'id', 'title'=> 'Código', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],*/
                ['name' => 'sau_ct_file_upload_contracts_leesse.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                /*['name' => 'sau_ct_section_category_items.item_name', 'data'=> 'item_name', 'title'=> 'Item', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],*/
                ['name' => 'sau_ct_contract_employees.name', 'data'=> 'employee_name', 'title'=> 'Empleado', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_contract_employees.identification', 'data'=> 'employee_identification', 'title'=> 'Cédula', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.name', 'data'=> 'user_name', 'title'=> 'Usuario Creador', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.created_at', 'data'=> 'created_at', 'title'=> 'Fecha Creación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.updated_at', 'data'=> 'updated_at', 'title'=> 'Fecha Actualización', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.state', 'data'=> 'state', 'title'=> 'Estado', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.module', 'data'=> 'module', 'title'=> 'Modulo', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
            ];
        else 
            $colums = [
                ['name' => 'sau_ct_file_upload_contracts_leesse.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
                /*['name' => 'sau_ct_file_upload_contracts_leesse.id', 'data'=> 'id', 'title'=> 'Código', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],*/
                ['name' => 'sau_ct_information_contract_lessee.social_reason', 'data'=> 'social_reason', 'title'=> 'Contratistas', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                /*['name' => 'sau_ct_section_category_items.item_name', 'data'=> 'item_name', 'title'=> 'Item', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],*/
                ['name' => 'sau_ct_contract_employees.name', 'data'=> 'employee_name', 'title'=> 'Empleado', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_contract_employees.identification', 'data'=> 'employee_identification', 'title'=> 'Cédula', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.name', 'data'=> 'user_name', 'title'=> 'Usuario Creador', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.created_at', 'data'=> 'created_at', 'title'=> 'Fecha Creación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.updated_at', 'data'=> 'updated_at', 'title'=> 'Fecha Actualización', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.state', 'data'=> 'state', 'title'=> 'Estado', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.module', 'data'=> 'module', 'title'=> 'Modulo', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
            ];

        if ($this->configuration && $this->configuration->value == 'SI')
            $colums = array_merge($colums, [
                ['name' => 'sau_ct_proyects.name', 'data' => 'proyects', 'title' => 'Proyectos', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
            ]);

        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function legalaspectslmlawsqualify()
    {
        if ($this->user->hasRole('Superadmin', $this->team))
        {
            $colums = [
                ['name' => 'sau_lm_laws.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
                ['name' => 'sau_lm_laws_types.name', 'data' => 'law_type', 'title' => 'Tipo Norma', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_laws.law_number', 'data' => 'law_number', 'title' => 'Número', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_laws.law_year', 'data' => 'law_year', 'title' => 'Año', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_laws.description', 'data' => 'description', 'title' => 'Descripción', 'sortable' => true, 'searchable' => false, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_risks_aspects.name', 'data' => 'risk_aspect', 'title' => 'Tema Ambiental', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_sst_risks.name', 'data' => 'sst_risk', 'title' => 'Tema SST', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_entities.name', 'data' => 'entity', 'title' => 'Ente', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_system_apply.name', 'data' => 'system_apply', 'title' => 'Sistema', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_laws.repealed', 'data' => 'repealed', 'title' => 'Derogada', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'hide', 'data'=> 'hide', 'title'=> '¿Oculta?', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ];
        }
        else
        {
            $colums = [
                ['name' => 'sau_lm_laws.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
                ['name' => 'sau_lm_laws_types.name', 'data' => 'law_type', 'title' => 'Tipo Norma', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_laws.law_number', 'data' => 'law_number', 'title' => 'Número', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_laws.law_year', 'data' => 'law_year', 'title' => 'Año', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_laws.description', 'data' => 'description', 'title' => 'Descripción', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_risks_aspects.name', 'data' => 'risk_aspect', 'title' => 'Tema Ambiental', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_sst_risks.name', 'data' => 'sst_risk', 'title' => 'Tema SST', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_entities.name', 'data' => 'entity', 'title' => 'Ente', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_system_apply.name', 'data' => 'system_apply', 'title' => 'Sistema', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
                ['name' => 'sau_lm_laws.repealed', 'data' => 'repealed', 'title' => 'Derogada', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
            ];
        }

        return $colums;
    }

    /**
     * returns the columns for the files upload
     * 
     * @return Array
     */
    public function administrativeusers()
    {
        if ($this->user->hasRole('Arrendatario', $this->team) || $this->user->hasRole('Contratista', $this->team))
            $colums = [
                ['name' => 'sau_users.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
                ['name' => 'sau_users.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.email', 'data'=> 'email', 'title'=> 'Email', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.document', 'data'=> 'document', 'title'=> 'Documento', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.document_type', 'data'=> 'document_type', 'title'=> 'Tipo de Documento', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.active', 'data'=> 'active', 'title'=> '¿Activo?', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
            ];
        else 
            $colums = [
                ['name' => 'sau_users.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
                ['name' => 'sau_users.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.email', 'data'=> 'email', 'title'=> 'Email', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.document', 'data'=> 'document', 'title'=> 'Documento', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.document_type', 'data'=> 'document_type', 'title'=> 'Tipo de Documento', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_roles.name', 'data'=> 'role', 'title'=> 'Rol', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.active', 'data'=> 'active', 'title'=> '¿Activo?', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.last_login_at', 'data'=> 'last_login_at', 'title'=> 'Última sesión', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
            ];

        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    /**
     * returns the columns for the danger matrix
     * 
     * @return Array
     */
    public function administrativeemployees()
    {
        $formModel = $this->getFormModel('form_employee');

        $colums = [
            ['name' => 'sau_employees.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_employees.identification', 'data'=> 'identification', 'title'=> 'Identificación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_employees.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_employees.sex', 'data'=> 'sex', 'title'=> 'Sexo', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_employees.active', 'data'=> 'active', 'title'=> '¿Activo?', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_employees.income_date', 'data'=> 'income_date', 'title'=> 'Fecha de Ingreso', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_employees_positions.name', 'data'=> 'position', 'title'=> $this->keywords['position'], 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => 'sau_employees_businesses.name', 'data'=> 'business', 'title'=> $this->keywords['businesses'], 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_employees_eps.name', 'data'=> 'eps', 'title'=> $this->keywords['eps'], 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ]);
        
        if ($formModel == 'vivaAir')
        { 
            $colums = array_merge($colums, [
                ['name' => 'sau_employees_afp.name', 'data'=> 'afp', 'title'=> $this->keywords['afp'], 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
            ]);
        }
        else if ($formModel == 'misionEmpresarial')
        {
            $colums = array_merge($colums, [
                ['name' => 'sau_employees_afp.name', 'data'=> 'afp', 'title'=> $this->keywords['afp'], 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_employees_arl.name', 'data'=> 'arl', 'title'=> $this->keywords['arl'], 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
            ]);
        }

        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    /**
     * returns the columns for the danger matrix
     * 
     * @return Array
     */
    public function reinstatementschecks()
    {
        $formModel = $this->getFormModel('table_check');

        $colums = [
            ['name' => 'sau_reinc_checks.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_reinc_cie10_codes.code', 'data'=> 'code', 'title'=> 'Código CIE 10', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_reinc_checks.disease_origin', 'data'=> 'disease_origin', 'title'=> $this->keywords['disease_origin'], 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_employees_regionals.name', 'data'=> 'regional', 'title'=> $this->keywords['regional'], 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_reinc_checks.state', 'data'=> 'state', 'title'=> 'Estado del Reporte', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false],
            ['name' => 'sau_employees.name', 'data'=> 'name', 'title'=> $this->keywords['employee'], 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        if (!$formModel == 'argos')
        {
            $colums = array_merge($colums, [
                ['name' => 'sau_employees.identification', 'data'=> 'identification', 'title'=> 'Identificación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_reinc_checks.deadline', 'data'=> 'deadline', 'title'=> 'Fecha de Cierre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
            ]);
        }

        if ($formModel == 'misionEmpresarial')
        { 
            $colums = array_merge($colums, [
                ['name' => 'sau_reinc_checks.next_date_tracking', 'data'=> 'next_date_tracking', 'title'=> 'Próximo Seguimiento', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
            ]);
        }

        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }


    /**
     * returns the columns for the danger matrix
     * 
     * @return Array
     */
    public function reinstatementschecksform()
    {
        return $this->reinstatementschecks();
    }

    /**
     * returns the columns for the danger matrix
     * 
     * @return Array
     */
    public function dangerousconditionsinspections()
    {
        $colums = [
            ['name' => 'sau_ph_inspections.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_ph_inspections.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_ph_inspections.state', 'data'=> 'state', 'title'=> '¿Activa?', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_ph_inspections.created_at', 'data'=> 'created_at', 'title'=> 'Fecha de creación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
        ];

        $colums = array_merge($colums, $this->getColumnsLocations('inspections'));
        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    /**
     * returns the columns for the danger matrix
     * 
     * @return Array
     */
    public function dangerousconditionsinspectionsqualification()
    {
        $colums = [
            ['name' => 'sau_ph_inspection_items_qualification_area_location.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_users.name', 'data'=> 'qualificator', 'title'=> 'Calificador', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_ph_inspection_items_qualification_area_location.qualification_date', 'data'=> 'qualification_date', 'title'=> 'Fecha Calificación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => 'sau_ph_inspection_qualification_state.state', 'data'=> 'state', 'title'=> 'Estado', 'sortable'=> false, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ]);

        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function dangerousconditionsinspectionsreport()
    {
        $colums = [
            ['name' => 'sau_ph_inspection_items_qualification_area_location.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_ph_inspections.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations('', [], false));
        $colums = array_merge($colums, [
            ['name' => 'section', 'data'=> 'section', 'title'=> 'Temas', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_inspecciones', 'data'=> 'numero_inspecciones', 'title'=> '# Inspecciones', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_items_cumplimiento', 'data'=> 'numero_items_cumplimiento', 'title'=> '# Items Cumplimiento', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_items_no_cumplimiento', 'data'=> 'numero_items_no_cumplimiento', 'title'=> '# Items No Cumplimiento', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_items_cumplimiento_parcial', 'data'=> 'numero_items_cumplimiento_parcial', 'title'=> '# Items Cumplimiento Parcial', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'porcentaje_items_cumplimiento', 'data'=> 'porcentaje_items_cumplimiento', 'title'=> '% Items Cumplimiento', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'porcentaje_items_no_cumplimiento', 'data'=> 'porcentaje_items_no_cumplimiento', 'title'=> '% Items No Cumplimiento', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'porcentaje_items_cumplimiento_parcial', 'data'=> 'porcentaje_items_cumplimiento_parcial', 'title'=> '% Items Cumplimiento Parcial', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_planes_ejecutados', 'data'=> 'numero_planes_ejecutados', 'title'=> '# Planes de Acción Realizados', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_planes_no_ejecutados', 'data'=> 'numero_planes_no_ejecutados', 'title'=> '# Planes de Acción No Realizados', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ]
        ]);

        return $colums;
    }

    public function dangerousconditionsinspectionsreporttype2()
    {
        $colums = [
            ['name' => 'sau_ph_inspection_items_qualification_area_location.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_ph_inspections.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations('', [], false));
        $colums = array_merge($colums, [
            ['name' => 'section', 'data'=> 'section', 'title'=> 'Temas', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_inspecciones', 'data'=> 'numero_inspecciones', 'title'=> '# Inspecciones', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_items_cumplimiento', 'data'=> 'numero_items_cumplimiento', 'title'=> '# Items Cumplimiento', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_items_no_cumplimiento', 'data'=> 'numero_items_no_cumplimiento', 'title'=> '# Items No Cumplimiento', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_items_cumplimiento_parcial', 'data'=> 'numero_items_cumplimiento_parcial', 'title'=> '# Items Cumplimiento Parcial', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'porcentaje_items_cumplimiento', 'data'=> 'porcentaje_items_cumplimiento', 'title'=> '% Items Cumplimiento', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'porcentaje_items_no_cumplimiento', 'data'=> 'porcentaje_items_no_cumplimiento', 'title'=> '% Items No Cumplimiento', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'porcentaje_items_cumplimiento_parcial', 'data'=> 'porcentaje_items_cumplimiento_parcial', 'title'=> '% Items Cumplimiento Parcial', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_planes_ejecutados', 'data'=> 'numero_planes_ejecutados', 'title'=> '# Planes de Acción Realizados', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_planes_no_ejecutados', 'data'=> 'numero_planes_no_ejecutados', 'title'=> '# Planes de Acción No Realizados', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ]
        ]);

        return $colums;
    }

    public function dangerousconditionsreport()
    {
        $colums = [
            [ 'name'=> 'sau_ph_reports.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());

        $colums = array_merge($colums, [
            ['name' => 'sau_ph_reports.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name'=> 'sau_users.name', 'data'=> 'user', 'title'=> 'Usuario', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            [ 'name'=> 'sau_ph_conditions.description', 'data'=> 'condition', 'title'=> 'Hallazgo', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            [ 'name'=> 'sau_ph_conditions_types.description', 'data'=> 'type', 'title'=> 'Tipo de reporte', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            [ 'name'=> 'sau_ph_reports.rate', 'data'=> 'rate', 'title'=> 'Severidad', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            [ 'name'=> 'sau_ph_reports.created_at', 'data'=> 'created_at', 'title'=> 'Fecha de creación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            [ 'name'=> 'sau_ph_reports.state', 'data'=> 'state', 'title'=> 'Estado', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ]);

        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function dangerousconditionsinspectionsrequestfirm()
    {
        $colums = [
            ['name' => 'sau_ph_inspection_items_qualification_area_location.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'u.name', 'data'=> 'user_firm', 'title'=> 'Usuario Firmante', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_users.name', 'data'=> 'qualificator', 'title'=> 'Calificador', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_ph_inspection_items_qualification_area_location.qualification_date', 'data'=> 'qualification_date', 'title'=> 'Fecha Calificación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function industrialsecureriskmatrixreport()
    {
        $colums = [
            ['name' => 'sau_dangers_matrix.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_rm_risk.name', 'data'=> 'name', 'title'=> 'Riesgo', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_rm_risk.category', 'data'=> 'category', 'title'=> 'Categoría', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function industrialsecureriskmatrixreportresidual()
    {
        $colums = [
            ['name' => 'sau_dangers_matrix.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_rm_risk.name', 'data'=> 'name', 'title'=> 'Riesgo', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_rm_risk.category', 'data'=> 'category', 'title'=> 'Categoría', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function dangerousconditionsinspectionsreportgestion()
    {
        $colums = [
            ['name' => 'sau_ph_inspection_items_qualification_area_location.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_ph_inspections.name', 'data'=> 'name', 'title'=> 'Nombre de la inspección', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_ph_inspection_sections.name', 'data'=> 'section', 'title'=> 'Tema', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_ph_inspection_section_items.description', 'data'=> 'item', 'title'=> 'Descripción del Item', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_ph_qualifications_inspections.name', 'data'=> 'qualification', 'title'=> 'Calificación', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'tiene_plan_action', 'data'=> 'tiene_plan_action', 'title'=> '¿Tiene plan de acción?', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_ph_qualifications_inspections.qualification_date', 'data'=> 'qualification_date', 'title'=> 'Fecha de ejecución de la inspección', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_users.name', 'data'=> 'qualificator', 'title'=> 'Usuario Calificador', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ];

        $colums = array_merge($colums, $this->getColumnsLocationsLastNivel('', [], false));

        return $colums;
    }

    private function getColumnsLocationsLastNivel($headers = [], $searchable = true)
    {
        $colums = [];

        $company = Session::get('company_id') ? Session::get('company_id') : null;

        $confLocation = $this->getLocationFormConfModule();

        $confLocationTableInspections = $this->getLocationFormConfTableInspections();

        $columnsHeader = [
            'regional' => $this->keywords['regional'],
            'headquarter' => $this->keywords['headquarter'],
            'area' => $this->keywords['area'],
            'process' => $this->keywords['process']
        ];

        if (isset($headers['regional']) && $headers['regional'])
            $columnsHeader['regional'] = $headers['regional'];
        
        if (isset($headers['headquarter']) && $headers['headquarter'])
            $columnsHeader['headquarter'] = $headers['headquarter'];

        if (isset($headers['area']) && $headers['area'])
            $columnsHeader['area'] = $headers['area'];

        if (isset($headers['process']) && $headers['process'])
            $columnsHeader['process'] = $headers['process'];


        if ($confLocationTableInspections['area'] == 'SI')
            array_push($colums, [
                'name'=>'sau_employees_areas.name', 'data'=>'area', 'title'=>$columnsHeader['area'], 'sortable'=>true, 'searchable'=> false, 'detail'=>false, 'key'=>false
            ]);

        else if ($confLocationTableInspections['process'] == 'SI')
            array_push($colums, [
                'name'=>'sau_employees_processes.name', 'data'=>'process', 'title'=>$columnsHeader['process'], 'sortable'=>true, 'searchable'=> false, 'detail'=>false, 'key'=>false
            ]);

        else if ($confLocationTableInspections['headquarter'] == 'SI')
            array_push($colums, [
                'name'=>'sau_employees_headquarters.name', 'data'=>'headquarter', 'title'=>$columnsHeader['headquarter'], 'sortable'=>true, 'searchable'=> false, 'detail'=>false, 'key'=>false
            ]);

        else if ($confLocationTableInspections['regional'] == 'SI')
            array_push($colums, [
                'name'=>'sau_employees_regionals.name', 'data'=>'regional', 'title'=>$columnsHeader['regional'], 'sortable'=>true, 'searchable'=> false, 'detail'=>false, 'key'=>false
            ]);

        return $colums;
    }

    public function industrialsecureroadsafetyvehicles()
    {
        $colums = [
            ['name' => 'sau_rs_vehicles.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_rs_vehicles.plate', 'data'=> 'plate', 'title'=> 'Placa', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_rs_vehicles.type_vehicle', 'data'=> 'type_vehicle', 'title'=> 'Tipo de Vehiculo', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_rs_vehicles.due_date_soat', 'data'=> 'due_date_soat', 'title'=> 'Vencimiento de SOAT', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_rs_vehicles.due_date_mechanical_tech', 'data'=> 'due_date_mechanical_tech', 'title'=> 'Vencimiento de Tecnomecanica', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function industrialsecureroadsafetydrivers()
    {
        $colums = [
            ['name' => 'sau_rs_drivers.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_employees.name', 'data'=> 'name', 'title'=> 'Conductor', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_rs_drivers.type_license', 'data'=> 'type_license', 'title'=> 'Tipo de licencia', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_rs_drivers.date_license', 'data'=> 'date_license', 'title'=> 'Vigencia Licencia', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function industrialsecureroadsafetyinspections()
    {
        $colums = [
            ['name' => 'sau_rs_inspections.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_rs_inspections.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_rs_inspections.state', 'data'=> 'state', 'title'=> '¿Activa?', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_rs_inspections.created_at', 'data'=> 'created_at', 'title'=> 'Fecha de creación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function roadsafetyinspectionsqualification()
    {
        $colums = [
            ['name' => 'sau_rs_inspections_qualified.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_users.name', 'data'=> 'qualificator', 'title'=> 'Calificador', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_rs_vehicles.plate', 'data'=> 'plate', 'title'=> 'Placa de vehiculo', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_rs_inspections_qualified.qualification_date', 'data'=> 'qualification_date', 'title'=> 'Fecha Calificación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function legalaspectscontractsemployees()
    {
        $colums = [
            ['name' => 'sau_ct_contract_employees.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            [ 'name' => 'sau_ct_contract_employees.identification', 'data' => 'identification', 'title' => 'Identificación', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_contract_employees.name', 'data' => 'name', 'title' => 'Nombre', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_contract_employees.position', 'data' => 'position', 'title' => 'Cargo', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_contract_employees.email', 'data' => 'email', 'title' => 'Email', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_contract_employees.state', 'data' => 'state', 'title' => 'Estado Documentos', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_contract_employees.state_employee', 'data' => 'state_employee', 'title' => 'Estado Empleado', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_contract_employees.liquidated', 'data' => 'liquidated', 'title' => '¿Liquidado?', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
        ];

        if ($this->configuration && $this->configuration->value == 'SI')
            $colums = array_merge($colums, [
                ['name' => 'sau_ct_proyects.name', 'data' => 'proyects', 'title' => 'Proyectos', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
            ]);

        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function legalaspectscontractsemployeesviewcontractor()
    {
        $colums = [
            ['name' => 'sau_ct_contract_employees.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            [ 'name' => 'sau_ct_contract_employees.identification', 'data' => 'identification', 'title' => 'Identificación', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_contract_employees.name', 'data' => 'name', 'title' => 'Nombre', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_contract_employees.position', 'data' => 'position', 'title' => 'Cargo', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_contract_employees.email', 'data' => 'email', 'title' => 'Email', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_contract_employees.state', 'data' => 'state', 'title' => 'Estado Documentos', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_contract_employees.state_employee', 'data' => 'state_employee', 'title' => 'Estado Empleado', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_contract_employees.liquidated', 'data' => 'liquidated', 'title' => '¿Liquidado?', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
        ];

        if ($this->configuration && $this->configuration->value == 'SI')
            $colums = array_merge($colums, [
                ['name' => 'sau_ct_proyects.name', 'data' => 'proyects', 'title' => 'Proyectos', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
            ]);

        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function legalaspectsinformscontracts()
    {
        $colums = [
            [ 'name' => 'sau_ct_inform_contract.id', 'data' => 'id', 'title' => 'ID', 'sortable' => false, 'searchable' => false, 'detail' => false, 'key' => true ],
            [ 'name' => 'sau_ct_information_contract_lessee.nit', 'data' => 'nit', 'title' => 'NIT', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_information_contract_lessee.social_reason', 'data' => 'social_reason', 'title' => 'Razón social', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_inform_contract.inform_date', 'data' => 'inform_date', 'title' => 'Fecha evaluación', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_inform_contract.periodo', 'data' => 'periodo', 'title' => 'Período', 'sortable' => true, 'searchable' => false, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_inform_contract.state', 'data' => 'state', 'title' => 'Estado', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
        ];

        if ($this->configuration && $this->configuration->value == 'SI')
            $colums = array_merge($colums, [
                ['name' => 'sau_ct_proyects.name', 'data' => 'proyects', 'title' => 'Proyecto', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
            ]);

        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function legalaspectsinformscontractslesse()
    {
        $colums = [
            [ 'name' => 'sau_ct_inform_contract.id', 'data' => 'id', 'title' => 'ID', 'sortable' => false, 'searchable' => false, 'detail' => false, 'key' => true ],
            [ 'name' => 'sau_ct_inform_contract.inform_date', 'data' => 'inform_date', 'title' => 'Fecha evaluación', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_inform_contract.periodo', 'data' => 'periodo', 'title' => 'Período', 'sortable' => true, 'searchable' => false, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_inform_contract.state', 'data' => 'state', 'title' => 'Estado', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
        ];

        if ($this->configuration && $this->configuration->value == 'SI')
            $colums = array_merge($colums, [
                ['name' => 'sau_ct_proyects.name', 'data' => 'proyects', 'title' => 'Proyecto', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
            ]);

        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    public function legalaspectscontractdocumentsconsultingemployeereport()
    {
        $colums = [
            ['name' => 'id', 'data' => 'id', 'title' =>  'ID', 'sortable' => false, 'searchable' => true, 'detail' => false, 'key' => true ],
            ['name' => 'sau_ct_information_contract_lessee.social_reason', 'data' => 'contract', 'title' =>  'Contratista', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_contract_employees.name', 'data' => 'employee', 'title' =>  'Empleado', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_activities.name', 'data' => 'activity', 'title' =>  'Actividad', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_activities_documents.name', 'data' => 'document', 'title' =>  'Documento', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
        ];

        if ($this->configuration && $this->configuration->value == 'SI')
            $colums = array_merge($colums, [
                ['name' => 'sau_ct_proyects.name', 'data' => 'proyects', 'title' => 'Proyecto', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
            ]);

        return $colums;
    }

    public function legalaspectscontractdocumentsconsultingemployeereportexpired()
    {
        $colums = [
            ['name' => 'id', 'data' => 'id', 'title' =>  'ID', 'sortable' => false, 'searchable' => true, 'detail' => false, 'key' => true ],
            ['name' => 'sau_ct_information_contract_lessee.social_reason', 'data' => 'contract', 'title' =>  'Contratista', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_contract_employees.name', 'data' => 'employee', 'title' =>  'Empleado', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_activities.name', 'data' => 'activity', 'title' =>  'Actividad', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_activities_documents.name', 'data' => 'document', 'title' =>  'Documento', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_file_upload_contracts_leesse.expirationDate', 'data' => 'expirationDate', 'title' =>  'Fecha de vencimiento', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
        ];

        if ($this->configuration && $this->configuration->value == 'SI')
            $colums = array_merge($colums, [
                ['name' => 'sau_ct_proyects.name', 'data' => 'proyects', 'title' => 'Proyecto', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
            ]);

        return $colums;
    }

    public function legalaspectscontractdocumentsconsultingemployeereportclosewinning()
    {
        $colums = [
            ['name' => 'id', 'data' => 'id', 'title' =>  'ID', 'sortable' => false, 'searchable' => true, 'detail' => false, 'key' => true ],
            ['name' => 'sau_ct_information_contract_lessee.social_reason', 'data' => 'contract', 'title' =>  'Contratista', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_contract_employees.name', 'data' => 'employee', 'title' =>  'Empleado', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_activities.name', 'data' => 'activity', 'title' =>  'Actividad', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_activities_documents.name', 'data' => 'document', 'title' =>  'Documento', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_file_upload_contracts_leesse.expirationDate', 'data' => 'expirationDate', 'title' =>  'Fecha de vencimiento', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
        ];

        if ($this->configuration && $this->configuration->value == 'SI')
            $colums = array_merge($colums, [
                ['name' => 'sau_ct_proyects.name', 'data' => 'proyects', 'title' => 'Proyecto', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
            ]);

        return $colums;
    }

    public function legalaspectscontractdocumentsconsultingcontractreport()
    {
        $colums = [
            ['name' => 'id', 'data' => 'id', 'title' =>  'ID', 'sortable' => false, 'searchable' => true, 'detail' => false, 'key' => true ],
            ['name' => 'sau_ct_information_contract_lessee.social_reason', 'data' => 'contratista', 'title' =>  'Contratista', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_activities.name', 'data' => 'activity', 'title' =>  'Actividad', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_contracts_documents.name', 'data' => 'documento', 'title' =>  'Documento', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
        ];

        return $colums;
    }

    public function legalaspectscontractdocumentsconsultingcontractreportexpired()
    {
        $colums = [
            ['name' => 'id', 'data' => 'id', 'title' =>  'ID', 'sortable' => false, 'searchable' => true, 'detail' => false, 'key' => true ],
            ['name' => 'sau_ct_information_contract_lessee.social_reason', 'data' => 'contratista', 'title' =>  'Contratista', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_activities.name', 'data' => 'activity', 'title' =>  'Actividad', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_contracts_documents.name', 'data' => 'documento', 'title' =>  'Documento', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_file_upload_contracts_leesse.expirationDate', 'data' => 'expirationDate', 'title' =>  'Fecha de vencimiento', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
        ];

        return $colums;
    }

    public function legalaspectscontractdocumentsconsultingcontractreportclosewinning()
    {
        $colums = [
            ['name' => 'id', 'data' => 'id', 'title' =>  'ID', 'sortable' => false, 'searchable' => true, 'detail' => false, 'key' => true ],
            ['name' => 'sau_ct_information_contract_lessee.social_reason', 'data' => 'contratista', 'title' =>  'Contratista', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_activities.name', 'data' => 'activity', 'title' =>  'Actividad', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_contracts_documents.name', 'data' => 'documento', 'title' =>  'Documento', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            ['name' => 'sau_ct_file_upload_contracts_leesse.expirationDate', 'data' => 'expirationDate', 'title' =>  'Fecha de vencimiento', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
        ];

        return $colums;
    }

    public function legalaspectscontractor()
    {
        $colums = [ 
            [ 'name' => 'sau_ct_information_contract_lessee.id', 'data' => 'id', 'title' => 'ID', 'sortable' => false, 'searchable' => false, 'detail' => false, 'key' => true ],
            [ 'name' => 'sau_ct_information_contract_lessee.nit', 'data' => 'nit', 'title' => 'Nit', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_information_contract_lessee.social_reason', 'data' => 'social_reason', 'title' => 'Razón social', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            //[ 'name' => 'sau_ct_information_contract_lessee.type', 'data' => 'type', 'title' => 'Tipo', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_information_contract_lessee.high_risk_work', 'data' => 'high_risk_work', 'title' => '¿Alto riesgo?', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_information_contract_lessee.active', 'data' => 'active', 'title' => '¿Activo?', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_list_check_resumen.total_standard', 'data' => 'total_standard', 'title' => 'Estándares', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_list_check_resumen.total_c', 'data' => 'total_c', 'title' => '#Cumple', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_list_check_resumen.total_nc', 'data' => 'total_nc', 'title' => '#No Cumple', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_list_check_resumen.total_sc', 'data' => 'total_sc', 'title' => '#Sin Calificar', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_list_check_resumen.total_p_c', 'data' => 'total_p_c', 'title' => '%Cumple', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ],
            [ 'name' => 'sau_ct_list_check_resumen.total_p_nc', 'data' => 'total_p_nc', 'title' => '%No Cumple', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
        ];

        if ($this->configuration && $this->configuration->value == 'SI')
            $colums = array_merge($colums, [
                ['name' => 'sau_ct_proyects.name', 'data' => 'proyects', 'title' => 'Proyecto', 'sortable' => true, 'searchable' => true, 'detail' => false, 'key' => false ]
            ]);

        $colums = array_merge($colums, [
            [ 'name' => '', 'data' => 'controlls', 'title' => 'Controles', 'sortable' => false, 'searchable' => false, 'detail' => false, 'key' => false ]
        ]);

        return $colums;
    }
}