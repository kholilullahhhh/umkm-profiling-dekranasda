<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('profilings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('umkm_id')->constrained('umkms')->onDelete('cascade');
            $table->integer('tenaga_kerja')->nullable();
            $table->string('kapasitas_produksi')->nullable();
            $table->string('bahan_baku')->nullable();
            $table->string('pasar')->nullable();
            $table->text('kebutuhan_pembinaan')->nullable();
            $table->text('potensi_pengembangan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profilings');
    }
};
