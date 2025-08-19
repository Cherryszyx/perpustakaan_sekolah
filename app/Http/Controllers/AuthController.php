<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kunjungan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $credentials = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'number_type' => ['required', Rule::in(User::NUMBER_TYPES)],
            'number' => ['required', 'numeric', 'unique:' . User::class],
            'address' => ['required', 'string', 'max:255'],
            'telephone' => ['required', 'numeric'],
            'gender' => ['required', Rule::in(['Man', 'Woman'])],
            'password' => ['required', 'string', 'confirmed', 'max:255'],
        ]);

        $credentials['role'] = 'Member';
        $credentials['password'] = Hash::make($credentials['password']);

        $user = User::create($credentials);
        Auth::login($user);

        return redirect()->route('home');
    }

    public function authenticate(Request $request)
    {
        $credentials = $request->validate([
            'number' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            $user = Auth::user();

            // Deteksi login via QR
            $isViaQR = $request->input('via') === 'qr' || session('from_qr');

            if ($isViaQR) {
                Kunjungan::create([
                    'user_id' => $user->id,
                    'waktu_kunjungan' => now(),
                    'via_qr' => true,
                ]);
                session()->forget('from_qr');
            }

            // Arahkan berdasarkan role
            switch ($user->role) {
            case 'Admin':
            case 'Librarian':
                return redirect()->route('admin.dashboard');

            case 'Member':
            default:
                return redirect('/'); // route('home') juga boleh
        }
    }

        return back()->withErrors([
            'number' => 'The provided credentials do not match our records.',
        ])->onlyInput('number');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
