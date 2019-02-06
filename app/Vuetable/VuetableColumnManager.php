<?php

namespace App\Vuetable;

use Exception;
use App\Traits\LocationFormTrait;

class VuetableColumnManager
{
    use LocationFormTrait;

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
        'industrialsecuredangermatrix',
    ];

    protected $customColumnsName;

    /**
     * create an instance and set the attribute class
     * @param array $regionals
     */
    function __construct($customColumnsName)
    {
        $this->customColumnsName = str_replace('-', '', $customColumnsName);
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
            ['name' => 'sau_dm_activities.id', 'data'=> 'id', 'title'=> 'ID', 'sortable'=> false, 'searchable'=> false, 'detail'=> false, 'key'=> true ],
            ['name' => 'sau_dm_activities.name', 'data'=> 'name', 'title'=> 'Nombre', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
            ['name' => 'supervisor', 'data'=> 'supervisor', 'title'=> 'Supervisor', 'sortable'=> true, 'searchable'=> false, 'detail'=> false, 'key'=> false ],
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
    private function getColumnsLocations()
    {
        $colums = [];

        $confLocation = $this->getLocationFormConfModule();
        
        if ($confLocation['regional'] == 'SI')
            array_push($colums, [
                'name'=>'regional', 'data'=>'regional', 'title'=>'Regional', 'sortable'=>true, 'searchable'=>false, 'detail'=>false, 'key'=>false
            ]);

        if ($confLocation['headquarter'] == 'SI')
            array_push($colums, [
                'name'=>'headquarter', 'data'=>'headquarter', 'title'=>'Sede', 'sortable'=>true, 'searchable'=>false, 'detail'=>false, 'key'=>false
            ]);

        if ($confLocation['area'] == 'SI')
            array_push($colums, [
                'name'=>'area', 'data'=>'area', 'title'=>'Área', 'sortable'=>true, 'searchable'=>false, 'detail'=>false, 'key'=>false
            ]);

        if ($confLocation['process'] == 'SI')
            array_push($colums, [
                'name'=>'process', 'data'=>'process', 'title'=>'Proceso', 'sortable'=>true, 'searchable'=>false, 'detail'=>false, 'key'=>false
            ]);

        return $colums;
    }
}