<?php

namespace App\Http\Middleware;

use App\Models\JadwalKerja;
use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Alert;

class JadwalChecking
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            if (Auth::user()->role != 'Owner') {
                $timezone = new \DateTimeZone('Asia/Makassar');
                $jadwalToday = JadwalKerja::with('Shift', 'User')->where('id', Auth::user()->id)
                    ->where('tanggal', Carbon::today($timezone))
                    ->first();

                if (!$jadwalToday) {
                    Alert::warning('warning', 'Tidak Ada Jadwal Hari Ini!');
                    return redirect()->route('jadwal-user.index');
                }

                if ($jadwalToday->jam_masuk == null) {
                    Alert::warning('warning', 'Anda Belum Melakukan Absensi');
                    return redirect()->route('jadwal-user.index');
                }
            }
        }

        return $next($request);
    }
}
