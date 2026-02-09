<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('geoserverdb', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Contoh: Batas Wilayah
            $table->string('layer_name'); // Contoh: adminkw
            $table->string('workspace')->default('latihan_leaflet');

            // Jenis Layer
            $table->enum('type', ['vector', 'raster'])->default('vector');

            // Konfigurasi URL
            $table->string('base_url')->default('http://localhost:8080/geoserver/');

            // Fitur yang diaktifkan
            $table->boolean('enable_wms')->default(true);  // Untuk gambar overlay
            $table->boolean('enable_wfs')->default(false); // Untuk interaksi klik/search
            $table->boolean('enable_wmts')->default(false); // Untuk raster cepat

            // Tampilan Default
            $table->boolean('is_active')->default(true);
            $table->integer('z_index')->default(1);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('geoserverdb');
    }
};
