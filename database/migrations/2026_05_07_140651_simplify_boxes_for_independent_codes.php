<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('boxes')
            ->where('status', 'moving')
            ->update(['status' => 'packed']);

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE boxes MODIFY status ENUM('packed', 'unpacked') DEFAULT 'packed'");
        }

        Schema::table('boxes', function (Blueprint $table) {
            if (Schema::hasColumn('boxes', 'parent_id')) {
                $table->dropConstrainedForeignId('parent_id');
            }

            if (Schema::hasColumn('boxes', 'title')) {
                $table->dropColumn('title');
            }

            if (Schema::hasColumn('boxes', 'level')) {
                $table->dropColumn('level');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('boxes', function (Blueprint $table) {
            if (! Schema::hasColumn('boxes', 'parent_id')) {
                $table->foreignId('parent_id')->nullable()->after('id')->constrained('boxes')->cascadeOnDelete();
            }

            if (! Schema::hasColumn('boxes', 'title')) {
                $table->string('title')->default('Kardus')->after('qr_uuid');
            }

            if (! Schema::hasColumn('boxes', 'level')) {
                $table->unsignedInteger('level')->default(1)->after('description');
            }
        });

        $driver = DB::connection()->getDriverName();

        if (in_array($driver, ['mysql', 'mariadb'], true)) {
            DB::statement("ALTER TABLE boxes MODIFY status ENUM('packed', 'moving', 'unpacked') DEFAULT 'packed'");
        }
    }
};
