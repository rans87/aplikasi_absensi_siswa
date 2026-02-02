@extends('layouts.app')

@section('content')
    <style>
        .bg-custom-gradient {
            background: linear-gradient(135deg, #2c1cbaea 0%, #6a3cc0 100%);
        }

        .border-custom {
            border-color: #2c1cbaea;
        }
    </style>

    <div class="min-h-screen py-12 px-4">
        <div class="max-w-3xl mx-auto">
            <a href="{{ route('anggota-kelas.index') }}"
                class="inline-flex items-center text-slate-500 hover:text-indigo-600 mb-6 font-semibold transition-colors">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
                Batalkan Perubahan
            </a>

            <div class="bg-white rounded-[2.5rem] shadow-2xl overflow-hidden border border-slate-50">
                <div class="bg-custom-gradient p-10 text-white relative">
                    <h2 class="text-3xl font-black tracking-tight">Edit Anggota</h2>
                    <p class="opacity-80 mt-2 font-medium text-indigo-100">Memperbarui data penempatan kelas siswa.</p>
                    <div class="absolute right-10 bottom-0 opacity-10">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-24 w-24" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                        </svg>
                    </div>
                </div>

                <form action="{{ route('anggota-kelas.update', $anggotaKelas->id) }}" method="POST" class="p-10 space-y-8">
                    @csrf
                    @method('PUT')

                    <div class="space-y-2">
                        <label class="block text-sm font-bold text-slate-700 ml-1">Nama Siswa</label>
                        <select name="siswa_id"
                            class="w-full p-4 rounded-2xl border-2 border-slate-100 focus:border-custom outline-none bg-slate-50 font-bold text-slate-600">
                            @foreach($siswa as $s)
                                <option value="{{ $s->id }}" {{ $anggotaKelas->siswa_id == $s->id ? 'selected' : '' }}>
                                    {{ $s->nama }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 ml-1">Rombongan Belajar</label>
                            <select name="rombongan_belajar_id"
                                class="w-full p-4 rounded-2xl border-2 border-slate-100 focus:border-custom outline-none bg-slate-50 font-medium">
                                @foreach($rombel as $r)
                                    <option value="{{ $r->id }}" {{ $anggotaKelas->rombongan_belajar_id == $r->id ? 'selected' : '' }}>
                                        {{ $r->nama_rombel }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="block text-sm font-bold text-slate-700 ml-1">Tahun Ajar</label>
                            <select name="tahun_ajar_id"
                                class="w-full p-4 rounded-2xl border-2 border-slate-100 focus:border-custom outline-none bg-slate-50 font-medium">
                                @foreach($tahunAjar as $t)
                                    <option value="{{ $t->id }}" {{ $anggotaKelas->tahun_ajar_id == $t->id ? 'selected' : '' }}>
                                        {{ $t->tahun }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="pt-6">
                        <button type="submit"
                            class="w-full bg-custom-gradient text-white py-4 rounded-2xl font-black text-lg shadow-xl hover:shadow-indigo-300 transition-all">
                            Perbarui Data Anggota
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection