<?php

namespace App\Console\Commands;

use App\Models\Guru;
use App\Models\Siswa;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SyncExternalData extends Command
{
    protected $signature = 'app:sync-data';
    protected $description = 'Sinkronisasi data Guru dan Siswa dari API eksternal ke database lokal';

    public function handle()
    {
        $this->info('Memulai sinkronisasi data...');

        // 1. Sync Guru
        $this->syncGurus();

        // 2. Sync Siswa
        $this->syncStudents();

        // 3. Ensure Admin
        $this->ensureAdmin();

        $this->info('Sinkronisasi selesai!');
    }

    private function syncGurus()
    {
        $url = env('API_GURU_URL');
        if (!$url) {
            $this->error('API_GURU_URL tidak dikonfigurasi di .env');
            return;
        }

        $this->info('Sinkronisasi Guru...');
        $response = Http::get($url);
        
        if ($response->successful()) {
            $json = $response->json();
            $data = isset($json['data']) ? $json['data'] : $json;
            $count = 0;

            foreach ($data as $item) {
                // Clean email - crucial for Guru login
                $email = strtolower($item['email'] ?? '');
                $email = str_replace(' ', '', $email);
                if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) continue;

                // Handle unique constraints for NIP, NUPTK, NIK
                // MySQL treats '' as a value, if multiple gurus have '' it causes duplication error.
                // We must set them to NULL if they are empty strings.
                $nip = trim($item['nip'] ?? '');
                $nuptk = trim($item['nuptk'] ?? '');
                $nik = trim($item['nik'] ?? '');

                Guru::updateOrCreate(
                    ['email' => $email],
                    [
                        'nip' => $nip !== '' ? $nip : null,
                        'external_guru_id' => $item['guru_id'] ?? null,
                        'nama' => $item['nama'],
                        'nuptk' => $nuptk !== '' ? $nuptk : null,
                        'nik' => $nik !== '' ? $nik : null,
                        'jenis_kelamin' => $item['jenis_kelamin'] ?? 'L',
                        'tempat_lahir' => $item['tempat_lahir'] ?? null,
                        'tanggal_lahir' => $item['tanggal_lahir'] ?? null,
                        'no_hp' => $item['no_hp'] ?? null,
                        'alamat' => $item['alamat'] ?? null,
                        'password' => Hash::make('12345678'),
                    ]
                );
                $count++;
            }
            $this->info("Berhasil sinkron $count data Guru.");
        } else {
            $this->error('Gagal mengambil data Guru dari API.');
        }
    }

    private function syncStudents()
    {
        $url = env('API_KELAS_URL');
        if (!$url) {
            $this->error('API_KELAS_URL tidak dikonfigurasi di .env');
            return;
        }

        $this->info('Sinkronisasi Siswa...');
        $response = Http::get($url);
        
        if ($response->successful()) {
            $json = $response->json();
            $data = isset($json['data']) ? $json['data'] : $json;
            $count = 0;

            foreach ($data as $item) {
                if (!isset($item['no_induk'])) continue;

                $siswa = Siswa::where('nis', $item['no_induk'])->first();
                if (!$siswa) {
                    $siswa = new Siswa();
                    $siswa->nis = $item['no_induk'];
                    $siswa->qr_code = (string) Str::uuid();
                }
                
                $siswa->nama = $item['nama'];
                $siswa->no_hp = $item['no_telp'] ?? null;
                $siswa->jenis_kelamin = (isset($item['jenis_kelamin']) && in_array($item['jenis_kelamin'], ['L', 'P'])) ? $item['jenis_kelamin'] : 'L';
                $siswa->save();
                $count++;
            }
            $this->info("Berhasil sinkron $count data Siswa.");
        } else {
            $this->error('Gagal mengambil data Siswa dari API.');
        }
    }

    private function ensureAdmin()
    {
        if (User::where('role', 'admin')->count() === 0) {
            User::create([
                'name' => 'Administrator',
                'email' => 'admin@presencex.com',
                'password' => Hash::make('admin123'),
                'role' => 'admin'
            ]);
            $this->info('Admin default dibuat: admin@presencex.com / admin123');
        }
    }
}
