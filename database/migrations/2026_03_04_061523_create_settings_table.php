<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('name')->index();
            $table->jsonb('metadata')->nullable();
            $table->jsonb('value')->nullable();
            $table->text('description')->nullable();

            $table->timestamps();

            $table->unique(['name', 'metadata']);
        });

        DB::statement('CREATE INDEX settings_metadata_gin ON settings USING GIN (metadata)');
        DB::statement('CREATE INDEX settings_value_gin ON settings USING GIN (value)');
    }

    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
