<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::where('is_active', false)->get();
        return view('admin.kelola_user', compact('users'));
    }

    public function approve($id)
    {
        $user = User::findOrFail($id);
        $user->is_active = true;
        $user->save();

        return redirect()->back()->with('success', 'User berhasil diaktifkan!');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);

        $user->delete();
        return redirect()->back()->with('success', 'User berhasil dihapus (Pendaftaran Ditolak).');
    }
}
