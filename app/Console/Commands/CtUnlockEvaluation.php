<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\LegalAspects\Contracts\Evaluation;
use Carbon\Carbon;

class CtUnlockEvaluation extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ct-unlock-evaluation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Desbloquea las evaluaciones que tengan mas de 1 minuto sin editarse';

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
        $records = Evaluation::locked()->withoutGlobalScopes()->get();

        foreach ($records as $key => $record)
        {
            if ($record->time_edit->diffInSeconds(Carbon::now()) >= 60)
                $record->update(['in_edit' => false, 'user_edit' => null]);
        }
    }
}
