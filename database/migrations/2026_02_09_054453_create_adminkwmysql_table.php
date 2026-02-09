<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('adminkwmysql', function (Blueprint $table) {
            $table->id();
            $table->string('feature_id', 50)->nullable()->comment('ID bawaan dari GeoJSON');
            $table->string('kalurahan');
            $table->string('padukuhan');
            $table->double('luas')->default(0)->comment('Satuan Hektar');
            $table->integer('jumlah_kk')->default(0);
            $table->integer('jumlah_penduduk')->default(0)->comment('JUM_PDDK');
            $table->integer('jumlah_laki')->default(0)->comment('JUM_LAKI2');
            $table->integer('jumlah_perempuan')->default(0)->comment('JUM_PEREMP');

            // Kolom Spatial (Geometry: bisa Polygon/MultiPolygon)
            // Parameter: nama_kolom, tipe_geometri, srid
            $table->geometry('geom', 'geometry', 4326);

            $table->timestamps();

            // Index Spatial (Wajib untuk performa query peta)
            $table->spatialIndex('geom');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('adminkwmysql');
    }
};
