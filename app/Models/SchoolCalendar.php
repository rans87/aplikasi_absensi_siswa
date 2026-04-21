<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class SchoolCalendar extends Model
{

    protected $fillable = [
        'tanggal',
        'jam_masuk',
        'is_libur',
        'keterangan'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'is_libur' => 'boolean'
    ];

    /**
     * Menghitung jam masuk untuk tanggal tertentu
     */
    public static function getEntryTimeForDate($date)
    {
        $dateStr = Carbon::parse($date)->toDateString();
        $calendar = self::whereDate('tanggal', $dateStr)->first();

        if ($calendar) {
            return $calendar->is_libur ? null : $calendar->jam_masuk;
        }

        // Default: Sabtu & Minggu libur
        $dayOfWeek = Carbon::parse($dateStr)->dayOfWeek;
        if ($dayOfWeek == Carbon::SUNDAY || $dayOfWeek == Carbon::SATURDAY) {
            return null;
        }

        return '07:00:00'; // Default jam masuk
    }
}
