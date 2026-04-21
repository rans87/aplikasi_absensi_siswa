<?php

namespace App\Http\Controllers;

use App\Models\Assessment;
use App\Models\AssessmentCategory;
use App\Models\AssessmentDetail;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssessmentController extends Controller
{
    /**
     * Menampilkan daftar siswa untuk diberikan penilaian.
     * Fungsi ini mengambil data siswa hasil sinkronisasi API dan mendukung pencarian.
     */
    public function index(Request $request)
    {
        $search = $request->search;
        // Penilai melihat semua siswa dari tabel Siswa
        $users = \App\Models\Siswa::when($search, function($q) use ($search) {
                $q->where('nama', 'like', "%$search%")
                  ->orWhere('nis', 'like', "%$search%");
            })
            ->paginate(12);

        // Get progress for current month
        $currentMonth = Carbon::now()->format('F Y');
        $totalSiswa = \App\Models\Siswa::count();
        $assessedThisMonth = Assessment::where('period', 'like', "%" . Carbon::now()->year . "%") 
            ->distinct('evaluatee_id')
            ->count();
        
        $progress = $totalSiswa > 0 ? round(($assessedThisMonth / $totalSiswa) * 100) : 0;

        return view('assessments.index', compact('users', 'progress', 'assessedThisMonth', 'totalSiswa'));
    }

    /**
     * Menampilkan halaman formulir penilaian untuk siswa tertentu.
     * Mengambil data siswa dan kategori penilaian yang aktif (tiap kategori muncul sebagai rating bintang).
     */
    public function create($id)
    {
        $user = \App\Models\Siswa::findOrFail($id);
        $categories = AssessmentCategory::where('is_active', true)->get();
        
        return view('assessments.create', compact('user', 'categories'));
    }

    /**
     * Menyimpan data penilaian ke dalam database.
     * Menggunakan DB Transaction untuk memastikan data induk (assessment) 
     * dan data detail (skor per kategori) tersimpan secara utuh.
     */
    public function store(Request $request)
    {
        $request->validate([
            'evaluatee_id' => 'required|exists:siswa,id',
            'scores' => 'required|array',
            'scores.*' => 'required|numeric|min:1|max:5',
            'period' => 'required|string',
            'general_notes' => 'nullable|string',
        ]);

        try {
            DB::transaction(function() use ($request) {
                // Deteksi ID Penilai dari guard yang aktif
                $evaluatorId = null;
                if (Auth::guard('web')->check()) {
                    $evaluatorId = Auth::guard('web')->id();
                } elseif (Auth::guard('guru')->check()) {
                    $evaluatorId = Auth::guard('guru')->id();
                } else {
                    $evaluatorId = Auth::id() ?: 1; // Fallback ke default atau ID 1
                }
                
                $assessment = Assessment::create([
                    'evaluator_id' => $evaluatorId,
                    'evaluatee_id' => $request->evaluatee_id,
                    'assessment_date' => now(),
                    'period' => $request->period,
                    'general_notes' => $request->general_notes,
                ]);

                foreach ($request->scores as $categoryId => $score) {
                    AssessmentDetail::create([
                        'assessment_id' => $assessment->id,
                        'category_id' => $categoryId,
                        'score' => $score,
                    ]);
                }
            });

            return redirect()->route('assessments.index')->with('success', 'Evaluasi berhasil disimpan!');
        } catch (\Exception $e) {
            \Log::error("Assessment Store Error: " . $e->getMessage());
            return back()->with('error', 'Gagal menyimpan penilaian: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Menampilkan laporan performa individu siswa.
     * Dilengkapi dengan grafik radar (radar chart) dan riwayat penilaian terakhir.
     */
    public function report($id)
    {
        $user = Siswa::findOrFail($id);
        
        // Get latest assessment
        $latestAssessment = Assessment::with('details.category')
            ->where('evaluatee_id', $id)
            ->latest()
            ->first();

        // Get history for timeline
        $history = Assessment::where('evaluatee_id', $id)
            ->latest()
            ->take(5)
            ->get();

        return view('assessments.report', compact('user', 'latestAssessment', 'history'));
    }

    /**
     * Menampilkan rekapitulasi seluruh laporan penilaian siswa.
     * Dilengkapi fitur filter berdasarkan nama/NIS, kelas, dan periode penilaian.
     */
    public function allReports(Request $request)
    {
        $search = $request->search;
        $filterKelas = $request->kelas_id;
        $evaluatorId = $request->evaluator_id;
        $performance = $request->performance;
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        
        $siswa = Siswa::whereHas('assessments')
            ->when($search, function($q) use ($search) {
                $q->where(function($sq) use ($search) {
                    $sq->where('nama', 'like', "%$search%")
                      ->orWhere('nis', 'like', "%$search%");
                });
            })
            ->when($filterKelas, function($q) use ($filterKelas) {
                $q->whereHas('anggotaKelas', function($ak) use ($filterKelas) {
                    $ak->where('rombongan_belajar_id', $filterKelas);
                });
            })
            ->when($evaluatorId, function($q) use ($evaluatorId) {
                $q->whereHas('assessments', function($as) use ($evaluatorId) {
                    $as->where('evaluator_id', $evaluatorId);
                });
            })
            ->when($startDate, function($q) use ($startDate) {
                $q->whereHas('assessments', function($as) use ($startDate) {
                    $as->whereDate('assessment_date', '>=', $startDate);
                });
            })
            ->when($endDate, function($q) use ($endDate) {
                $q->whereHas('assessments', function($as) use ($endDate) {
                    $as->whereDate('assessment_date', '<=', $endDate);
                });
            })
            ->when($performance, function($q) use ($performance) {
                // Filter by latest assessment's average score
                $q->whereIn('id', function($sub) use ($performance) {
                    $sub->select('evaluatee_id')
                        ->from('assessments')
                        ->whereIn('id', function($inner) {
                            $inner->select(DB::raw('MAX(id)'))
                                  ->from('assessments')
                                  ->groupBy('evaluatee_id');
                        });
                    
                    if ($performance == 'excellent') {
                        $sub->whereIn('id', function($dSub) {
                            $dSub->select('assessment_id')
                                 ->from('assessment_details')
                                 ->groupBy('assessment_id')
                                 ->havingRaw('AVG(score) >= 4');
                        });
                    } elseif ($performance == 'good') {
                        $sub->whereIn('id', function($dSub) {
                            $dSub->select('assessment_id')
                                 ->from('assessment_details')
                                 ->groupBy('assessment_id')
                                 ->havingRaw('AVG(score) >= 3 AND AVG(score) < 4');
                        });
                    } elseif ($performance == 'low') {
                        $sub->whereIn('id', function($dSub) {
                            $dSub->select('assessment_id')
                                 ->from('assessment_details')
                                 ->groupBy('assessment_id')
                                 ->havingRaw('AVG(score) < 3');
                        });
                    }
                });
            })
            ->with(['assessments' => function($q) {
                $q->latest();
            }, 'assessments.details', 'currentKelas.rombonganBelajar'])
            ->paginate(15);

        // Get options for filters
        $rombels = \App\Models\RombonganBelajar::orderBy('nama_kelas')->get();
        // Get all instructors who have performed evaluations
        $gurus = \App\Models\Guru::whereIn('id', Assessment::distinct()->pluck('evaluator_id'))->get();

        return view('assessments.all_reports', compact('siswa', 'rombels', 'gurus'));
    }

    // --- CATEGORY MANAGEMENT (Admin Only) ---

    public function indexCategory()
    {
        $categories = AssessmentCategory::all();
        return view('assessments.categories.index', compact('categories'));
    }

    public function storeCategory(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string',
            'description' => 'nullable|string',
        ]);

        AssessmentCategory::create($request->all());
        return back()->with('success', 'Kategori penilaian berhasil ditambahkan');
    }

    public function updateCategory(Request $request, $id)
    {
        $category = AssessmentCategory::findOrFail($id);
        $category->update($request->all());
        return back()->with('success', 'Kategori penilaian berhasil diperbarui');
    }

    public function destroyCategory($id)
    {
        AssessmentCategory::destroy($id);
        return back()->with('success', 'Kategori penilaian berhasil dihapus');
    }
}
