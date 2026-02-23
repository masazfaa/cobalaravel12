<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail; // TAMBAHAN: Import Mail
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'admin',
            'is_active' => false,
        ]);

        event(new Registered($user));
        try {
            $pemilikEmail = 'geoanfaspasial@gmail.com';

            Mail::raw("Halo Superadmin,\n\nAda pendaftaran user baru:\nNama: {$user->name}\nEmail: {$user->email}\n\nMohon cek dashboard untuk melakukan aktivasi.", function ($message) use ($pemilikEmail) {
                $message->to($pemilikEmail)
                        ->subject('Notifikasi Pendaftaran User Baru');
            });
        } catch (\Exception $e) {

        }

        return redirect()->route('login')->with('status', 'Pendaftaran berhasil! Akun Anda menunggu persetujuan Admin untuk diaktifkan.');
    }
}
