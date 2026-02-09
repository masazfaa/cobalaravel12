<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class jalankwmysql extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tentukan lokasi file
        // Pastikan file jalankw.geojson ada di folder public/geojson/
        $path = public_path('geojson/jalankw.geojson');

        // Cek keberadaan file
        if (!File::exists($path)) {
            $this->command->error("File tidak ditemukan di: " . $path);
            $this->command->warn("Pastikan Anda sudah menyimpan file jalankw.geojson di folder public/geojson/");
            return;
        }

        // 2. Baca isi file JSON
        $jsonString = File::get($path);
        $data = json_decode($jsonString, true);

        // (Opsional) Hapus data lama agar tidak duplikat saat di-seed ulang
        // DB::table('jalankwmysql')->truncate();

        // 3. Loop setiap fitur (Jalan)
        foreach ($data['features'] as $feature) {

            $props = $feature['properties'];

            // Ambil geometri (MultiLineString) dan encode balik jadi string JSON
            $geometryJson = json_encode($feature['geometry']);

            // Insert ke database
            DB::table('jalankwmysql')->insert([
                // --- Mapping Kolom Database => Key di GeoJSON ---

                'feature_id'   => $props['id'] ?? null,
                'nama'         => $props['Nama'] ?? 'Tanpa Nama',

                // Data Numerik (Pastikan default 0 jika null)
                'panjang'      => $props['Panjang'] ?? 0,
                'luas'         => $props['Luas'] ?? 0,
                'aset_tanah'   => $props['Asettanah'] ?? 0,
                'rer_njop'     => $props['RERNJOP'] ?? 0,
                'lebar'        => $props['Lebar'] ?? 0,

                // Data String
                'kondisi'      => $props['Kondisi'] ?? null,
                'kewenangan'   => $props['Kewenangan'] ?? null,
                'status'       => $props['Status'] ?? null,
                'asal'         => $props['Asal'] ?? null,
                'layer'        => $props['layer'] ?? null,

                // Foto (Path string)
                'foto_awal'    => $props['FotoAwal'] ?? null,
                'foto_akhir'   => $props['FotoAkhir'] ?? null,

                // PENTING: Konversi Geometry MySQL
                'geom'         => DB::raw("ST_GeomFromGeoJSON('$geometryJson')"),

                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }

        $this->command->info('Data Jalan (LineString) berhasil diimport ke tabel jalankwmysql!');
    }
}
