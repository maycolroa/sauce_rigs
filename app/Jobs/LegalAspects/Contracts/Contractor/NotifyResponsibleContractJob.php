<?php

namespace App\Jobs\LegalAspects\Contracts\Contractor;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\Administrative\Users\User;
use App\Models\General\Company;
use App\Models\General\Module;
use App\Models\General\Team;
use App\Models\System\Licenses\License;
use App\Facades\Mail\Facades\NotificationMail;
use App\Facades\Configuration;
use DB;

class NotifyResponsibleContractJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $users;
    protected $contract;
    protected $company_id;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($users, $contract, $company_id)
    {
        $this->users = $users;
        $this->contract = $contract;
        $this->company_id = $company_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $recipients = User::select('sau_users.email')
            ->active(true, $this->company_id)
            ->whereIn('sau_users.id', $this->users)
            ->groupBy('sau_users.id', 'sau_users.email');

        $recipients->company_scope = $this->company_id;
        $recipients = $recipients->get();

        NotificationMail::
            subject('Responsables Contratista')
            ->recipients($recipients)
            ->message("Usted acaba de ser asignado como responsable del contratista <b>{$this->contract}</b>")
            ->module('users')
            ->event('Job: NotifyResponsibleContractJob')
            ->company($this->company_id)
            ->send();
    }
}
