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
        Schema::table('event', function (Blueprint $table) {
            $table->string('passkey', 255)->nullable()->after('qr_token');
            $table->string('shortlink', 8)->unique()->nullable()->after('passkey');
            
            $table->index('shortlink');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event', function (Blueprint $table) {
            $table->dropIndex(['shortlink']);
            $table->dropColumn(['passkey', 'shortlink']);
        });
    }
};
