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
    public function index(Request $request)
    {
        // TANGKAP INPUTAN PENCARIAN DARI VIEW
        $searchAdmin = $request->input('search_admin');
        $searchJalan = $request->input('search_jalan');
        $searchMasjid = $request->input('search_masjid');
        $searchGeo = $request->input('search_geoserver');

        // 1. QUERY ADMIN (+ SEARCH)
        $admins = AdminKwMysql::select('*', DB::raw('ST_AsGeoJSON(geom) as geom_json'))
            ->when($searchAdmin, function ($query, $searchAdmin) {
                return $query->where('padukuhan', 'like', "%{$searchAdmin}%")
                             ->orWhere('kalurahan', 'like', "%{$searchAdmin}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'admin_page')
            ->appends(['search_admin' => $searchAdmin]); // appends biar pas pindah halaman, search-nya ga hilang

        // 2. QUERY JALAN (+ SEARCH)
        $jalans = JalanKwMysql::select('*', DB::raw('ST_AsGeoJSON(geom) as geom_json'))
            ->when($searchJalan, function ($query, $searchJalan) {
                return $query->where('nama', 'like', "%{$searchJalan}%")
                             ->orWhere('kondisi', 'like', "%{$searchJalan}%")
                             ->orWhere('kewenangan', 'like', "%{$searchJalan}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'jalan_page')
            ->appends(['search_jalan' => $searchJalan]);

        // 3. QUERY MASJID (+ SEARCH)
        $masjids = MasjidKwMysql::select('*', DB::raw('ST_Longitude(geom) as lng, ST_Latitude(geom) as lat'))
            ->when($searchMasjid, function ($query, $searchMasjid) {
                return $query->where('nama', 'like', "%{$searchMasjid}%")
                             ->orWhere('takmir_cp', 'like', "%{$searchMasjid}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'masjid_page')
            ->appends(['search_masjid' => $searchMasjid]);

        // 4. QUERY GEOSERVER (+ SEARCH) <-- UBAH BAGIAN INI
        $geoservers = GeoserverDb::when($searchGeo, function ($query, $searchGeo) {
                return $query->where('title', 'like', "%{$searchGeo}%")
                             ->orWhere('layer_name', 'like', "%{$searchGeo}%")
                             ->orWhere('type', 'like', "%{$searchGeo}%");
            })
            ->orderBy('id', 'desc')
            ->paginate(10, ['*'], 'geoserver_page')
            ->appends(['search_geoserver' => $searchGeo]);

        $selfHosteds = CesiumSelfHosted::all();
        $ions = CesiumIon::all();

        return view('admin.dashboard', compact('admins', 'jalans', 'masjids', 'geoservers', 'selfHosteds', 'ions'));
    }

    // =========================================================
    // FUNGSI SAKTI: CONVERT GEOJSON ARRAY KE WKT (LAT LNG)
    // =========================================================
    private function geojsonToWkt($geometry) {
        $type = $geometry['type'];
        $coords = $geometry['coordinates'];

        if ($type === 'LineString') {
            $points = [];
            foreach ($coords as $c) $points[] = $c[1] . ' ' . $c[0];
            return "LINESTRING(" . implode(', ', $points) . ")";
        } elseif ($type === 'MultiLineString') {
            $lines = [];
            foreach ($coords as $line) {
                $points = [];
                foreach ($line as $c) $points[] = $c[1] . ' ' . $c[0];
                $lines[] = "(" . implode(', ', $points) . ")";
            }
            return "MULTILINESTRING(" . implode(', ', $lines) . ")";
        } elseif ($type === 'Polygon') {
            $rings = [];
            foreach ($coords as $ring) {
                $points = [];
                foreach ($ring as $c) $points[] = $c[1] . ' ' . $c[0];
                $rings[] = "(" . implode(', ', $points) . ")";
            }
            return "POLYGON(" . implode(', ', $rings) . ")";
        } elseif ($type === 'MultiPolygon') {
            $polys = [];
            foreach ($coords as $poly) {
                $rings = [];
                foreach ($poly as $ring) {
                    $points = [];
                    foreach ($ring as $c) $points[] = $c[1] . ' ' . $c[0];
                    $rings[] = "(" . implode(', ', $points) . ")";
                }
                $polys[] = "(" . implode(', ', $rings) . ")";
            }
            return "MULTIPOLYGON(" . implode(', ', $polys) . ")";
        }
        return null;
    }

    // =========================================================
    // 1. CRUD BATAS WILAYAH (ADMIN)
    // =========================================================
    public function importAdminKw(Request $request) {
        $request->validate(['file_geojson' => 'required|file']);
        try {
            $file = file_get_contents($request->file('file_geojson')->getRealPath());
            $geojson = json_decode($file, true);
            $count = 0;

            foreach ($geojson['features'] as $feature) {
                if (isset($feature['geometry'])) {
                    $wkt = $this->geojsonToWkt($feature['geometry']);
                    if(!$wkt) continue;

                    $geom = DB::raw("ST_GeomFromText('{$wkt}', 4326)");
                    $props = $feature['properties'];

                    AdminKwMysql::create([
                        'feature_id' => $props['id'] ?? null,
                        'kalurahan' => $props['Kalurahan'] ?? '',
                        'padukuhan' => $props['Padukuhan'] ?? '',
                        'luas' => (float)($props['LUAS'] ?? 0),
                        'jumlah_kk' => (int)($props['JUMLAH_KK'] ?? 0),
                        'jumlah_penduduk' => (int)($props['JUM_PDDK'] ?? 0),
                        'jumlah_laki' => (int)($props['JUM_LAKI2'] ?? 0),
                        'jumlah_perempuan' => (int)($props['JUM_PEREMP'] ?? 0),
                        'geom' => $geom
                    ]);
                    $count++;
                }
            }
            return back()->with('success', "$count Data Batas Wilayah berhasil diimport!");
        } catch (\Exception $e) { return back()->with('error', 'Gagal import: ' . $e->getMessage()); }
    }

    public function exportAdminKw() {
        $admins = AdminKwMysql::select('*', DB::raw('ST_AsGeoJSON(geom) as geom_json'))->get();
        $features = [];
        foreach ($admins as $a) {
            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $a->feature_id, 'Kalurahan' => $a->kalurahan, 'Padukuhan' => $a->padukuhan,
                    'LUAS' => $a->luas, 'JUMLAH_KK' => $a->jumlah_kk, 'JUM_PDDK' => $a->jumlah_penduduk,
                    'JUM_LAKI2' => $a->jumlah_laki, 'JUM_PEREMP' => $a->jumlah_perempuan,
                ],
                'geometry' => json_decode($a->geom_json)
            ];
        }
        $geojson = ['type' => 'FeatureCollection', 'name' => 'admin_karangwuni', 'crs' => ['type' => 'name', 'properties' => ['name' => 'urn:ogc:def:crs:OGC:1.3:CRS84']], 'features' => $features];
        return response()->json($geojson)->header('Content-Disposition', 'attachment; filename="batas_wilayah.geojson"');
    }

    public function destroyAdminKw($id) {
        AdminKwMysql::findOrFail($id)->delete();
        return back()->with('success', 'Data Batas Wilayah berhasil dihapus!');
    }

    public function storeAdminKw(Request $request) {
        try {
            $wkt = $this->geojsonToWkt(json_decode($request->geom, true));
            $geom = DB::raw("ST_GeomFromText('{$wkt}', 4326)");

            AdminKwMysql::create([
                'kalurahan' => $request->kalurahan, 'padukuhan' => $request->padukuhan, 'luas' => $request->luas,
                'jumlah_kk' => $request->jumlah_kk, 'jumlah_penduduk' => $request->jumlah_penduduk,
                'jumlah_laki' => $request->jumlah_laki, 'jumlah_perempuan' => $request->jumlah_perempuan, 'geom' => $geom
            ]);
            return back()->with('success', 'Data Batas Wilayah ditambahkan!');
        } catch (\Exception $e) { return back()->with('error', 'Gagal: ' . $e->getMessage()); }
    }

    public function updateAdminKw(Request $request, $id) {
        try {
            $admin = AdminKwMysql::findOrFail($id);
            $wkt = $this->geojsonToWkt(json_decode($request->geom, true));
            $geom = DB::raw("ST_GeomFromText('{$wkt}', 4326)");

            $admin->update([
                'kalurahan' => $request->kalurahan, 'padukuhan' => $request->padukuhan, 'luas' => $request->luas,
                'jumlah_kk' => $request->jumlah_kk, 'jumlah_penduduk' => $request->jumlah_penduduk,
                'jumlah_laki' => $request->jumlah_laki, 'jumlah_perempuan' => $request->jumlah_perempuan, 'geom' => $geom
            ]);
            return back()->with('success', 'Data Batas Wilayah diperbarui!');
        } catch (\Exception $e) { return back()->with('error', 'Gagal: ' . $e->getMessage()); }
    }

    // =========================================================
    // 2. CRUD JARINGAN JALAN
    // =========================================================
    public function importJalanKw(Request $request) {
        $request->validate(['file_geojson' => 'required|file']);
        try {
            $file = file_get_contents($request->file('file_geojson')->getRealPath());
            $geojson = json_decode($file, true);
            $count = 0;

            foreach ($geojson['features'] as $feature) {
                if (isset($feature['geometry'])) {
                    $wkt = $this->geojsonToWkt($feature['geometry']);
                    if(!$wkt) continue;

                    $geom = DB::raw("ST_GeomFromText('{$wkt}', 4326)");
                    $props = $feature['properties'];

                    JalanKwMysql::create([
                        'feature_id' => $props['id'] ?? null,
                        'nama' => $props['Nama'] ?? 'Jalan Tanpa Nama',
                        'panjang' => (float)($props['Panjang'] ?? 0),
                        'luas' => (float)($props['Luas'] ?? 0),
                        'aset_tanah' => (float)($props['Asettanah'] ?? 0),
                        'kondisi' => $props['Kondisi'] ?? '',
                        'kewenangan' => $props['Kewenangan'] ?? '',
                        'foto_awal' => $props['FotoAwal'] ?? null,
                        'foto_akhir' => $props['FotoAkhir'] ?? null,
                        'rer_njop' => (float)($props['RERNJOP'] ?? 0),
                        'status' => $props['Status'] ?? '',
                        'lebar' => (float)($props['Lebar'] ?? 0),
                        'asal' => $props['Asal'] ?? '',
                        'layer' => $props['layer'] ?? '',
                        'geom' => $geom
                    ]);
                    $count++;
                }
            }
            return back()->with('success', "$count Data Jaringan Jalan berhasil diimport!");
        } catch (\Exception $e) { return back()->with('error', 'Gagal import: ' . $e->getMessage()); }
    }

    public function exportJalanKw() {
        $jalans = JalanKwMysql::select('*', DB::raw('ST_AsGeoJSON(geom) as geom_json'))->get();
        $features = [];
        foreach ($jalans as $j) {
            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $j->feature_id, 'Nama' => $j->nama, 'Panjang' => $j->panjang, 'Luas' => $j->luas,
                    'Asettanah' => $j->aset_tanah, 'Kondisi' => $j->kondisi, 'Kewenangan' => $j->kewenangan,
                    'FotoAwal' => $j->foto_awal, 'FotoAkhir' => $j->foto_akhir, 'RERNJOP' => $j->rer_njop,
                    'Status' => $j->status, 'Lebar' => $j->lebar, 'Asal' => $j->asal, 'layer' => $j->layer,
                ],
                'geometry' => json_decode($j->geom_json)
            ];
        }
        $geojson = ['type' => 'FeatureCollection', 'name' => 'jaringan_jalan', 'crs' => ['type' => 'name', 'properties' => ['name' => 'urn:ogc:def:crs:OGC:1.3:CRS84']], 'features' => $features];
        return response()->json($geojson)->header('Content-Disposition', 'attachment; filename="jaringan_jalan.geojson"');
    }

    public function destroyJalanKw($id) {
        $jalan = JalanKwMysql::findOrFail($id);
        // Hapus foto jika ada
        if ($jalan->foto_awal && file_exists(public_path('jalan/' . $jalan->foto_awal))) unlink(public_path('jalan/' . $jalan->foto_awal));
        if ($jalan->foto_akhir && file_exists(public_path('jalan/' . $jalan->foto_akhir))) unlink(public_path('jalan/' . $jalan->foto_akhir));
        $jalan->delete();
        return back()->with('success', 'Data Jalan berhasil dihapus!');
    }

    public function storeJalanKw(Request $request) {
        try {
            $wkt = $this->geojsonToWkt(json_decode($request->geom, true));
            $geom = DB::raw("ST_GeomFromText('{$wkt}', 4326)");

            JalanKwMysql::create([
                'nama' => $request->nama, 'panjang' => $request->panjang, 'lebar' => $request->lebar,
                'kondisi' => $request->kondisi, 'kewenangan' => $request->kewenangan, 'status' => $request->status, 'geom' => $geom
            ]);
            return back()->with('success', 'Data Jaringan Jalan ditambahkan!');
        } catch (\Exception $e) { return back()->with('error', 'Gagal: ' . $e->getMessage()); }
    }

    public function updateJalanKw(Request $request, $id) {
        try {
            $jalan = JalanKwMysql::findOrFail($id);
            $wkt = $this->geojsonToWkt(json_decode($request->geom, true));
            $geom = DB::raw("ST_GeomFromText('{$wkt}', 4326)");

            $jalan->update([
                'nama' => $request->nama, 'panjang' => $request->panjang, 'lebar' => $request->lebar,
                'kondisi' => $request->kondisi, 'kewenangan' => $request->kewenangan, 'status' => $request->status, 'geom' => $geom
            ]);
            return back()->with('success', 'Data Jaringan Jalan diperbarui!');
        } catch (\Exception $e) { return back()->with('error', 'Gagal: ' . $e->getMessage()); }
    }

    // =========================================================
    // 3. CRUD MASJID
    // =========================================================
    public function storeMasjidKw(Request $request) {
        $request->validate(['nama' => 'required|string', 'longitude' => 'required|numeric', 'latitude' => 'required|numeric']);
        try {
            $geom = DB::raw("ST_GeomFromText('POINT({$request->latitude} {$request->longitude})', 4326)");
            $fotoPath = null;
            if ($request->hasFile('foto')) {
                $file = $request->file('foto');
                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $file->move(public_path('masjid'), $filename);
                $fotoPath = 'masjid/' . $filename;
            }
            MasjidKwMysql::create([
                'nama' => $request->nama, 'luas_m2' => $request->luas_m2 ?? 0, 'jumlah_jamaah' => $request->jumlah_jamaah ?? 0,
                'takmir_cp' => $request->takmir_cp, 'no_telepon' => $request->no_telepon, 'foto' => $fotoPath,
                'icon_url' => $request->icon_url ?? './0.png', 'geom' => $geom
            ]);
            return back()->with('success', 'Data Masjid ditambahkan!');
        } catch (\Exception $e) { return back()->with('error', 'Gagal: ' . $e->getMessage()); }
    }

    public function updateMasjidKw(Request $request, $id) {
        try {
            $masjid = MasjidKwMysql::findOrFail($id);
            $geom = DB::raw("ST_GeomFromText('POINT({$request->latitude} {$request->longitude})', 4326)");
            $fotoPath = $masjid->foto;
            if ($request->hasFile('foto')) {
                if ($fotoPath && file_exists(public_path($fotoPath))) unlink(public_path($fotoPath));
                $file = $request->file('foto');
                $filename = time() . '_' . preg_replace('/\s+/', '_', $file->getClientOriginalName());
                $file->move(public_path('masjid'), $filename);
                $fotoPath = 'masjid/' . $filename;
            }
            $masjid->update([
                'nama' => $request->nama, 'luas_m2' => $request->luas_m2 ?? 0, 'jumlah_jamaah' => $request->jumlah_jamaah ?? 0,
                'takmir_cp' => $request->takmir_cp, 'no_telepon' => $request->no_telepon, 'foto' => $fotoPath,
                'icon_url' => $request->icon_url ?? $masjid->icon_url, 'geom' => $geom
            ]);
            return back()->with('success', 'Data Masjid diperbarui!');
        } catch (\Exception $e) { return back()->with('error', 'Gagal: ' . $e->getMessage()); }
    }

    public function importMasjidKw(Request $request) {
        $request->validate(['file_geojson' => 'required|file']);
        try {
            $file = file_get_contents($request->file('file_geojson')->getRealPath());
            $geojson = json_decode($file, true);
            $count = 0;
            foreach ($geojson['features'] as $feature) {
                if (isset($feature['geometry']) && $feature['geometry']['type'] === 'Point') {
                    $lng = $feature['geometry']['coordinates'][0];
                    $lat = $feature['geometry']['coordinates'][1];
                    $props = $feature['properties'];
                    $geom = DB::raw("ST_GeomFromText('POINT({$lat} {$lng})', 4326)");
                    MasjidKwMysql::create([
                        'feature_id' => $props['id'] ?? null, 'nama' => $props['Nama'] ?? 'Tanpa Nama',
                        'luas_m2' => (float)($props['LUAS  M2'] ?? 0), 'jumlah_jamaah' => (int)($props['JUM JAMAAH'] ?? 0),
                        'takmir_cp' => $props['TAKMIR CP'] ?? null, 'no_telepon' => $props['NO  HENPON'] ?? null,
                        'foto' => $props['FOTO'] ?? null, 'icon_url' => $props['icon_url'] ?? null, 'geom' => $geom
                    ]);
                    $count++;
                }
            }
            return back()->with('success', "$count Data Masjid diimport!");
        } catch (\Exception $e) { return back()->with('error', 'Gagal: ' . $e->getMessage()); }
    }

    public function exportMasjidKw() {
        $masjids = MasjidKwMysql::select('*', DB::raw('ST_Longitude(geom) as lng, ST_Latitude(geom) as lat'))->get();
        $features = [];
        foreach ($masjids as $m) {
            $features[] = [
                'type' => 'Feature',
                'properties' => [
                    'id' => $m->feature_id, 'Nama' => $m->nama, 'LUAS  M2' => $m->luas_m2, 'JUM JAMAAH' => $m->jumlah_jamaah,
                    'TAKMIR CP' => $m->takmir_cp, 'NO  HENPON' => $m->no_telepon, 'FOTO' => $m->foto, 'icon_url' => $m->icon_url,
                ],
                'geometry' => ['type' => 'Point', 'coordinates' => [$m->lng, $m->lat]]
            ];
        }
        $geojson = ['type' => 'FeatureCollection', 'name' => 'masjid_export', 'crs' => ['type' => 'name', 'properties' => ['name' => 'urn:ogc:def:crs:OGC:1.3:CRS84']], 'features' => $features];
        return response()->json($geojson)->header('Content-Disposition', 'attachment; filename="data_masjid.geojson"');
    }

    public function destroyMasjidKw($id) {
        $masjid = MasjidKwMysql::findOrFail($id);
        if ($masjid->foto && file_exists(public_path($masjid->foto))) unlink(public_path($masjid->foto));
        $masjid->delete();
        return back()->with('success', 'Data Masjid dihapus!');
    }

    // =========================================================
    // 4. CRUD GEOSERVER (DYNAMIC LAYERS)
    // =========================================================
    public function storeGeoserver(Request $request) {
        $request->validate(['title' => 'required', 'layer_name' => 'required', 'type' => 'required']);
        try {
            GeoserverDb::create([
                'title' => $request->title,
                'layer_name' => $request->layer_name,
                'workspace' => $request->workspace ?? 'latihan_leaflet',
                'type' => $request->type,
                'base_url' => $request->base_url ?? 'http://localhost:8080/geoserver/',
                'enable_wms' => $request->has('enable_wms'),
                'enable_wfs' => $request->has('enable_wfs'),
                'enable_wmts' => $request->has('enable_wmts'),
                'is_active' => $request->has('is_active'),
                'z_index' => $request->z_index ?? 1,
            ]);
            return back()->with('success', 'Konfigurasi Layer GeoServer ditambahkan!');
        } catch (\Exception $e) { return back()->with('error', 'Gagal: ' . $e->getMessage()); }
    }

    public function updateGeoserver(Request $request, $id) {
        $request->validate(['title' => 'required', 'layer_name' => 'required', 'type' => 'required']);
        try {
            $geo = GeoserverDb::findOrFail($id);
            $geo->update([
                'title' => $request->title,
                'layer_name' => $request->layer_name,
                'workspace' => $request->workspace,
                'type' => $request->type,
                'base_url' => $request->base_url,
                'enable_wms' => $request->has('enable_wms'),
                'enable_wfs' => $request->has('enable_wfs'),
                'enable_wmts' => $request->has('enable_wmts'),
                'is_active' => $request->has('is_active'),
                'z_index' => $request->z_index,
            ]);
            return back()->with('success', 'Konfigurasi Layer GeoServer diperbarui!');
        } catch (\Exception $e) { return back()->with('error', 'Gagal: ' . $e->getMessage()); }
    }

    public function destroyGeoserver($id) {
        GeoserverDb::findOrFail($id)->delete();
        return back()->with('success', 'Data Layer GeoServer dihapus!');
    }
}
