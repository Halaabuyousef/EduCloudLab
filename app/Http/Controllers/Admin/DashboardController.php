<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Supervisor;
use App\Models\Experiment;
use App\Models\Reservation;
use App\Models\Device;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // 🔹 الإحصائيات العامة
        $stats = [
            'users'                 => User::count(),
            'supervisors'           => Supervisor::count(),
            'experiments'           => Experiment::count(),
            'reservations_active'   => Reservation::whereIn('status', ['active', 'in_use', 'reserved'])->count(),

            'devices_maintenance'   => Device::where('status', 'maintenance')->count(),
            'experiments_in_use'    => Experiment::where('status', 'in_use')->count(),
            'experiments_available' => Experiment::where('status', 'available')->count(),
        ];

        // 🔹 توزيع حالات التجارب (Pie Chart)
        $expStatus = [
            'available'   => Experiment::where('status', 'available')->count(),
            'reserved'    => Experiment::where('status', 'reserved')->count(),
            'in_use'      => Experiment::where('status', 'in_use')->count(),
            'maintenance' => Experiment::where('status', 'maintenance')->count(),
        ];

        // 🔹 حجوزات آخر 7 أيام (Line Chart)
        $labels = [];
        $counts = [];
        for ($i = 6; $i >= 0; $i--) {
            $day = Carbon::today()->subDays($i);
            $labels[] = $day->format('d M');
            $counts[] = Reservation::whereDate('created_at', $day)->count();
        }
        $reservationsTrend = [
            'labels' => $labels,
            'counts' => $counts,
        ];

        // 🔹 آخر 8 حجوزات (Recent Table)
        $recentReservations = Reservation::with(['experiment:id,title', 'user:id,name'])
            ->latest()
            ->limit(8)
            ->get();

        // 🟢 لو طلب المستخدم JSON (مثلاً /admin/dashboard?json=1)
        if ($request->wantsJson() || $request->query('json')) {
            return response()->json([
                'stats'               => $stats,
                'expStatus'           => $expStatus,
                'reservationsTrend'   => $reservationsTrend,
                'recentReservations'  => $recentReservations,
            ]);
        }
        $experiments = Experiment::all();
        // 🟢 العرض العادي (Blade)
        return view('admin.dashboard', compact(
            'stats',
            'expStatus',
            'reservationsTrend',
            'recentReservations',
            'experiments'
        ));
    }
}
