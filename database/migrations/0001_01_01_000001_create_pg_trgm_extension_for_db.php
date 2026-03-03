<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE EXTENSION IF NOT EXISTS pg_trgm');
        DB::statement("SELECT show_trgm('example')"); //Проверка установленности расширения
        // Доступность pg_trgm на сервере: SELECT * FROM pg_available_extensions WHERE name = 'pg_trgm';
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

    }
};
