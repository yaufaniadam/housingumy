<?php

namespace App\Http\Controllers;

use App\Models\UnitKerja;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UnitKerjaAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::guard('unit_kerja')->check()) {
            return redirect()->route('unit-kerja.dashboard');
        }
        return view('unit-kerja.auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'code' => 'required|string',
            'password' => 'required',
        ]);

        $unitKerja = UnitKerja::where('code', $credentials['code'])->first();

        if ($unitKerja && Hash::check($credentials['password'], $unitKerja->password)) {
            Auth::guard('unit_kerja')->login($unitKerja, $request->boolean('remember'));
            $request->session()->regenerate();
            return redirect()->intended(route('unit-kerja.dashboard'));
        }

        return back()->withErrors([
            'code' => 'Kode atau password salah.',
        ])->onlyInput('code');
    }

    public function logout(Request $request)
    {
        Auth::guard('unit_kerja')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('unit-kerja.login');
    }
}
