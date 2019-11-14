<?php

namespace App\Observers\LegalAspects\Contracts;

use App\Models\LegalAspects\Contracts\Item;
use App\Models\LegalAspects\Contracts\EvaluationContractItem;
use App\Facades\ActionPlans\Facades\ActionPlan;

class ItemObserver
{
    /**
     * Handle the article "saving" event.
     *
     * @param  \App\Models\LegalAspects\Contracts\Item $item
     * @return void
     */
    public function deleting(Item $item)
    {
        \Log::info("entre al observador de item: ".$item->id);
        $itemsEvaluations = EvaluationContractItem::where('sau_ct_evaluation_contract_items.item_id', $item->id)->get();

        foreach ($itemsEvaluations as $itemEvaluation) 
        {
            \Log::info("borrando al observador de item");
            ActionPlan::company($item->subobjective->objective->evaluation->company_id)->model($itemEvaluation)->modelDeleteAll();
        }

        \Log::info("saliendo al observador de item");
    }
}
