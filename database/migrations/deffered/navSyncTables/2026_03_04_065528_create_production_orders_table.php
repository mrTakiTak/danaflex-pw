<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('production_orders', function (Blueprint $table) {
            $table->id();
            $table->char('timestamp',16)->nullable()->index();
            $table->string('place_code')->index();
            $table->unsignedInteger('status_id')->index();

            $table->string('no')->index();
            $table->string('description')->nullable()->index();
            $table->string('klient')->nullable()->index();
            $table->date('date_complete')->nullable();


            $table->timestamps(6);
            $table->softDeletes(precision: 6);
            $table->unique(['place_code', 'no']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('production_orders');
    }
};
