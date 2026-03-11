<?php

namespace App\Console\Commands\Test\s_kuznecov;

use App\Actions\NavSync\SyncModelDataAction;
use App\Enums\PlaceEnum;
use App\Models\LocalNavSync\ProductionOrder;
use Illuminate\Console\Command;

class TestSKuznecov extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:test_s_kuznecov';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {

        //SyncModelDataAction::syncFull(Cylinder::class,PlaceEnum::Zao);
        //SyncModelDataAction::syncChanges(Cylinder::class, PlaceEnum::Zao);
        //SyncModelDataAction::syncFull(ProductionOrder::class,PlaceEnum::Zao);
        SyncModelDataAction::syncChanges(ProductionOrder::class, PlaceEnum::Zao);
    }
}
