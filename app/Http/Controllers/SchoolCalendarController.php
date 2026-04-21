<?php

namespace App\Http\Controllers;

use App\Models\SchoolCalendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolCalendarController extends Controller
{
    public function index(Request $request)
    {
        $year = $request->year ?? now()->year;
        $month = $request->month ?? now()->month;
        
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfMonth();
        $endDate = Carbon::createFromDate($year, $month, 1)->endOfMonth();
        
        $calendars = SchoolCalendar::whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->get()
            ->keyBy(function($item) {
                return $item->tanggal->format('Y-m-d');
            });

        return view('school_calendar.index', compact('calendars', 'year', 'month', 'startDate', 'endDate'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'tanggal' => 'required|date',
            'jam_masuk' => 'required',
            'is_libur' => 'nullable|boolean',
            'keterangan' => 'nullable|string'
        ]);

        SchoolCalendar::updateOrCreate(
            ['tanggal' => $request->tanggal],
            [
                'jam_masuk' => $request->jam_masuk,
                'is_libur' => $request->has('is_libur'),
                'keterangan' => $request->keterangan
            ]
        );

        return back()->with('success', 'Pengaturan tanggal berhasil disimpan.');
    }

    public function destroy($id)
    {
        SchoolCalendar::destroy($id);
        return back()->with('success', 'Pengaturan tanggal berhasil dihapus.');
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'year' => 'required|integer',
            'month' => 'required|integer',
            'jam_masuk' => 'required',
            'libur_days' => 'nullable|array'
        ]);

        $year = $request->year;
        $month = $request->month;
        $jamMasuk = $request->jam_masuk;
        $liburDays = $request->libur_days ?? [];

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = Carbon::create($year, $month, 1)->endOfMonth();

        DB::transaction(function() use ($startDate, $endDate, $jamMasuk, $liburDays) {
            $current = $startDate->copy();
            while ($current->lte($endDate)) {
                $isLibur = in_array($current->dayOfWeek, $liburDays);
                
                SchoolCalendar::updateOrCreate(
                    ['tanggal' => $current->toDateString()],
                    [
                        'jam_masuk' => $jamMasuk,
                        'is_libur' => $isLibur,
                        'keterangan' => $isLibur ? 'Libur Rutin' : null
                    ]
                );
                $current->addDay();
            }
        });

        return back()->with('success', "Berhasil men-generate kalender untuk bulan " . $startDate->translatedFormat('F Y'));
    }
}
