<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminKwMysql extends Model
{
    use HasFactory;
    protected $table = 'adminkwmysql'; // Nama tabel di database
    protected $guarded = ['id'];
}
