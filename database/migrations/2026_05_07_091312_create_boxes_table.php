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
        Schema::create('boxes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('boxes')->cascadeOnDelete();
            $table->string('code')->unique();
            $table->uuid('qr_uuid')->unique();
            $table->string('title');
            $table->text('description')->nullable();
            $table->unsignedInteger('level')->default(1);
            $table->enum('status', [
                'packed',
                'moving',
                'unpacked',
            ])->default('packed');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('boxes');
    }
};
