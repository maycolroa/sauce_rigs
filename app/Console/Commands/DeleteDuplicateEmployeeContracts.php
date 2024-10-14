<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalAspects\Contracts\ContractEmployee;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use DB;

class DeleteDuplicateEmployeeContracts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-duplicate-employee-contracts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra los empleados de contractistas duplicados y relaciona todos los documentos a solo 1 de ellos';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $employees = ContractEmployee::selectRaw('
            distinct identification, 
            group_concat(id) AS ids,
            count(identification) AS contar, 
            group_concat(contract_id) AS contracts
        ')
        ->withoutGlobalScopes()
        ->where('company_id', 159)
        ->whereRaw("(identification, contract_id) IN (
            SELECT identification, contract_id
            FROM sau_ct_contract_employees
            where company_id = 159
            GROUP BY identification, contract_id
            HAVING COUNT(*) > 1
        )")
        ->groupBy('identification')
        ->get();

        $employeesDuplicateNotFiles = [];
        $employeesDuplicateFiles = [];

        foreach ($employees as $key => $employee) 
        {
            $employees_id = explode(',', $employee->ids);

            $files = ContractEmployee::selectRaw('
                sau_ct_contract_employees.id AS employee_id,
                COUNT(sau_ct_file_upload_contracts_leesse.id) AS count_files
            ')
            ->withoutGlobalScopes()
            ->leftJoin('sau_ct_file_document_employee', 'sau_ct_file_document_employee.employee_id', 'sau_ct_contract_employees.id')
            ->leftJoin('sau_ct_file_upload_contracts_leesse', 'sau_ct_file_upload_contracts_leesse.id', 'sau_ct_file_document_employee.file_id')
            ->whereIn('sau_ct_contract_employees.id', $employees_id)
            ->groupBy('sau_ct_contract_employees.id')
            ->orderBy('sau_ct_contract_employees.id')
            ->get();

            foreach ($files as $key => $file) 
            {
                if ($file->count_files > 0)
                    array_push($employeesDuplicateFiles, $file->employee_id);
                else
                {
                    if ($key == 0)
                        array_push($employeesDuplicateFiles, $file->employee_id);
                    else
                        array_push($employeesDuplicateNotFiles, $file->employee_id);
                }
            }
        }

        if (COUNT($employeesDuplicateNotFiles) > 0)
        {
            $employees_reubicate = ContractEmployee::whereIn('id', $employeesDuplicateNotFiles)->withoutGlobalScopes()->where('company_id', 159)->get();

            foreach ($employees_reubicate as $key => $reubicate) 
            {
                $reubicate->identification = $reubicate->identification.'- reubicado -'.$reubicate->contract_id.' - '.$key;
                $reubicate->company_id = 1;
                $reubicate->contract_id = 11;
                $reubicate->save();
            }
        }
    }
}