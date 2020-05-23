<?php

namespace App\Managers;

use App\Models\General\Team;

use Exception;

class BaseManager
{
    /**
     * Escribe en el log el mensaje de error
     *
     * @param Exception $e
     * @return void
     */
    protected function printLogError(Exception $e)
    {
        $errors = "{$e->getMessage()} \n {$e->getTraceAsString()}";
        \Log::error($errors);
    }

    /**
     * Verifica si existe alguna relación con el registro
     *
     * @param $record
     * @param array $relations
     * @return boolean
     */
    protected function checkExistsRelation($record, $relations)
    {
        try
        {
            foreach ($relations as $relation)
            {
                if (COUNT($record->$relation) > 0)
                    return true;
            }

            return false;
        }
        catch (Exception $e) {
            $this->printLogError($e);
            return false;
        }
    }

    /**
     * Devuelve el equipo de la compañia
     *
     * @param int $company
     * @return void
     */
    protected function getTeam($company)
    {
        return  Team::where('name', $company)->first();
    }
}