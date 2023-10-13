<?php

namespace App\Jobs\IndustrialSecure\AccidentsWork;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategory;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategoryItems;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategoryCompany;
use App\Models\IndustrialSecure\WorkAccidents\SectionCategoryItemsCompany;
use DB;

class SyncCategoryItemsDefaultJob implements ShouldQueue
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
        $categoriesDefault = SectionCategory::get();

        foreach ($categoriesDefault as $keyC => $value) 
        {
            $category = SectionCategoryCompany::firstOrCreate(
                [
                    'category_name' => $value->category_name,
                    'company_id' => $this->company_id,
                    'section_id' => $value->section_id,
                ], 
                [
                    'category_name' => $value->category_name, 
                    'company_id' => $this->company_id,
                    'section_id' => $value->section_id,
                    'category_default_id' => $value->id
                ]
            );

            $itemsDefault = SectionCategoryItems::where('category_id', $value->id)->get();

            foreach ($itemsDefault as $keyI => $item) 
            {
                $itemCompany = SectionCategoryItemsCompany::firstOrCreate(
                    [
                        'item_name' => $item->item_name,
                        'company_id' => $this->company_id,                            
                        'category_id' => $category->id,
                    ], 
                    [
                        'item_name' => $item->item_name, 
                        'company_id' => $this->company_id,
                        'category_id' => $category->id,
                    ]
                );
            }
        }
    }
}
