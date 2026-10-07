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
        Schema::create('unit_barangs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('barang_id')->constrained('barangs')->onDelete('cascade');
            $table->foreignId('ruangan_id')->constrained('ruangans')->onDelete('cascade');
            $table->foreignId('sumber_dana_id')->nullable()->constrained('sumber_danas')->onDelete('set null');
            $table->string('kode_unit')->unique();
            $table->string('nomor_seri')->nullable();
            $table->enum('kondisi', ['Baik', 'Rusak Ringan', 'Rusak Berat'])->default('Baik');
            $table->enum('status', ['Tersedia', 'Dipinjam'])->default('Tersedia');
            $table->timestamps();

            $table->index('ruangan_id');
            $table->index('sumber_dana_id');
            $table->index('kondisi');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('unit_barangs');
    }
};
