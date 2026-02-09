<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CesiumSelfHosted extends Model
{
    use HasFactory;
    protected $table = 'cesiumselfhosted'; // Nama tabel di MySQL
    protected $guarded = ['id'];
}
