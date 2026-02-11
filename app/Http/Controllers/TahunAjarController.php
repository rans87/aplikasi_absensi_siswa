<?php

namespace App\Http\Controllers;

use App\Models\TahunAjar;
use Illuminate\Http\Request;

class TahunAjarController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->search;

        $tahunAjar = TahunAjar::when($search, function ($query) use ($search) {
            $query->where('tahun', 'like', "%{$search}%");
        })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('tahun_ajar.index', compact('tahunAjar'));
    }

    public function create()
    {
        return view('tahun_ajar.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string|max:255|unique:tahun_ajar,tahun',
            'aktif' => 'nullable',
        ]);

        if ($request->has('aktif')) {
            TahunAjar::where('aktif', true)->update(['aktif' => false]);
        }

        TahunAjar::create([
            'tahun' => $request->tahun,
            'aktif' => $request->has('aktif'),
        ]);

        return redirect()->route('tahun_ajar.index')->with('success', 'Tahun ajaran berhasil ditambahkan!');
    }

    public function edit($id)
    {
        $tahun_ajar = TahunAjar::findOrFail($id);
        return view('tahun_ajar.edit', compact('tahun_ajar'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'tahun' => 'required|string|max:255|unique:tahun_ajar,tahun,' . $id,
            'aktif' => 'nullable',
        ]);

        $tahun_ajar = TahunAjar::findOrFail($id);

        if ($request->has('aktif')) {
            TahunAjar::where('aktif', true)->where('id', '!=', $id)->update(['aktif' => false]);
        }

        $tahun_ajar->update([
            'tahun' => $request->tahun,
            'aktif' => $request->has('aktif'),
        ]);

        return redirect()->route('tahun_ajar.index')->with('success', 'Tahun ajaran berhasil diperbarui!');
    }

    public function destroy($id)
    {
        $tahun_ajar = TahunAjar::findOrFail($id);
        $tahun_ajar->delete();
        return redirect()->route('tahun_ajar.index')->with('success', 'Tahun ajaran berhasil dihapus!');
    }

    public function show($id)
    {
        abort(404);
    }
}
