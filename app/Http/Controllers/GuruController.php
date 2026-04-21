<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\Guru;
use Illuminate\Http\Request;

class GuruController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $guru = Guru::when($search, function ($query) use ($search) {
            $query->where('nama', 'like', "%{$search}%")
                ->orWhere('nip', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%");
        })
            ->select('id', 'nama', 'nip', 'email', 'jenis_kelamin', 'no_hp')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('guru.index', compact('guru'));
    }

    public function create()
    {
        return view('guru.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50|unique:guru,nip',
            'no_hp' => 'nullable|string|max:20',
            'email' => 'required|email|unique:guru,email',
        ]);

        Guru::create([
            'nama' => $request->nama,
            'nip' => $request->nip,
            'no_hp' => $request->no_hp,
            'email' => $request->email,
            'password' => '12345678',
        ]);

        return redirect()->route('guru.index')->with('success', 'Guru berhasil ditambahkan! Password default: 12345678');
    }

    public function edit(Guru $guru)
    {
        return view('guru.edit', compact('guru'));
    }

    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'nip' => 'nullable|string|max:50|unique:guru,nip,' . $guru->id,
            'no_hp' => 'nullable|string|max:20',
            'email' => 'required|email|unique:guru,email,' . $guru->id,
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:6']);
            $validated['password'] = $request->password;
        }

        $guru->update($validated);

        return redirect()->route('guru.index')->with('success', 'Data guru berhasil diperbarui!');
    }

    public function destroy(Guru $guru)
    {
        $guru->delete();
        return redirect()->route('guru.index')->with('success', 'Data guru berhasil dihapus!');
    }

    public function show(Guru $guru)
    {
        // Load related data for profile view
        $guru->loadCount(['jadwalPelajaran']);
        $guru->load('kelasWali:id,nama_kelas,jurusan,wali_kelas_id');
        
        return view('guru.show', compact('guru'));
    }

    public function syncApi()
    {
        $url = config('app.api_guru_url', env('API_GURU_URL'));

        try {
            $response = Http::timeout(60)->get($url);

            if (!$response->successful()) {
                return back()->with('error', 'Gagal mengambil data dari API (Server Sibuk/Error)');
            }

            $result = $response->json();
            $dataGuru = $result['data'] ?? $result;

            if (!is_array($dataGuru)) {
                return back()->with('error', 'Format data API tidak sesuai');
            }

            $count = 0;
            
            // Use DB transaction for batch operations
            DB::transaction(function () use ($dataGuru, &$count) {
                foreach ($dataGuru as $item) {
                    $nip = $item['nip'] ?? null;
                    if (is_string($nip) && trim($nip) === '') {
                        $nip = null;
                    }
                    
                    if (!$nip) continue;

                    $nama = $item['nama'] ?? 'Tanpa Nama';
                    $sanitizedName = preg_replace('/[^a-zA-Z0-9]/', '', strtolower($nama));
                    $email = $item['email'] ?? ($sanitizedName . '@gmail.com');

                    Guru::updateOrCreate(
                        ['nip' => $nip], 
                        [
                            'email' => $email,
                            'external_guru_id' => $item['guru_id'] ?? null,
                            'nama' => $nama,
                            'nuptk' => $item['nuptk'] ?? null,
                            'nik' => $item['nik'] ?? null,
                            'jenis_kelamin' => $item['jenis_kelamin'] ?? 'L',
                            'tempat_lahir' => $item['tempat_lahir'] ?? null,
                            'tanggal_lahir' => $item['tanggal_lahir'] ?? null,
                            'no_hp' => $item['no_hp'] ?? null,
                            'alamat' => $item['alamat'] ?? null,
                            'foto' => $item['photo'] ?? null,
                            'password' => Hash::make('12345678'), 
                        ]
                    );
                    $count++;
                }
            });

            return back()->with('success', "Berhasil sinkronisasi $count data guru dari API.");

        } catch (\Exception $e) {
            return back()->with('error', 'Koneksi API Gagal: ' . $e->getMessage());
        }
    }
}
