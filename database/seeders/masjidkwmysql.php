<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class masjidkwmysql extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Tentukan lokasi file
        // Pastikan file masjid.geojson ada di folder public/geojson/
        $path = public_path('geojson/masjid.geojson');

        // Cek keberadaan file
        if (!File::exists($path)) {
            $this->command->error("File tidak ditemukan di: " . $path);
            $this->command->warn("Pastikan Anda sudah menyimpan file masjid.geojson di folder public/geojson/");
            return;
        }

        // 2. Baca isi file JSON
        $jsonString = File::get($path);
        $data = json_decode($jsonString, true);

        // (Opsional) Hapus data lama agar tidak duplikat saat di-seed ulang
        // DB::table('masjidkwmysql')->truncate();

        // 3. Loop setiap fitur (Masjid/Musholla)
        foreach ($data['features'] as $feature) {

            $props = $feature['properties'];

            // Ambil geometri (Point) dan encode balik jadi string JSON
            $geometryJson = json_encode($feature['geometry']);

            // Insert ke database
            DB::table('masjidkwmysql')->insert([
                // --- Mapping Kolom Database => Key di GeoJSON ---

                'feature_id'    => $props['id'] ?? null,
                'nama'          => $props['Nama'] ?? 'Tanpa Nama',

                // Perhatikan Key GeoJSON yang memiliki karakter spasi khusus/ganda
                // Kita gunakan ?? 0 atau ?? null untuk menangani nilai kosong
                'luas_m2'       => $props['LUAS  M2'] ?? 0,
                'jumlah_jamaah' => $props['JUM JAMAAH'] ?? 0,
                'takmir_cp'     => $props['TAKMIR CP'] ?? null,
                'no_telepon'    => $props['NO  HENPON'] ?? null,

                'foto'          => $props['FOTO'] ?? null,
                'icon_url'      => $props['icon_url'] ?? null,

                // PENTING: Konversi Geometry MySQL (Point)
                'geom'          => DB::raw("ST_GeomFromGeoJSON('$geometryJson')"),

                'created_at'    => now(),
                'updated_at'    => now(),
            ]);
        }

        $this->command->info('Data Masjid (Point) berhasil diimport ke tabel masjidkwmysql!');
    }
}
