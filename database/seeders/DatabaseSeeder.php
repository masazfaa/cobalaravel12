<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Buat 1 Akun Superadmin
        User::create([
            'name' => 'Superadmin Utama',
            'email' => 'superadmin@gmail.com', // Email login Anda
            'password' => Hash::make('sayaadalahadmin'), // Password Anda
            'role' => 'superadmin',
            'is_active' => true,
        ]);

        // Opsional: Buat 1 Akun Admin biasa untuk tes
        User::create([
            'name' => 'Admin Staff',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('sayaadalahadmin'),
            'role' => 'admin',
            'is_active' => true,
        ]);

        $this->call([
            adminkwmysql::class,   // Data Administrasi
            jalankwmysql::class,   // Data Jalan
            masjidkwmysql::class,  // Data Masjid
            geoserverseeder::class, // Data Geoserver
            CesiumDataSeeder::class, // Data Cesium
        ]);
    }
}
