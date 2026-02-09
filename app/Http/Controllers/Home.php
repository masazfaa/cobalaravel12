<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\CesiumSelfHosted; // Import Model
use App\Models\CesiumIon;        // Import Model
use App\Models\AdminKwMysql;
use App\Models\JalanKwMysql;
use App\Models\MasjidKwMysql;
use App\Models\GeoserverDb;

class Home extends Controller
{
public function index()
    {
        // 1. Ambil Data Admin (Polygon)
        // Kita select semua kolom (*), lalu ubah kolom 'geom' jadi teks GeoJSON
        $dataAdmin = AdminKwMysql::select('*', DB::raw('ST_AsGeoJSON(geom) as geometry_json'))->get();
        $adminGeoJson = $this->formatGeoJSON($dataAdmin);

        // 2. Ambil Data Jalan (LineString)
        $dataJalan = JalanKwMysql::select('*', DB::raw('ST_AsGeoJSON(geom) as geometry_json'))->get();
        $jalanGeoJson = $this->formatGeoJSON($dataJalan);

        // 3. Ambil Data Masjid (Point)
        $dataMasjid = MasjidKwMysql::select('*', DB::raw('ST_AsGeoJSON(geom) as geometry_json'))->get();
        $masjidGeoJson = $this->formatGeoJSON($dataMasjid);

        // Kirim ke View
        return view('map.map1', compact('adminGeoJson', 'jalanGeoJson', 'masjidGeoJson'));
    }

    // Fungsi helper untuk merubah format database ke standar GeoJSON FeatureCollection
    private function formatGeoJSON($data)
    {
        $features = [];

        foreach ($data as $item) {
            $properties = $item->toArray();

            // Hapus data geometry mentah biar tidak berat
            unset($properties['geom']);
            unset($properties['geometry_json']);

            $features[] = [
                'type' => 'Feature',
                'geometry' => json_decode($item->geometry_json), // Decode string JSON dari MySQL
                'properties' => $properties
            ];
        }

        return [
            'type' => 'FeatureCollection',
            'features' => $features
        ];
    }

public function geoserver()
    {
        // 1. Ambil data layer dari tabel geoserverdb
        // Filter: Hanya yang is_active = true
        // Urutkan: Berdasarkan z_index (Layer background/raster di bawah, vector di atas)
        $layers = GeoserverDb::where('is_active', true)
                    ->orderBy('z_index', 'asc')
                    ->get();

        // 2. Kirim ke View
        return view('map.map2', compact('layers'));
    }

    function cesium()
    {
        $dataSelfHosted = CesiumSelfHosted::all();

        return view('map.map3', compact('dataSelfHosted'));
    }

    function cesiumion()
        {
        $dataIon = CesiumIon::all();

        return view('map.map4', compact('dataIon'));
    }
}
