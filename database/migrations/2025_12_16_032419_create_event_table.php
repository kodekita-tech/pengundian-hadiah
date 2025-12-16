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
        Schema::create('event', function (Blueprint $table) {
            $table->id();
            $table->string('nm_event')->nullable();
            $table->foreignId('opd_id')->constrained('opd')->onDelete('restrict');
            $table->enum('status', ['draft', 'pendaftaran_dibuka', 'pendaftaran_ditutup', 'pengundian', 'selesai'])->default('draft');
            $table->dateTime('tgl_mulai')->nullable();
            $table->dateTime('tgl_selesai')->nullable();
            $table->text('deskripsi')->nullable();
            $table->string('qr_token')->unique()->nullable();
            $table->timestamps();

            $table->index('opd_id');
            $table->index('status');
            $table->index('qr_token');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('event');
    }
};
