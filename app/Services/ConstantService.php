<?php

namespace App\Services;

use ReflectionClass;

class ConstantService
{
    /**
     * PREFIJOS
     * DM -> Matriz de peligros
     * CT -> Contratistas
     * LM -> Matriz Legal
     * PH -> Condiciones Peligrosas
     */

    CONST SI_NO = [
        'SI',
        'NO'
    ];

    CONST SEXS = [
        'Masculino',
        'Femenino',
        'Sin Sexo'
    ];

    CONST ROLE_SUPER = 'Superadmin';

    /** Matriz de peligros */

    CONST DM_TYPE_ACTIVITIES = [
        'Rutinaria',
        'No rutinaria'
    ];
    
    CONST DM_GENERATED_DANGERS = [
        'Sitio de trabajo',
        'Vecindad',
        'Fuera del sitio de trabajo'
    ];

    /** FIN Matriz de Peligros */

    /** Contratistas */

    CONST CT_TYPE_EVALUATIONS = [
        'Verificación', 
        'Seguimiento', 
        'Adicional'
    ];

    CONST CT_ROLES = [
        'Arrendatario',
        'Contratista'
    ];

    CONST CT_CONTRACT_CLASSIFICATIONS = [
        'Unidad de producción agropecuaria' => 'UPA',
        'Empresa' => 'Empresa'
    ];

    CONST CT_KINDS_RISKS = [
        'Clase de riesgo I',
        'Clase de riesgo II',
        'Clase de riesgo III',
        'Clase de riesgo IV',
        'Clase de riesgo V'
    ];

    /** FIN Contratistas */

    /** Matriz Legal */

    CONST LM_TYPE_REPELEAD = [
        'SI',
        'NO',
        'Parcial'
    ];

    CONST LM_LAW_STATES = [
        'Sin calificar',
        'En proceso',
        'Terminada'
    ];

    /** FIN Matriz Legal */

    /** Condiciones Peligrosas */

    CONST PH_TYPE_RATES = [
        'SI',
        'NO',
        'Parcial'
    ];

    CONST PH_RATES = [
        'Alto',
        'Medio',
        'Bajo'
    ];

    /** FIN Condiciones Peligrosas */

    /**
     * returns a constant defined by the $key
     * @param  string $key
     * @return string|integer|float|array|object
     */
    public function getConstant($key)
    {
        $thisClass = new ReflectionClass(__CLASS__);
        return $thisClass->getConstant($key);
    }
}