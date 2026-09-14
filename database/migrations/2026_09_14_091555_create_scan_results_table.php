<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scan_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('scan_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->enum('severity', [
                'info',
                'low',
                'medium',
                'high',
                'critical'
            ])->default('info');
            $table->json('raw_data')->nullable();
            $table->text('description')->nullable();
            $table->text('recommendation')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scan_results');
    }
};