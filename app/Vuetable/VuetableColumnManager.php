<?php

namespace App\Vuetable;

use Session;
use Exception;
use App\Models\General\Team;
use App\Traits\LocationFormTrait;
use App\Traits\ConfigurableFormTrait;
use Illuminate\Support\Facades\Auth;

class VuetableColumnManager
{
    use LocationFormTrait;
    use ConfigurableFormTrait;

    protected $team;
    protected $company;
    protected $user;
    protected $keywords;

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
        'legalAspectsfileUpload',
        'reinstatementschecks',
        'reinstatementschecksform',
        'dangerousconditionsinspections',
        'dangerousconditionsinspectionsqualification',
        'dangerousconditionsinspectionsreport',
        'dangerousconditionsinspectionsreporttype2'
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
            ['name' => 'sau_dm_dangers.name', 'data'=> 'name', 'title'=> 'Peligro', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_dm_activity_danger.danger_description', 'data'=> 'danger_description', 'title'=> 'Descripción', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
        $colums = array_merge($colums, [
            ['name' => '', 'data'=> 'controlls', 'title'=> 'Controles', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
        ]);

        return $colums;
    }

    /**
     * returns the columns for the location according to the configuration of the company
     * 
     * @return Array
     */
    private function getColumnsLocations($headers = [], $searchable = true)
    {
        $colums = [];

        $confLocation = $this->getLocationFormConfModule();

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
                ['name' => 'sau_ct_file_upload_contracts_leesse.id', 'data'=> 'id', 'title'=> 'Código', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_section_category_items.item_name', 'data'=> 'item_name', 'title'=> 'Item', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.name', 'data'=> 'user_name', 'title'=> 'Usuario Creador', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.created_at', 'data'=> 'created_at', 'title'=> 'Fecha Creación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.updated_at', 'data'=> 'updated_at', 'title'=> 'Fecha Actualización', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.state', 'data'=> 'state', 'title'=> 'Estado', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
            ];
        else 
            $colums = [
                ['name' => 'sau_ct_file_upload_contracts_leesse.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.id', 'data'=> 'id', 'title'=> 'Código', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_information_contract_lessee.social_reason', 'data'=> 'social_reason', 'title'=> 'Contratistas', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_section_category_items.item_name', 'data'=> 'item_name', 'title'=> 'Item', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_users.name', 'data'=> 'user_name', 'title'=> 'Usuario Creador', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.created_at', 'data'=> 'created_at', 'title'=> 'Fecha Creación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.updated_at', 'data'=> 'updated_at', 'title'=> 'Fecha Actualización', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_ct_file_upload_contracts_leesse.state', 'data'=> 'state', 'title'=> 'Estado', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
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
            ['name' => 'sau_checks.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_reinc_cie10_codes.code', 'data'=> 'code', 'title'=> 'Código CIE 10', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_checks.disease_origin', 'data'=> 'disease_origin', 'title'=> $this->keywords['disease_origin'], 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_employees_regionals.name', 'data'=> 'regional', 'title'=> $this->keywords['regional'], 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_checks.state', 'data'=> 'state', 'title'=> 'Estado del Reporte', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false],
            ['name' => 'sau_employees.name', 'data'=> 'name', 'title'=> $this->keywords['employee'], 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        if (!$formModel == 'argos')
        {
            $colums = array_merge($colums, [
                ['name' => 'sau_employees.identification', 'data'=> 'identification', 'title'=> 'Identificación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
                ['name' => 'sau_checks.deadline', 'data'=> 'deadline', 'title'=> 'Fecha de Cierre', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
            ]);
        }

        if ($formModel == 'misionEmpresarial')
        { 
            $colums = array_merge($colums, [
                ['name' => 'sau_checks.next_date_tracking', 'data'=> 'next_date_tracking', 'title'=> 'Próximo Seguimiento', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
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
    public function dangerousconditionsinspectionsqualification()
    {
        $colums = [
            ['name' => 'sau_ph_inspection_items_qualification_area_location.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_users.name', 'data'=> 'qualificator', 'title'=> 'Calificador', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ],
            ['name' => 'sau_ph_inspection_items_qualification_area_location.qualification_date', 'data'=> 'qualification_date', 'title'=> 'Fecha Calificación', 'sortable'=> true, 'searchable'=> true, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations());
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

        $colums = array_merge($colums, $this->getColumnsLocations([], false));
        $colums = array_merge($colums, [
            ['name' => 'section', 'data'=> 'section', 'title'=> 'Temas', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_inspecciones', 'data'=> 'numero_inspecciones', 'title'=> '# Inspecciones', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_items_cumplimiento', 'data'=> 'numero_items_cumplimiento', 'title'=> '# Items Cumplimiento', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_items_no_cumplimiento', 'data'=> 'numero_items_no_cumplimiento', 'title'=> '# Items No Cumplimiento', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_items_cumplimiento_parcial', 'data'=> 'numero_items_cumplimiento_parcial', 'title'=> '# Items Cumplimiento Parcial', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'porcentaje_items_cumplimiento', 'data'=> 'porcentaje_items_cumplimiento', 'title'=> '% Items Cumplimiento', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'porcentaje_items_no_cumplimiento', 'data'=> 'porcentaje_items_no_cumplimiento', 'title'=> '% Items No Cumplimiento', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'porcentaje_items_cumplimiento_parcial', 'data'=> 'porcentaje_items_cumplimiento_parcial', 'title'=> '% Items Cumplimiento Parcial', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_planes_ejecutados', 'data'=> 'numero_planes_ejecutados', 'title'=> '# Planes de Acción Realizados', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_planes_no_ejecutados', 'data'=> 'numero_planes_no_ejecutados', 'title'=> '# Planes de Acción No Realizados', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ]
        ]);

        return $colums;
    }

    public function dangerousconditionsinspectionsreporttype2()
    {
        $colums = [
            ['name' => 'sau_ph_inspection_items_qualification_area_location.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_ph_inspections.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ]
        ];

        $colums = array_merge($colums, $this->getColumnsLocations([], false));
        $colums = array_merge($colums, [
            ['name' => 'section', 'data'=> 'section', 'title'=> 'Temas', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_inspecciones', 'data'=> 'numero_inspecciones', 'title'=> '# Inspecciones', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_items_cumplimiento', 'data'=> 'numero_items_cumplimiento', 'title'=> '# Items Cumplimiento', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_items_no_cumplimiento', 'data'=> 'numero_items_no_cumplimiento', 'title'=> '# Items No Cumplimiento', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_items_cumplimiento_parcial', 'data'=> 'numero_items_cumplimiento_parcial', 'title'=> '# Items Cumplimiento Parcial', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'porcentaje_items_cumplimiento', 'data'=> 'porcentaje_items_cumplimiento', 'title'=> '% Items Cumplimiento', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'porcentaje_items_no_cumplimiento', 'data'=> 'porcentaje_items_no_cumplimiento', 'title'=> '% Items No Cumplimiento', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'porcentaje_items_cumplimiento_parcial', 'data'=> 'porcentaje_items_cumplimiento_parcial', 'title'=> '% Items Cumplimiento Parcial', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_planes_ejecutados', 'data'=> 'numero_planes_ejecutados', 'title'=> '# Planes de Acción Realizados', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'numero_planes_no_ejecutados', 'data'=> 'numero_planes_no_ejecutados', 'title'=> '# Planes de Acción No Realizados', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ]
        ]);

        return $colums;
    }
}