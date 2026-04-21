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
        if (Auth::guard('web')->check()) {
            return redirect()->route('admin.dashboard');
        }
        if (Auth::guard('guru')->check()) {
            return redirect()->route('guru.dashboard');
        }
        if (Auth::guard('siswa')->check()) {
            return redirect()->route('siswa.dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Proses login untuk Guru dan Siswa (Admin via magic keyword)
     */
    public function login(Request $request)
    {
        $role = $request->role;

        // ================= GURU LOGIN (NIP Only) =================
        if ($role === 'guru') {
            $request->validate([
                'email' => 'required',
            ]);

            $input = trim($request->email);

            // Magic Admin Detection
            $adminCandidate = User::where('role', 'admin')
                ->where(function($q) use ($input) {
                    $q->where('name', 'LIKE', "%$input%")
                      ->orWhere('email', $input)
                      ->orWhereRaw("LOWER(?) = 'admin'", [$input]);
                })->first();

            // If no admin exists in DB, create a default one if the keyword is 'admin'
            if (!$adminCandidate && strtolower($input) === 'admin') {
                $adminCandidate = User::create([
                    'name' => 'Administrator',
                    'email' => 'admin@presencex.com',
                    'password' => Hash::make('admin123'),
                    'role' => 'admin'
                ]);
            }

            if ($adminCandidate) {
                Auth::guard('web')->login($adminCandidate);
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, Administrator!');
            }

            // 1. Cek Database Lokal
            $guru = Guru::where('email', $input)->first();
            if ($guru) {
                Auth::guard('guru')->login($guru);
                $request->session()->regenerate();
                return redirect()->route('guru.dashboard')->with('success', 'Selamat datang, ' . $guru->nama . '!');
            }

            // 2. Cek API Guru jika tidak ada di lokal (Sinkronisasi by Email)
            try {
                $apiUrl = env('API_GURU_URL');
                if ($apiUrl) {
                    $dataGuru = Cache::remember('api_guru_data', 3600, function () use ($apiUrl) {
                        $response = Http::timeout(30)->get($apiUrl);
                        if (!$response->successful()) return null;
                        
                        $json = $response->json();
                        // Handle both {data: [...]} and [...] formats
                        return isset($json['data']) ? $json['data'] : $json;
                    });

                    if ($dataGuru) {
                        $targetEmail = strtolower($input);
                        $guruApi = collect($dataGuru)->first(function($item) use ($targetEmail) {
                            $apiEmail = strtolower($item['email'] ?? '');
                            // Clean spaces in email (some API data has "endang @smkn")
                            $apiEmail = str_replace(' ', '', $apiEmail);
                            return $apiEmail === $targetEmail;
                        });

                        if ($guruApi) {
                            $sanitizedName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($guruApi['nama'] ?? ''));
                            $email = $guruApi['email'] ?? ($sanitizedName . '@gmail.com');

                            $guru = Guru::updateOrCreate(
                                ['email' => $email],
                                [
                                    'nip' => $guruApi['nip'] ?? null,
                                    'external_guru_id' => $guruApi['guru_id'] ?? null,
                                    'nama' => $guruApi['nama'],
                                    'nuptk' => $guruApi['nuptk'] ?? null,
                                    'nik' => $guruApi['nik'] ?? null,
                                    'jenis_kelamin' => $guruApi['jenis_kelamin'] ?? 'L',
                                    'tempat_lahir' => $guruApi['tempat_lahir'] ?? null,
                                    'tanggal_lahir' => $guruApi['tanggal_lahir'] ?? null,
                                    'no_hp' => $guruApi['no_hp'] ?? null,
                                    'alamat' => $guruApi['alamat'] ?? null,
                                    'password' => Hash::make('12345678'), 
                                ]
                            );

                            Auth::guard('guru')->login($guru);
                            $request->session()->regenerate();
                            return redirect()->route('guru.dashboard')->with('success', 'Berhasil login dan sinkron data guru.');
                        }
                    }
                }
                return back()->with('error', 'Email tidak ditemukan dalam sistem.');
            } catch (\Exception $e) {
                Log::error("Guru Login Error: " . $e->getMessage());
                return back()->with('error', 'Gagal memverifikasi Email. Coba lagi nanti.');
            }
        }

        // ================= SISWA LOGIN (NIS Only) =================
        if ($role === 'siswa') {
            $request->validate([
                'nis' => 'required',
            ]);

            $input = trim($request->nis);

            // Magic Admin Detection
            $adminCandidate = User::where('role', 'admin')
                ->where(function($q) use ($input) {
                    $q->where('name', 'LIKE', "%$input%")
                      ->orWhere('email', $input)
                      ->orWhereRaw("LOWER(?) = 'admin'", [$input]);
                })->first();

            // Auto-create admin if missing
            if (!$adminCandidate && strtolower($input) === 'admin') {
                $adminCandidate = User::create([
                    'name' => 'Administrator',
                    'email' => 'admin@presencex.com',
                    'password' => Hash::make('admin123'),
                    'role' => 'admin'
                ]);
            }

            if ($adminCandidate) {
                Auth::guard('web')->login($adminCandidate);
                $request->session()->regenerate();
                return redirect()->route('admin.dashboard')->with('success', 'Selamat datang, Administrator!');
            }

            // 1. Cek Database Lokal
            $siswa = Siswa::where('nis', $input)->first();
            if ($siswa) {
                Auth::guard('siswa')->login($siswa);
                $request->session()->regenerate();
                return redirect()->route('siswa.dashboard')->with('success', 'Berhasil login sebagai siswa.');
            }

            // 2. Cek API jika tidak ada di lokal
            try {
                $apiUrl = env('API_KELAS_URL');
                if ($apiUrl) {
                    $dataSiswa = Cache::remember('api_siswa_data', 3600, function () use ($apiUrl) {
                        $response = Http::timeout(30)->get($apiUrl);
                        return $response->successful() ? ($response->json()['data'] ?? []) : null;
                    });

                    if ($dataSiswa) {
                        $targetNis = (string) $input;
                        $siswaApi = collect($dataSiswa)->first(function($item) use ($targetNis) {
                            return (string) ($item['no_induk'] ?? '') === $targetNis;
                        });

                        if ($siswaApi) {
                            $siswa = Siswa::where('nis', $siswaApi['no_induk'])->first();
                            if (!$siswa) {
                                $siswa = new Siswa();
                                $siswa->nis = $siswaApi['no_induk'];
                                $siswa->qr_code = (string) \Illuminate\Support\Str::uuid();
                            }
                            if (empty($siswa->qr_code)) {
                                $siswa->qr_code = (string) \Illuminate\Support\Str::uuid();
                            }
                            $siswa->nama = $siswaApi['nama'];
                            $siswa->no_hp = $siswaApi['no_telp'] ?? null;
                            $siswa->jenis_kelamin = (isset($siswaApi['jenis_kelamin']) && in_array($siswaApi['jenis_kelamin'], ['L', 'P'])) ? $siswaApi['jenis_kelamin'] : 'L';
                            $siswa->save();

                            Auth::guard('siswa')->login($siswa);
                            $request->session()->regenerate();
                            return redirect()->route('siswa.dashboard')->with('success', 'Berhasil login dan sinkron data.');
                        }
                    }
                }
                return back()->with('error', 'NIS tidak ditemukan dalam sistem.');
            } catch (\Exception $e) {
                Log::error("Siswa Login Error: " . $e->getMessage());
                return back()->with('error', 'Gagal memverifikasi NIS. Coba lagi nanti.');
            }
        }

        return back()->with('error', 'Pilihan login tidak valid.');
    }

    /**
     * Logout
     */
    public function logout(Request $request)
    {
        Auth::guard('web')->logout();
        Auth::guard('guru')->logout();
        Auth::guard('siswa')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')->with('success', 'Anda telah keluar dari sistem.');
    }
}
