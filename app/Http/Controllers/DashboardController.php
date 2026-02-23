<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminKwMysql;
use App\Models\JalanKwMysql;
use App\Models\MasjidKwMysql;
use App\Models\GeoserverDb;
use App\Models\CesiumSelfHosted;
use App\Models\CesiumIon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $admins = AdminKwMysql::orderBy('id', 'desc')->paginate(10, ['*'], 'admin_page');
        $jalans = JalanKwMysql::orderBy('id', 'desc')->paginate(10, ['*'], 'jalan_page');

        // AMBIL DATA MASJID + EKSTRAK KOORDINAT (BIAR BISA DI-EDIT)
        $masjids = MasjidKwMysql::select('*', DB::raw('ST_Longitude(geom) as lng, ST_Latitude(geom) as lat'))
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'masjid_page');

        $geoservers = GeoserverDb::all();
        $selfHosteds = CesiumSelfHosted::all();
        $ions = CesiumIon::all();

        return view('admin.dashboard', compact(
            'admins', 'jalans', 'masjids',
            'geoservers', 'selfHosteds', 'ions'
        ));
    }

    // =========================================================
    // CRUD MASJID (GEOMETRY: POINT)
    // =========================================================

    // 1. Tambah Manual
    public function storeMasjidKw(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'icon_url' => 'nullable|string'
        ]);

        try {
            $geom = DB::raw("ST_GeomFromText('POINT({$request->latitude} {$request->longitude})', 4326)");

            // PROSES UPLOAD LANGSUNG KE FOLDER PUBLIC/MASJID
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                // Bikin nama file unik pakai time() biar nggak ketimpa kalau namanya sama
                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                // Pindah langsung ke public/masjid
                $file->move(public_path('masjid'), $filename);
                // Simpan path relatifnya ke database
                $fotoPath = 'masjid/' . $filename;
            }

            MasjidKwMysql::create([
                'nama' => $request->nama,
                'luas_m2' => $request->luas_m2 ?? 0,
                'jumlah_jamaah' => $request->jumlah_jamaah ?? 0,
                'takmir_cp' => $request->takmir_cp,
                'no_telepon' => $request->no_telepon,
                'foto' => $fotoPath,
                'icon_url' => $request->icon_url ?? './0.png',
                'geom' => $geom
            ]);

            return back()->with('success', 'Data Masjid dan Foto berhasil ditambahkan!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menyimpan data: ' . $e->getMessage());
        }
    }

    // 2. Edit / Update
    public function updateMasjidKw(Request $request, $id)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'longitude' => 'required|numeric',
            'latitude' => 'required|numeric',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'icon_url' => 'nullable|string'
        ]);

        try {
            $masjid = MasjidKwMysql::findOrFail($id);
            $geom = DB::raw("ST_GeomFromText('POINT({$request->latitude} {$request->longitude})', 4326)");

            // PROSES UPDATE FOTO DI PUBLIC
            $fotoPath = $masjid->foto;
            if ($request->hasFile('foto')) {
                // Cek dan hapus foto fisik yang lama kalau ada di public/masjid
                if ($fotoPath && file_exists(public_path($fotoPath))) {
                    unlink(public_path($fotoPath));
                }

                $file = $request->file('foto');
                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                // Upload foto baru ke public/masjid
                $file->move(public_path('masjid'), $filename);
                $fotoPath = 'masjid/' . $filename;
            }

            $masjid->update([
                'nama' => $request->nama,
                'luas_m2' => $request->luas_m2 ?? 0,
                'jumlah_jamaah' => $request->jumlah_jamaah ?? 0,
                'takmir_cp' => $request->takmir_cp,
                'no_telepon' => $request->no_telepon,
                'foto' => $fotoPath,
                'icon_url' => $request->icon_url ?? $masjid->icon_url,
                'geom' => $geom
            ]);

            return back()->with('success', 'Data Masjid berhasil diperbarui!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal update data: ' . $e->getMessage());
        }
    }

    // 3. Import Batch GeoJSON
    public function importMasjidKw(Request $request)
    {
        $request->validate([
            'file_geojson' => 'required|file'
        ]);

        try {
            $file = file_get_contents($request->file('file_geojson')->getRealPath());
            $geojson = json_decode($file, true);

            if (!isset($geojson['features'])) {
                return back()->with('error', 'Format file GeoJSON tidak valid (tidak ada features).');
            }

            $count = 0;
            foreach ($geojson['features'] as $feature) {
                if (isset($feature['geometry']) && $feature['geometry']['type'] === 'Point') {
                    // GeoJSON koordinatnya [Longitude, Latitude]
                    $lng = $feature['geometry']['coordinates'][0];
                    $lat = $feature['geometry']['coordinates'][1];
                    $props = $feature['properties'];

                    // FIX MYSQL 8: POINT(Latitude Longitude)
                    $geom = DB::raw("ST_GeomFromText('POINT({$lat} {$lng})', 4326)");

                    // Map property sesuai format file Bapak
                    MasjidKwMysql::create([
                        'feature_id' => $props['id'] ?? null,
                        'nama' => $props['Nama'] ?? 'Masjid Tanpa Nama',
                        'luas_m2' => (float)($props['LUAS  M2'] ?? 0),
                        'jumlah_jamaah' => (int)($props['JUM JAMAAH'] ?? 0),
                        'takmir_cp' => $props['TAKMIR CP'] ?? null,
                        'no_telepon' => $props['NO  HENPON'] ?? null,
                        'foto' => $props['FOTO'] ?? null,
                        'icon_url' => $props['icon_url'] ?? null,
                        'geom' => $geom
                    ]);
                    $count++;
                }
            }

            return back()->with('success', "$count Data Masjid dari GeoJSON berhasil diimport!");
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal import GeoJSON: ' . $e->getMessage());
        }
    }

    // 4. Export GeoJSON
    public function exportMasjidKw()
    {
        $masjids = MasjidKwMysql::select('*', DB::raw('ST_Longitude(geom) as lng, ST_Latitude(geom) as lat'))->get();
        $features = [];

        foreach ($masjids as $m) {
            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $m->feature_id,
                    'Nama' => $m->nama,
                    'LUAS  M2' => $m->luas_m2,
                    'JUM JAMAAH' => $m->jumlah_jamaah,
                    'TAKMIR CP' => $m->takmir_cp,
                    'NO  HENPON' => $m->no_telepon,
                    'FOTO' => $m->foto,
                    'icon_url' => $m->icon_url,
                ],
                'geometry' => [
                    'type' => 'Point',
                    'coordinates' => [$m->lng, $m->lat] // Format GeoJSON wajib [Lng, Lat]
                ]
            ];
        }

        $geojson = [
            'type' => 'FeatureCollection',
            'name' => 'masjid_export',
            'crs' => ['type' => 'name', 'properties' => ['name' => 'urn:ogc:def:crs:OGC:1.3:CRS84']],
            'features' => $features
        ];

        return response()->json($geojson)->header('Content-Disposition', 'attachment; filename="data_masjid.geojson"');
    }

    // 5. Hapus Data
    public function destroyMasjidKw($id)
    {
        try {
            $masjid = MasjidKwMysql::findOrFail($id);

            // Hapus juga file fotonya dari folder public biar nggak nyampah
            if ($masjid->foto && file_exists(public_path($masjid->foto))) {
                unlink(public_path($masjid->foto));
            }

            $masjid->delete();
            return back()->with('success', 'Data Masjid beserta fotonya berhasil dihapus!');
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus data: ' . $e->getMessage());
        }
    }
}
