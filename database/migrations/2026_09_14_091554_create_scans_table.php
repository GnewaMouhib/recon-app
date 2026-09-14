<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('scans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('target_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->enum('type', [
                'subdomain',
                'port',
                'vulnerability',
                'directory',
                'wordpress',
                'technology'
            ]);
            $table->enum('status', [
                'pending',
                'running',
                'completed',
                'failed'
            ])->default('pending');
            $table->timestamp('started_at')->nullable();
            $table->timestamp('finished_at')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('scans');
    }
};