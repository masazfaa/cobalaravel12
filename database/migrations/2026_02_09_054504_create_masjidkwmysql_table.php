<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('masjidkwmysql', function (Blueprint $table) {
            $table->id();
            $table->string('feature_id', 50)->nullable()->comment('ID dari GeoJSON');
            $table->string('nama');
            $table->double('luas_m2')->nullable()->default(0)->comment('Mapping dari LUAS M2');
            $table->integer('jumlah_jamaah')->default(0)->comment('Mapping dari JUM JAMAAH');
            $table->string('takmir_cp', 100)->nullable()->comment('Contact Person Takmir');
            $table->string('no_telepon', 50)->nullable()->comment('Mapping dari NO HENPON');
            $table->string('foto')->nullable();
            $table->string('icon_url')->nullable();

            // Kolom Spatial (Geometry: Point)
            $table->geometry('geom', 'geometry', 4326);

            $table->timestamps();

            // Index Spatial
            $table->spatialIndex('geom');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('masjidkwmysql');
    }
};
