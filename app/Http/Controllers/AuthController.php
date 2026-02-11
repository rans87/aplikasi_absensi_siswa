<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login
     */
    public function showLogin()
    {
        Log::debug('showLogin hit', [
            'web' => Auth::guard('web')->check(),
            'guru' => Auth::guard('guru')->check(),
            'siswa' => Auth::guard('siswa')->check(),
        ]);

        if (Auth::guard('web')->check()) {
            Log::debug('Redirecting web user to admin dashboard');
            return redirect()->route('admin.dashboard');
        }
        if (Auth::guard('guru')->check()) {
            Log::debug('Redirecting guru user to guru dashboard');
            return redirect()->route('guru.dashboard');
        }
        if (Auth::guard('siswa')->check()) {
            Log::debug('Redirecting siswa user to siswa dashboard');
            return redirect()->route('siswa.dashboard');
        }
        
        return view('auth.login');
    }


    /**
     * Proses login untuk Admin, Guru, dan Siswa
     */
    public function login(Request $request)
    {
        Log::debug('Login Method Hit', $request->only('role', 'email', 'username', 'nis'));
        $role = $request->role;

        // ================= ADMIN LOGIN =================
        if ($role === 'admin') {
            $request->validate([
                'username' => 'required',
                'password' => 'required',
            ]);

            $credentials = [
                'email' => $request->username,
                'password' => $request->password
            ];

            if (Auth::guard('web')->attempt($credentials)) {
                $user = Auth::guard('web')->user();
                
                if ($user->role !== 'admin') {
                    Auth::guard('web')->logout();
                    return back()->with('error', 'Maaf, akun ini tidak memiliki hak akses Administrator.');
                }

                $request->session()->regenerate();
                return redirect()->route('admin.dashboard')->with('success', 'Berhasil login sebagai Admin.');
            }

            return back()->with('error', 'Username atau password Admin salah.');
        }

        // ================= GURU LOGIN =================
        if ($role === 'guru') {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required',
            ]);

            // dd('Guru login hit', $credentials); // Debugging
            Log::debug('Guru Attempting Login', ['email' => $request->email]);

            if (Auth::guard('guru')->attempt($credentials)) {
                Log::debug('Guru Attempt Success', [
                    'guard_check' => Auth::guard('guru')->check(),
                    'user_id' => Auth::guard('guru')->id(),
                    'session_id' => session()->getId()
                ]);

                $request->session()->regenerate();
                
                Log::debug('Session Regenerated', [
                    'new_session_id' => session()->getId(),
                    'guard_check_after_regeneration' => Auth::guard('guru')->check()
                ]);

                return redirect()->route('guru.dashboard')->with('success', 'Selamat datang, Guru!');
            }

            Log::warning('Guru Attempt Failed', ['email' => $request->email]);
            return back()->with('error', 'Kredensial Guru tidak valid.');
        }

        // ================= SISWA LOGIN =================
        if ($role === 'siswa') {
            $request->validate([
                'nis' => 'required',
                'nama' => 'required',
            ]);

            // 1. Cek Database Lokal Terlebih Dahulu (Agar Cepat & Ringan)
            $siswa = Siswa::where('nis', $request->nis)->first();
            if ($siswa && strtolower(trim($siswa->nama)) === strtolower(trim($request->nama))) {
                Auth::guard('siswa')->login($siswa);
                $request->session()->regenerate();
                return redirect()->route('siswa.dashboard')->with('success', 'Berhasil login.');
            }

            // 2. Jika tidak ada di lokal, baru verifikasi ke API dengan Caching
            try {
                $apiUrl = env('API_ROMBEL_URL');
                
                // Simpan data API di cache selama 60 menit supaya tidak download 2.8MB tiap kali login
                $dataSiswa = Cache::remember('api_siswa_data', 3600, function () use ($apiUrl) {
                    Log::debug('Fetching student data from API (Cache Missing)');
                    $response = Http::timeout(30)->get($apiUrl); // Timeout diperpanjang ke 30 detik
                    return $response->successful() ? ($response->json()['data'] ?? []) : null;
                });

                if (!$dataSiswa) {
                    return back()->with('error', 'Koneksi ke server pusat sedang sibuk (Timeout). Silakan hubungi operator.');
                }

                $targetNis = (string) $request->nis;
                
                $siswaApi = collect($dataSiswa)->first(function($item) use ($targetNis) {
                    return (string) ($item['no_induk'] ?? '') === $targetNis;
                });

                if (!$siswaApi || strtolower(trim($siswaApi['nama'] ?? '')) !== strtolower(trim($request->nama))) {
                    return back()->with('error', 'NIS atau Nama Siswa tidak cocok dengan database sekolah.');
                }

                // Update atau Create lokal agar login selanjutnya tidak butuh API
                $siswa = Siswa::updateOrCreate(
                    ['nis' => $siswaApi['no_induk']],
                    [
                        'nama' => $siswaApi['nama'],
                        'no_hp' => $siswaApi['no_telp'] ?? null,
                        'jenis_kelamin' => (isset($siswaApi['jenis_kelamin']) && in_array($siswaApi['jenis_kelamin'], ['L', 'P'])) ? $siswaApi['jenis_kelamin'] : 'L',
                    ]
                );

                Auth::guard('siswa')->login($siswa);
                $request->session()->regenerate();

                return redirect()->route('siswa.dashboard')->with('success', 'Berhasil login dan sinkron data.');

            } catch (\Exception $e) {
                Log::error("Siswa Login Error: " . $e->getMessage());
                return back()->with('error', 'Server pusat tidak merespon (Timeout). Pastikan koneksi internet stabil.');
            }
        }

        return back()->with('error', 'Pilihan login tidak valid.');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        // Logout dari semua guard
        Auth::guard('web')->logout();
        Auth::guard('guru')->logout();
        Auth::guard('siswa')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem.');
    }
}
