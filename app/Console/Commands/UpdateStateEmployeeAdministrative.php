<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administrative\Employees\Employee;
use GuzzleHttp\Client;
use Carbon\Carbon;

class UpdateStateEmployeeAdministrative extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-state-employee-administrative';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualizacion de estado de empleados Administrativo para la compañia Cartama usando un api proporcionado por la empresa';

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
        try 
            {
                $client = new Client();

                $date = '2025-08-15';//date('Y-m-d');
                $apiToken = '0x010000006643480A7900D94618AE19E709326F941C6501C5F360502BA379FC7E34248DFA';

                $response = $client->get("https://wikanapi.appta.com.co/cartama/empleados/movimientos?fecha=".$date, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $apiToken
                    ],
                ]);

                if ($response->getStatusCode() == 200 || $response->getStatusCode() == 201 || $response->getStatusCode() == 204) 
                {
                    $apiData = json_decode($response->getBody()->getContents(), true);

                    foreach ($apiData as $key => $row) 
                    {
                        if ($row['tipo'] == 'BAJA')
                        {
                            $employee = Employee::where('company_id', 719)->withoutGlobalScopes()->where('identification', $row['Identificacion'])->first();

                            if ($employee)
                            {
                                $fechaCarbon = Carbon::createFromFormat('d/m/Y', $row['fecha']);
                                $date = $fechaCarbon->format('Y-m-d');

                                $employee->update([
                                    'active' => 'NO',
                                    'date_inactivation' => $date
                                ]);
                            }
                        }
                    }
                    

                } else {
                    \Log::info('Error de conexión o inesperado al consultar la API POST: ' . $response->getStatusCode());            
                    return 'Error inesperado en la consulta';
                }

            } catch (RequestException $e) {
                \Log::info('Error de conexión o inesperado al consultar la API POST: ' . $e->getMessage());            
                return $this->respondHttp500();
            }
    }
}
