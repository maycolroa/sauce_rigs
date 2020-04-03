<?php

namespace App\Jobs\LegalAspects\Contracts\ListCheck;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LegalAspects\Contracts\ListCheck\ListCheckContractExcel;
use App\Facades\Mail\Facades\NotificationMail;
use App\Traits\ContractTrait;
use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Models\Administrative\Users\User;
use App\Models\LegalAspects\Contracts\FileUpload;
use App\Models\LegalAspects\Contracts\Qualifications;
use Illuminate\Support\Facades\Storage;
use App\Facades\ActionPlans\Facades\ActionPlan;
use Illuminate\Support\Facades\File;
use App\Models\Administrative\ActionPlans\ActionPlansActivity;
use App\Models\Administrative\ActionPlans\ActionPlansActivityModule;
use App\Models\LegalAspects\Contracts\ContractLesseeInformation;
use App\Jobs\LegalAspects\Contracts\ListCheck\SyncQualificationResumenJob;
use DB;

class ListCheckCopyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ContractTrait;

    protected $company_id;
    protected $contract;
    protected $contract_select;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $contract, $contract_select)
    {
      $this->company_id = $company_id;
      $this->contract = $contract;
      $this->contract_select = $contract_select;
      $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
      DB::beginTransaction();

      try
      {
        /**ELIMINANDO INFORMACION ACTUAL */
        $items = $this->getStandardItemsContract($this->contract);

        $qualifications = Qualifications::pluck("name", "id");

        //Obtiene los items calificados
        $items_calificated = ItemQualificationContractDetail::
                  where('contract_id', $this->contract->id)
                ->pluck("qualification_id", "item_id");

        if (COUNT($items) > 0)
        {
          $items->each(function($item, $index) use ($qualifications, $items_calificated) {

            $model_activity = ItemQualificationContractDetail::
                            where('contract_id', $this->contract->id)
                          ->where('item_id', $item->id)
                          ->first();

            $item->qualification = isset($items_calificated[$item->id]) ? $qualifications[$items_calificated[$item->id]] : '';

            if ($item->qualification == 'C')
            {
              $files = FileUpload::select(
                          'sau_ct_file_upload_contracts_leesse.id AS id',
                          'sau_ct_file_upload_contracts_leesse.name AS name',
                          'sau_ct_file_upload_contracts_leesse.file AS file',
                          'sau_ct_file_upload_contracts_leesse.expirationDate AS expirationDate'
                      )
                      ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                      ->join('sau_ct_file_item_contract', 'sau_ct_file_item_contract.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                      ->where('sau_ct_file_upload_contract.contract_id', $this->contract->id)
                      ->where('sau_ct_file_item_contract.item_id', $item->id)
                      ->get();

              if ($files)
              {
                $files->each(function($file, $index) {
                  $old_file = $file->file;
                  $file->delete();
                  $path = 'legalAspects/files/'. $old_file;

                  if (!File::exists($path))
                    Storage::disk('s3')->delete($path);
                });

                $item->files = $files;
              }
            }
            else if ($item->qualification == 'NC')
            {
              $item->actionPlan = ActionPlan::company($this->company_id)->model($model_activity)->modelDeleteAll();
            }

            if ($model_activity)
              $model_activity->delete();
          });
        }

        /**Fin Eliminacion de registros*/

        /**Insercion de registros encontrados*/

        $selected_contract = ContractLesseeInformation::withoutGlobalScopes()->find($this->contract_select);

        $getQualifications = ItemQualificationContractDetail::where('contract_id', $this->contract_select)->get();

        if (COUNT($getQualifications) > 0)
        {
          $getQualifications->each(function($item, $index) use ($qualifications, $selected_contract) {

            $newQualification = $item->replicate()->fill([
                'contract_id' => $this->contract->id
            ]);

            $newQualification->save();

            $item->qualification = $item->qualification_id ? $qualifications[$item->qualification_id] : '';

            if ($item->qualification == 'C')
            {
              $files = FileUpload::select(
                          'sau_ct_file_upload_contracts_leesse.id AS id',
                          'sau_ct_file_upload_contracts_leesse.name AS name',
                          'sau_ct_file_upload_contracts_leesse.file AS file',
                          'sau_ct_file_upload_contracts_leesse.expirationDate AS expirationDate'
                      )
                      ->join('sau_ct_file_upload_contract','sau_ct_file_upload_contract.file_upload_id','sau_ct_file_upload_contracts_leesse.id')
                      ->join('sau_ct_file_item_contract', 'sau_ct_file_item_contract.file_id', 'sau_ct_file_upload_contracts_leesse.id')
                      ->where('sau_ct_file_upload_contract.contract_id', $this->contract_select)
                      ->where('sau_ct_file_item_contract.item_id', $item->item_id)
                      ->get();

              if ($files->count() > 0)
              {
                $files->each(function($file, $index) use ($item) {

                  $extension = explode('.', $file->file);

                  $nameFile = base64_encode($this->user->id . now() . rand(1,10000)) .'.'. $extension[1];

                  $newQFile = $file->replicate()->fill([
                    'user_id' => $this->user->id,
                    'file' => $nameFile
                  ]);

                  $newQFile->save();

                  Storage::disk('s3')->copy('legalAspects/files/'. $file->file, 'legalAspects/files/'. $nameFile);

                  $newQFile->contracts()->sync([$this->contract->id]);
                  $newQFile->items()->sync([$item->item_id]);
                });
              }
            }            
          });
        }

        $this->reloadLiskCheckResumen($this->contract);

        DB::commit();

        NotificationMail::
            subject('Contratistas - Transferencia de estandares mínimos')
            ->recipients($this->user)
            ->message('Se ha realizado la transferencia de estandares mínimos con éxito')
            ->module('contracts')
            ->event('Job: ListCheckCopyJob')
            ->company($this->company_id)
            ->send();

      } catch (\Exception $e) {
        \Log::info($e->getMessage());
        DB::rollback();

        NotificationMail::
          subject('Contratistas - Transferencia de estandares mínimos')
          ->recipients($this->user)
          ->message('Se produjo un error durante el proceso de Transferencia de valores de la lista de chequeo. Contacte con el administrador')
          ->module('contracts')
          ->event('Job: ListCheckCopyJob')
          ->company($this->company_id)
          ->send();
      }        
    }
}
