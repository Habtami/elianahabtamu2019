<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Smart backup bookkeeping: the fingerprint that decides whether a
     * scheduled run has anything new to save, plus copy/email outcomes.
     */
    public function up(): void
    {
        Schema::create('backups', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->id();
            $table->string('filename', 255)->nullable();
            $table->string('file_path', 500)->nullable();
            $table->unsignedBigInteger('size_bytes')->nullable();
            $table->string('content_hash', 64)->nullable()->index();
            $table->boolean('was_skipped')->default(false);
            $table->string('skip_reason', 255)->nullable();
            $table->string('backup_type', 20)->default('manual');
            $table->unsignedBigInteger('created_by')->nullable()->index();
            $table->string('destination_path', 500)->nullable();
            $table->string('copy_status', 30)->nullable();
            $table->string('email_status', 30)->nullable();
            $table->text('email_recipients')->nullable();
            $table->timestamps();

            $table->index(['was_skipped', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('backups');
    }
};
