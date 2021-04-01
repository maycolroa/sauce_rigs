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
use App\Models\LegalAspects\Contracts\ListCheckQualification;
use App\Jobs\LegalAspects\Contracts\ListCheck\SyncQualificationResumenJob;
use DB;

class ListCheckQualificationCopyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ContractTrait;

    protected $company_id;
    protected $contract;
    protected $list_selected;
    protected $user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($user, $company_id, $contract, $list_selected)
    {
      $this->company_id = $company_id;
      $this->contract = $contract;
      $this->list_selected = $list_selected;
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

        $qualification_exist = ListCheckQualification::where('company_id', $this->company_id)
        ->where('contract_id', $this->contract->id)
        ->where('state', true)
        ->first();

        //Obtiene los items calificados
        $items_calificated = ItemQualificationContractDetail::
                  where('contract_id', $this->contract->id)
                ->where('list_qualification_id', $qualification_exist->id)
                ->pluck("qualification_id", "item_id");

        if (COUNT($items) > 0)
        {
          $items->each(function($item, $index) use ($qualifications, $items_calificated, $qualification_exist) {
            $model_activity = ItemQualificationContractDetail::
                            where('contract_id', $this->contract->id)
                          ->where('list_qualification_id', $qualification_exist->id)
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
              ->where('sau_ct_file_item_contract.list_qualification_id', $qualification_exist->id)
              ->get();

              if ($files)
              {
                $files->each(function($file, $index) {
                  $old_file = $file->file;
                  $file->delete();
                  $path = 'legalAspects/files/'. $old_file;

                  if (!File::exists($path))
                    Storage::disk('public')->delete($path);
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

        $selected_list = ListCheckQualification::find($this->list_selected);

        \Log::info($selected_list);

        $getQualifications = ItemQualificationContractDetail::
              where('contract_id', $this->contract->id)
            ->where('list_qualification_id', $selected_list->id)
            ->get();

        if (COUNT($getQualifications) > 0)
        {
          $getQualifications->each(function($item, $index) use ($qualifications, $selected_list, $qualification_exist) {

            $newQualification = $item->replicate()->fill([
                'contract_id' => $this->contract->id,
                'list_qualification_id' => $qualification_exist->id
            ]);

            $newQualification->save();

            $item->qualification = $item->qualification_id ? $qualifications[$item->qualification_id] : '';         
          });
        }

        $this->reloadLiskCheckResumen($this->contract, $qualification_exist->id);

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
