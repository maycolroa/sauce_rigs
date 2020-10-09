<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\IndustrialSecure\DangerousConditions\ImageApi;
use App\Models\IndustrialSecure\DangerousConditions\Inspections\InspectionItemsQualificationAreaLocation;
use App\Models\IndustrialSecure\DangerousConditions\Reports\Report;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class DeleteOldImagesApi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'delete-old-images-api';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Borra las imagenes subidas por la api que no han sido emparejadas en 1 mes';

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
        $date = Carbon::now()->subMonth()->format('Y-m-d 00:00:00');

        $oldImagesReport = ImageApi::where('created_at', '<', $date)->get();

        $report = new Report;
        $inspection = new InspectionItemsQualificationAreaLocation;

        foreach ($oldImagesReport as $key => $image) 
        {
            try
            {
                if ($image->type == 1)
                {
                    Storage::disk($report::DISK)->delete("{$report->path_base()}{$image->file}");
                    $image->delete();
                }
                else
                {
                    Storage::disk($inspection::DISK)->delete("{$inspection->path_base()}{$image->file}");
                    $image->delete();
                }

            } catch (\Exception $e) {
                
            }
        }
    }
}
