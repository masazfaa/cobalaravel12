<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Tambah kolom role, defaultnya kita set 'admin'
            $table->string('role')->default('admin')->after('email');
        });

        Schema::table('users', function (Blueprint $table) {
            // Default false artinya: Daftar = Tidak Aktif (Harus nunggu approve)
            $table->boolean('is_active')->default(false)->after('password');
        });

    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_active');
        });

    }
};
