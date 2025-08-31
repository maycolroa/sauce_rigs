<?php

namespace App\Jobs\PreventiveOccupationalMedicine\Reinstatements;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PreventiveOccupationalMedicine\Reinstatements\ImportCie11;
use App\Facades\Mail\Facades\NotificationMail;
use Illuminate\Support\Facades\Storage;

class CheckImportCie11Job implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $nameFile;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($file)
    {
      $this->nameFile = 'cie_11_'.date("YmdHis").'.xlsx';
      Storage::disk('public')->putFileAs('import/1', $file, $this->nameFile);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      try
      {
         Excel::import(new ImportCie11(), "/import/1/$this->nameFile", 'public');
        Storage::disk('public')->delete('import/1/'. $this->nameFile);

      } catch (\Exception $e)
      {
        \Log::info($e);
      }

    }
}
