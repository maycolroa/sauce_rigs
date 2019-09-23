<?php

namespace App\Observers\LegalAspects\Contracts;

use App\Models\LegalAspects\Contracts\ItemQualificationContractDetail;
use App\Jobs\LegalAspects\Contracts\ListCheck\SyncQualificationResumenJob;
use Illuminate\Support\Facades\Auth;

class ItemQualificationContractDetailObserver
{
    /**
     * Handle the article "saving" event.
     *
     * @param  \App\Models\LegalAspects\LegalMatrix\ItemQualificationContractDetail $qualification
     * @return void
     */
    public function created(ItemQualificationContractDetail $qualification)
    {
        $this->syncInformation($qualification);
    }

    /**
     * Handle the article "saving" event.
     *
     * @param  \App\Models\LegalAspects\LegalMatrix\ItemQualificationContractDetail $qualification
     * @return void
     */
    public function updated(ItemQualificationContractDetail $qualification)
    {
        $this->syncInformation($qualification);
    }

    private function syncInformation(ItemQualificationContractDetail $qualification)
    {
        $qualification->contract->listCheckHistory()->create([
            'user_id' => Auth::user()->id
        ]);

        SyncQualificationResumenJob::dispatch($qualification->contract);
    }
}
