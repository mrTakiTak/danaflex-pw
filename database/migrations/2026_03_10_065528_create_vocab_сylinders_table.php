<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vocab_cylinders', function (Blueprint $table) {
            $table->id();
            $table->char('timestamp',16)->nullable()->index();

            $table->string('place_code')->index();
            $table->string('code')->unique();
            $table->string('comment')->nullable();

            $table->unsignedInteger('circumference')->nullable()->comment('Длина окружности в мм');
            $table->unsignedInteger('status_id')->index();

            $table->timestamps(6);
            $table->softDeletes(precision: 6);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vocab_cylinders');
    }
};
