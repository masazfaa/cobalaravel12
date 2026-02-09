<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class adminkwmysql extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tentukan lokasi file
        // Pastikan file adminKw.geojson sudah ada di folder public/geojson/
        $path = public_path('geojson/adminKw.geojson');

        // Cek keberadaan file
        if (!File::exists($path)) {
            $this->command->error("File tidak ditemukan di: " . $path);
            $this->command->warn("Pastikan Anda sudah menyimpan file adminKw.geojson di folder public/geojson/");
            return;
        }

        // 2. Baca isi file JSON
        $jsonString = File::get($path);
        $data = json_decode($jsonString, true);

        // (Opsional) Kosongkan tabel sebelum import agar tidak duplikat
        // DB::table('adminkwmysql')->truncate();

        // 3. Loop setiap fitur (Padukuhan)
        foreach ($data['features'] as $feature) {

            $props = $feature['properties'];

            // Ambil geometri dan encode balik jadi string JSON untuk MySQL
            $geometryJson = json_encode($feature['geometry']);

            // Insert ke database
            DB::table('adminkwmysql')->insert([
                // Mapping Kolom: 'kolom_db' => $props['Key_di_GeoJSON']

                'feature_id'       => $props['id'] ?? null,
                'kalurahan'        => $props['Kalurahan'] ?? 'Unknown',
                'padukuhan'        => $props['Padukuhan'] ?? 'Unknown',

                // Data Angka (Gunakan ?? 0 jika null biar tidak error)
                'luas'             => $props['LUAS'] ?? 0,
                'jumlah_kk'        => $props['JUMLAH_KK'] ?? 0,
                'jumlah_penduduk'  => $props['JUM_PDDK'] ?? 0,
                'jumlah_laki'      => $props['JUM_LAKI2'] ?? 0,
                'jumlah_perempuan' => $props['JUM_PEREMP'] ?? 0,

                // PENTING: Fungsi Spatial MySQL
                'geom'             => DB::raw("ST_GeomFromGeoJSON('$geometryJson')"),

                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }

        $this->command->info('Data Administrasi (Polygon) berhasil diimport ke tabel adminkwmysql!');
    }
}
