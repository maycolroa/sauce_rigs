<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Administrative\ActionPlans\ActionPlansActivity;
use App\Models\LegalAspects\LegalMatrix\ArticleFulfillment;

class MigrateActionsPlansFamilia extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate-actions-plans-familia';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrar planes de accion de matriz legal de la empresa familia a su empresa filial Rionegro';

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
        $activities = ActionPlansActivity::select(
            'sau_action_plans_activities.*',
            'sau_action_plans_activity_module.*'
        )
        ->join('sau_action_plans_activity_module', 'sau_action_plans_activity_module.activity_id', 'sau_action_plans_activities.id')
        ->where('item_table_name', 'sau_lm_articles_fulfillment')
        ->where('sau_action_plans_activity_module.module_id', 17);

        $activities->company_scope = 623;
        $activities = $activities->get();

        
        foreach ($activities as $key => $activity) 
        {
            $clone = new ActionPlansActivity;
            $clone->description = $activity->description;
            $clone->responsible_id = 2333;
            $clone->user_id = 2333;
            $clone->execution_date = $activity->execution_date;
            $clone->expiration_date = $activity->expiration_date;
            $clone->state = $activity->state;
            $clone->editable = $activity->editable;
            $clone->company_id = 691;
            $clone->observation = $activity->observation;
            $clone->detail_procedence = $activity->detail_procedence;
            $clone->evidence = $activity->evidence;
            $clone->save();


            $qualification = ArticleFulfillment::withoutGlobalScopes()->find($activity->item_id);
            $qualification_clone = ArticleFulfillment::withoutGlobalScopes()->where('article_id', $qualification->article_id)->where('company_id', 691)->first();

            $clone->activityModule()->create([
                'module_id' => 17,
                'item_id' => $qualification_clone->id,
                'item_table_name' => 'sau_lm_articles_fulfillment'
            ]);
        }
    }
}
