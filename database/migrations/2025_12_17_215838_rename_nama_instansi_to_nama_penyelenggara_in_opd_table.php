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
        if (Schema::hasColumn('opd', 'nama_instansi')) {
            Schema::table('opd', function (Blueprint $table) {
                $table->renameColumn('nama_instansi', 'nama_penyelenggara');
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('opd', 'nama_penyelenggara')) {
            Schema::table('opd', function (Blueprint $table) {
                $table->renameColumn('nama_penyelenggara', 'nama_instansi');
            });
        }
    }
};
