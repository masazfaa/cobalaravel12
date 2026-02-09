<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class geoserverseeder extends Seeder
{
    public function run(): void
    {
        // Kosongkan tabel dulu
        // DB::table('geoserver_layers')->truncate();

        $layers = [
            // 1. DATA VEKTOR: ADMIN (Poligon)
            // Di JS Anda: WMS aktif, WFS aktif (untuk popup & search)
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
            ],

            // 4. DATA RASTER: FOTO UDARA (WMTS)
            // Di JS Anda: WMTS aktif, default mati (false) biar ringan
            [
                'title'       => 'Foto Udara (WMTS)',
                'layer_name'  => 'krwn', // Pastikan nama layer raster benar
                'workspace'   => 'latihan_leaflet',
                'type'        => 'raster',
                'base_url'    => 'http://localhost:8080/geoserver/',
                'enable_wms'  => false, // Raster biasanya berat di WMS, pakai WMTS
                'enable_wfs'  => false, // Raster tidak bisa WFS
                'enable_wmts' => true,
                'is_active'   => false, // Default mati
                'z_index'     => 0, // Paling bawah
            ],
        ];

        DB::table('geoserverdb')->insert($layers);
    }
}
