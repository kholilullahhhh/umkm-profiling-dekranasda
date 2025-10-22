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
        Schema::create('umkms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            // $table->foreign('jenis_usaha_id')->references('id')->on('jenis_usahas')->onDelete('cascade');
            $table->foreignId('jenis_usaha_id')->constrained('jenis_usahas')->onDelete('cascade');
            $table->string('nama_usaha');
            $table->string('pemilik');
            $table->text('alamat');
            $table->string('kabupaten');
            $table->year('tahun_berdiri')->nullable();
            $table->enum('skala_usaha', ['mikro', 'kecil', 'menengah']);
            $table->decimal('omset_per_tahun', 15, 2)->nullable();
            $table->string('kontak')->nullable();
            $table->boolean('status_binaan')->default(true);
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('umkms');
    }
};
