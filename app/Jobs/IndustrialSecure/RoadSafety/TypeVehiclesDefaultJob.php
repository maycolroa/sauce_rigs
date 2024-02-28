<?php

namespace App\Jobs\IndustrialSecure\RoadSafety;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\IndustrialSecure\RoadSafety\TagsTypeVehicle;
use DB;

class TypeVehiclesDefaultJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $company_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($company_id)
    {
        $this->company_id = $company_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $vehicles = [
            'Motocicleta',
            'Carro',
            'Camión',
            'Camioneta',
            'Tracto camión ',
            'Maquinaria pesada',
            'Montacargas',
        ];

        foreach ($vehicles as $key => $value) 
        {
            $type = TagsTypeVehicle::query();
            $type->company_scope = $this->company_id;
            $type = $type->firstOrCreate(['name' => $value], 
                                                ['name' => $value, 'company_id' => $this->company_id]);
        }
    }
}
