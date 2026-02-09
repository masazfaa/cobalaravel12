<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class geoserverseeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel dulu
        DB::table('geoserverdb')->truncate();

        $layers = [
            // 1. DATA VEKTOR: ADMIN (Poligon)
            [
                'title'       => 'Batas Wilayah',
                'layer_name'  => 'adminkw',
                'workspace'   => 'latihan_leaflet',
                'type'        => 'vector',
                'base_url'    => 'http://localhost:8080/geoserver/',
                'enable_wms'  => true,
                'enable_wfs'  => true,
                'enable_wmts' => false,
                'is_active'   => true,
                'z_index'     => 1,
                'created_at'  => now(), // <--- Tambahkan ini
                'updated_at'  => now(), // <--- Tambahkan ini
            ],

            // 2. DATA VEKTOR: JALAN (Garis)
            [
                'title'       => 'Jaringan Jalan',
                'layer_name'  => 'jalankw',
                'workspace'   => 'latihan_leaflet',
                'type'        => 'vector',
                'base_url'    => 'http://localhost:8080/geoserver/',
                'enable_wms'  => true,
                'enable_wfs'  => true,
                'enable_wmts' => false,
                'is_active'   => true,
                'z_index'     => 2,
                'created_at'  => now(), // <--- Tambahkan ini
                'updated_at'  => now(), // <--- Tambahkan ini
            ],

            // 3. DATA VEKTOR: MASJID (Titik)
            [
                'title'       => 'Sebaran Masjid',
                'layer_name'  => 'masjid',
                'workspace'   => 'latihan_leaflet',
                'type'        => 'vector',
                'base_url'    => 'http://localhost:8080/geoserver/',
                'enable_wms'  => true,
                'enable_wfs'  => true,
                'enable_wmts' => false,
                'is_active'   => true,
                'z_index'     => 3,
                'created_at'  => now(), // <--- Tambahkan ini
                'updated_at'  => now(), // <--- Tambahkan ini
            ],

            // 4. DATA RASTER: FOTO UDARA (WMTS)
            [
                'title'       => 'Foto Udara (WMTS)',
                'layer_name'  => 'krwn',
                'workspace'   => 'latihan_leaflet',
                'type'        => 'raster',
                'base_url'    => 'http://localhost:8080/geoserver/',
                'enable_wms'  => false,
                'enable_wfs'  => false,
                'enable_wmts' => true,
                'is_active'   => false,
                'z_index'     => 0,
                'created_at'  => now(),
                'updated_at'  => now(),
            ],
        ];

        DB::table('geoserverdb')->insert($layers);
    }
}
