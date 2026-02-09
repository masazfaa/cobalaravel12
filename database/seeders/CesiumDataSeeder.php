<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CesiumDataSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================================
        // 1. TABEL SELF HOSTED (Batch Insert)
        // ==========================================================
        // Kosongkan tabel dulu biar bersih (Opsional)
        // DB::table('cesiumselfhosted')->truncate();

        DB::table('cesiumselfhosted')->insert([
            [
                'name'        => "Gedung SGLC UGM",
                'description' => 'Smart Green Learning Center.',
                'model_path'  => "/data3d/Smart Green Learning Center UGM.glb",
                'longitude'   => 110.372406,
                'latitude'    => -7.765341,
                'height'      => 0,
                'heading'     => 90,
                // GEOM DIHAPUS
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
            [
                'name'        => "Gedung ERIC UGM",
                'description' => 'Engineering Research and Innovation Center.',
                'model_path'  => "/data3d/ERIC_UGM.glb",
                'longitude'   => 110.374573,
                'latitude'    => -7.765974,
                'height'      => 0,
                'heading'     => 90,
                // GEOM DIHAPUS
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ]);


        // ==========================================================
        // 2. TABEL CESIUM ION (Batch Insert)
        // ==========================================================

        DB::table('cesiumion')->insert([
            [
                'ion_asset_id' => 2976635,
                'name'         => "Gedung SGLC UGM",
                'description'  => "Smart Green Learning Center.",
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
            [
                'ion_asset_id' => 2976596,
                'name'         => "Gedung ERIC UGM",
                'description'  => "Engineering Research and Innovation Center.",
                'created_at'   => now(),
                'updated_at'   => now(),
            ],
        ]);

        $this->command->info('Data Cesium (Tanpa Geom) berhasil diimport!');
    }
}
