<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CesiumIon extends Model
{
    use HasFactory;
    protected $table = 'cesiumion'; // Nama tabel di MySQL
    protected $guarded = ['id'];
}
