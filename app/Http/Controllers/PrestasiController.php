<?php

namespace App\Http\Controllers;

use App\Models\Prestasi;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PrestasiController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $prestasis = Prestasi::with(['siswa', 'guru'])
            ->when($search, function ($query) use ($search) {
                $query->where('nama_prestasi', 'like', "%{$search}%")
                    ->orWhereHas('siswa', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                          ->orWhere('nis', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('prestasi.index', compact('prestasis'));
    }

    public function create()
    {
        $siswa = Siswa::orderBy('nama')->get();
        return view('prestasi.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'nama_prestasi' => 'required|string|max:255',
            'poin' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        Prestasi::create([
            'siswa_id' => $request->siswa_id,
            'guru_id' => Auth::guard('guru')->id() ?? null,
            'nama_prestasi' => $request->nama_prestasi,
            'poin' => $request->poin,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('prestasi.index')->with('success', 'Prestasi berhasil dicatat!');
    }

    public function edit($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        $siswa = Siswa::orderBy('nama')->get();
        return view('prestasi.edit', compact('prestasi', 'siswa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'nama_prestasi' => 'required|string|max:255',
            'poin' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $prestasi = Prestasi::findOrFail($id);
        $prestasi->update($request->all());

        return redirect()->route('prestasi.index')->with('success', 'Prestasi berhasil diupdate!');
    }

    public function destroy($id)
    {
        $prestasi = Prestasi::findOrFail($id);
        $prestasi->delete();
        return redirect()->route('prestasi.index')->with('success', 'Prestasi berhasil dihapus!');
    }

    public function show($id)
    {
        abort(404);
    }
}
