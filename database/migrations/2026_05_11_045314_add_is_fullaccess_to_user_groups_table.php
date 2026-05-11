<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_groups', function (Blueprint $table) {
            if (!Schema::hasColumn('user_groups', 'is_fullaccess')) {
                $table->boolean('is_fullaccess')
                    ->default(false)
                    ->after('description');
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_groups', function (Blueprint $table) {
            if (Schema::hasColumn('user_groups', 'is_fullaccess')) {
                $table->dropColumn('is_fullaccess');
            }
        });
    }
};