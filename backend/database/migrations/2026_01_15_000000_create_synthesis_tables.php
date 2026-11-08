<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dossiers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('client_niche')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dossier_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('parent_report_id')->nullable()->constrained('reports')->onDelete('cascade');
            $table->string('title');
            $table->string('type');
            $table->enum('status', ['pending', 'processing', 'completed', 'failed'])->default('pending');
            $table->unsignedTinyInteger('progress')->default(0);
            $table->json('input_payload');
            $table->longText('result_markdown')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['type', 'status']);
            $table->index('dossier_id');
        });

        Schema::create('sources', function (Blueprint $table) {
            $table->id();
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->string('type');
            $table->string('url')->nullable();
            $table->string('file_path')->nullable();
            $table->longText('raw_content')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sources');
        Schema::dropIfExists('reports');
        Schema::dropIfExists('dossiers');
    }
};
