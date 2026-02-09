<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Tabel Self Hosted
        Schema::create('cesiumselfhosted', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('model_path');
            $table->double('longitude');
            $table->double('latitude');
            $table->double('height')->default(0);
            $table->double('heading')->default(0);

            $table->timestamps();
        });

        // 2. Tabel Cesium Ion
        Schema::create('cesiumion', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ion_asset_id');
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cesiumselfhosted');
        Schema::dropIfExists('cesiumion');
    }
};
