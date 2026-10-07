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
        Schema::create('mutasi_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_barang_id')->constrained('unit_barangs')->onDelete('cascade');
            $table->foreignId('ruangan_asal_id')->nullable()->constrained('ruangans')->onDelete('set null');
            $table->foreignId('ruangan_tujuan_id')->constrained('ruangans')->onDelete('cascade');
            $table->date('tanggal_mutasi');
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_barangs');
    }
};
