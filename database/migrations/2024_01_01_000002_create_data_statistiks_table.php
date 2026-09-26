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
        Schema::create('data_statistiks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kecamatan_id')->constrained('kecamatans')->onDelete('cascade');
            $table->unsignedInteger('jumlah_penduduk');     // jiwa
            $table->decimal('laju_pertumbuhan', 5, 2);      // % per tahun
            $table->unsignedSmallInteger('jumlah_desa');    // desa/kelurahan
            $table->year('tahun')->default(2024);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('data_statistiks');
    }
};
