<?php

namespace App\Console\Commands\Test\s_kuznecov;

use App\Actions\NavSync\SyncModelDataAction;
use App\Enums\PlaceEnum;
use App\Models\LocalNavSync\BaseClass\LocalNavSyncBaseModel;
use App\Models\LocalNavSync\ProductionOrder;
use App\Models\LocalNavSync\Vocabs\Different\Cylinder;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\File;

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




    }
}
