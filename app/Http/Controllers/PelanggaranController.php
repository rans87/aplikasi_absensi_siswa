<?php

namespace App\Http\Controllers;

use App\Models\Pelanggaran;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PelanggaranController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $pelanggarans = Pelanggaran::with(['siswa', 'guru'])
            ->when($search, function ($query) use ($search) {
                $query->where('nama_pelanggaran', 'like', "%{$search}%")
                    ->orWhereHas('siswa', function ($q) use ($search) {
                        $q->where('nama', 'like', "%{$search}%")
                          ->orWhere('nis', 'like', "%{$search}%");
                    });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('pelanggaran.index', compact('pelanggarans'));
    }

    public function create()
    {
        $siswa = Siswa::orderBy('nama')->get();
        return view('pelanggaran.create', compact('siswa'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'nama_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        Pelanggaran::create([
            'siswa_id' => $request->siswa_id,
            'guru_id' => Auth::guard('guru')->id() ?? null,
            'nama_pelanggaran' => $request->nama_pelanggaran,
            'poin' => $request->poin,
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('pelanggaran.index')->with('success', 'Pelanggaran berhasil dicatat!');
    }

    public function edit($id)
    {
        $pelanggaran = Pelanggaran::findOrFail($id);
        $siswa = Siswa::orderBy('nama')->get();
        return view('pelanggaran.edit', compact('pelanggaran', 'siswa'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'siswa_id' => 'required|exists:siswa,id',
            'nama_pelanggaran' => 'required|string|max:255',
            'poin' => 'required|integer|min:1',
            'keterangan' => 'nullable|string',
        ]);

        $pelanggaran = Pelanggaran::findOrFail($id);
        $pelanggaran->update($request->all());

        return redirect()->route('pelanggaran.index')->with('success', 'Data berhasil diperbarui!');
    }

    public function destroy($id)
    {
        Pelanggaran::destroy($id);
        return redirect()->back()->with('success', 'Data berhasil dihapus!');
    }

    public function show($id)
    {
        abort(404);
    }
}
