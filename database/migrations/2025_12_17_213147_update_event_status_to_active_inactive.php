<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ubah ENUM menjadi string sementara untuk memudahkan update data
        DB::statement("ALTER TABLE `event` MODIFY COLUMN `status` VARCHAR(20) DEFAULT 'tidak_aktif'");

        $now = Carbon::now('Asia/Jakarta');

        // Update semua event berdasarkan tanggal
        DB::table('event')->get()->each(function ($event) use ($now) {
            if (!$event->tgl_mulai || !$event->tgl_selesai) {
                // Jika tanggal tidak ada, set menjadi tidak aktif
                DB::table('event')
                    ->where('id', $event->id)
                    ->update(['status' => 'tidak_aktif']);
                return;
            }

            // Parse tanggal dengan asumsi timezone Jakarta
            $tglMulai = Carbon::createFromFormat('Y-m-d H:i:s', $event->tgl_mulai, 'Asia/Jakarta');
            $tglSelesai = Carbon::createFromFormat('Y-m-d H:i:s', $event->tgl_selesai, 'Asia/Jakarta');

            // Cek apakah dalam rentang tanggal
            $isWithinDateRange = $tglMulai->lte($now) && $tglSelesai->gte($now);

            // Update status berdasarkan rentang tanggal
            $newStatus = $isWithinDateRange ? 'aktif' : 'tidak_aktif';

            DB::table('event')
                ->where('id', $event->id)
                ->update(['status' => $newStatus]);
        });

        // Ubah kembali menjadi ENUM dengan nilai baru
        DB::statement("ALTER TABLE `event` MODIFY COLUMN `status` ENUM('aktif', 'tidak_aktif') DEFAULT 'tidak_aktif'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Ubah ENUM menjadi string sementara
        DB::statement("ALTER TABLE `event` MODIFY COLUMN `status` VARCHAR(20) DEFAULT 'draft'");

        // Convert back to old status system
        // Aktif -> pendaftaran_dibuka
        // Tidak aktif -> pendaftaran_ditutup
        DB::table('event')
            ->where('status', 'aktif')
            ->update(['status' => 'pendaftaran_dibuka']);

        DB::table('event')
            ->where('status', 'tidak_aktif')
            ->update(['status' => 'pendaftaran_ditutup']);

        // Ubah ENUM kembali ke yang lama
        DB::statement("ALTER TABLE `event` MODIFY COLUMN `status` ENUM('draft', 'pendaftaran_dibuka', 'pendaftaran_ditutup', 'pengundian', 'selesai') DEFAULT 'draft'");
    }
};
