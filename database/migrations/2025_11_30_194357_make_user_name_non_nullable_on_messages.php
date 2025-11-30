<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // set any null user_name to 'Anonymous' first
        \DB::table('messages')->whereNull('user_name')->update(['user_name' => 'Anonymous']);

        Schema::table('messages', function (Blueprint $table) {
            $table->string('user_name')->default('Anonymous')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            $table->string('user_name')->nullable()->default(null)->change();
        });
    }
};
