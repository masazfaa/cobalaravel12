<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeoserverDb extends Model
{
    use HasFactory;

    // Pastikan nama tabel sesuai migration
    protected $table = 'geoserverdb';

    // Izinkan mass assignment (biar seeder jalan lancar)
    protected $guarded = ['id'];

    // Casting tipe data biar di JS nanti otomatis jadi Boolean/Integer
    protected $casts = [
        'enable_wms'  => 'boolean',
        'enable_wfs'  => 'boolean',
        'enable_wmts' => 'boolean',
        'is_active'   => 'boolean',
        'z_index'     => 'integer',
    ];
}
