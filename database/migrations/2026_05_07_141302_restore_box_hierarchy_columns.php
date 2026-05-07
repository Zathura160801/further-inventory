<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            if (! Schema::hasColumn('boxes', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('boxes')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('boxes', 'level')) {
                $table->unsignedInteger('level')->default(1)->after('description');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            if (Schema::hasColumn('boxes', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }

            if (Schema::hasColumn('boxes', 'level')) {
                $table->dropColumn('level');
            }
        });
    }
};
