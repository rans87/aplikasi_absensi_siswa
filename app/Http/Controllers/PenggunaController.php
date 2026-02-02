<?php

namespace App\Http\Controllers;

use App\Models\Pengguna;
use App\Models\Guru;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PenggunaController extends Controller
{
    public function index()
    {
        $pengguna = Pengguna::with(['guru', 'siswa'])->latest()->get();
        return view('pengguna.index', compact('pengguna'));
    }

    public function create()
    {
        $guru = Guru::all();
        $siswa = Siswa::all();
        return view('pengguna.create', compact('guru', 'siswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'username' => 'required|unique:pengguna',
            'password' => 'required|min:6',
            'role' => 'required',
        ]);

        Pengguna::create([
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'guru_id' => $request->guru_id,
            'siswa_id' => $request->siswa_id,
        ]);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil ditambahkan');
    }

    public function edit(Pengguna $pengguna)
    {
        $guru = Guru::all();
        $siswa = Siswa::all();
        return view('pengguna.edit', compact('pengguna', 'guru', 'siswa'));
    }

    public function update(Request $request, Pengguna $pengguna)
    {
        $request->validate([
            'username' => 'required|unique:pengguna,username,' . $pengguna->id,
            'role' => 'required',
        ]);

        $data = [
            'username' => $request->username,
            'role' => $request->role,
            'guru_id' => $request->guru_id,
            'siswa_id' => $request->siswa_id,
        ];

        if ($request->password) {
            $data['password'] = Hash::make($request->password);
        }

        $pengguna->update($data);

        return redirect()->route('pengguna.index')->with('success', 'Pengguna berhasil diperbarui');
    }

    public function destroy(Pengguna $pengguna)
    {
        $pengguna->delete();
        return back()->with('success', 'Pengguna berhasil dihapus');
    }
}
