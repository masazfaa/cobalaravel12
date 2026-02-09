<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jalankwmysql', function (Blueprint $table) {
            $table->id();
            $table->string('feature_id', 50)->nullable()->comment('ID dari GeoJSON');
            $table->string('nama')->nullable();
            $table->double('panjang')->default(0);
            $table->double('luas')->default(0);
            $table->double('aset_tanah')->default(0);
            $table->string('kondisi', 100)->nullable();
            $table->string('kewenangan', 100)->nullable();
            $table->string('foto_awal')->nullable();
            $table->string('foto_akhir')->nullable();
            $table->double('rer_njop')->default(0);
            $table->string('status', 100)->nullable();
            $table->double('lebar')->default(0);
            $table->string('asal', 100)->nullable();
            $table->string('layer', 100)->nullable();

            // Kolom Spatial (Geometry: bisa LineString/MultiLineString)
            $table->geometry('geom', 'geometry', 4326);

            $table->timestamps();

            // Index Spatial
            $table->spatialIndex('geom');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jalankwmysql');
    }
};
