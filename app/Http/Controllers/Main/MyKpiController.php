<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\KpiUser;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MyKpiController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();
        $cutoff = $today->copy()->subDays(14);

        // Get user's KPI records where:
        // 1. deadline_time >= today (not yet due)
        // 2. deadline_time >= (today - 14 days) AND deadline_time < today (past due within 14 days)
        // Order: not-yet-due first (ascending), then past-due
        $myKpis = KpiUser::where('user_id', $user->id)
            ->where('deadline_time', '>=', $cutoff->toDateString())
            ->with('kpi')
            ->orderByRaw("CASE WHEN deadline_time >= ? THEN 0 ELSE 1 END", [$today->toDateString()])
            ->orderBy('deadline_time', 'asc')
            ->get();

        // Stats by month
        $statsThang = (int) $request->get('stats_thang', now()->month);
        $statsNam = (int) $request->get('stats_nam', now()->year);

        $statsQuery = KpiUser::where('user_id', $user->id)
            ->where('thang', $statsThang)
            ->where('nam', $statsNam);

        $statsAll = (clone $statsQuery)->get();

        $totalKpi = $statsAll->count();
        $hoanThanh = $statsAll->filter(function ($ku) {
            return $ku->trang_thai === 'Hợp Lệ'
                && $ku->reported_at
                && $ku->deadline_time
                && Carbon::parse($ku->reported_at)->startOfDay()->lte(Carbon::parse($ku->deadline_time));
        })->count();
        $treDeadline = $statsAll->filter(function ($ku) {
            return $ku->trang_thai === 'Hợp Lệ'
                && $ku->reported_at
                && $ku->deadline_time
                && Carbon::parse($ku->reported_at)->startOfDay()->gt(Carbon::parse($ku->deadline_time));
        })->count();
        $datKpi = $statsAll->where('danh_gia', 'Đạt KPI')->count();
        $khongDat = $statsAll->where('danh_gia', 'Không Đạt')->count();
        $vuotKpi = $statsAll->where('danh_gia', 'Vượt KPI')->count();

        $stats = [
            'total' => $totalKpi,
            'hoan_thanh' => $hoanThanh,
            'tre_deadline' => $treDeadline,
            'dat' => $datKpi,
            'khong_dat' => $khongDat,
            'vuot' => $vuotKpi,
        ];

        return view('main.mykpi.index', compact('myKpis', 'stats', 'statsThang', 'statsNam'));
    }

    public function report(Request $request, $id)
    {
        $user = Auth::user();
        $kpiUser = KpiUser::where('id', $id)->where('user_id', $user->id)->firstOrFail();

        // Only allow report if status is Chưa Báo Cáo or Báo Cáo Lại
        if (!in_array($kpiUser->trang_thai, ['Chưa Báo Cáo', 'Báo Cáo Lại'])) {
            return back()->withErrors(['msg' => 'Bạn không thể báo cáo KPI này.']);
        }

        $request->validate([
            'bao_cao' => 'required|string',
            'hinh_anh' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
        ], [
            'bao_cao.required' => 'Vui lòng nhập nội dung báo cáo.',
            'hinh_anh.image' => 'File phải là hình ảnh.',
            'hinh_anh.max' => 'Ảnh không được vượt quá 5MB.',
        ]);

        $data = [
            'bao_cao' => $request->bao_cao,
            'trang_thai' => 'Đã Báo Cáo',
            'danh_gia' => null,
            'reported_at' => now(),
        ];

        if ($request->hasFile('hinh_anh')) {
            if ($kpiUser->hinh_anh) {
                Storage::disk('public')->delete('kpi/' . $kpiUser->hinh_anh);
            }
            $file = $request->file('hinh_anh');
            $filename = 'kpi_' . $kpiUser->id . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->storeAs('kpi', $filename, 'public');
            $data['hinh_anh'] = $filename;
        }

        $kpiUser->update($data);

        return back()->with('success', 'Báo cáo KPI thành công!');
    }

    public function statDetail(Request $request)
    {
        $user = Auth::user();
        $thang = (int) $request->get('thang', now()->month);
        $nam = (int) $request->get('nam', now()->year);
        $filter = $request->get('filter', 'total');

        $query = KpiUser::where('user_id', $user->id)
            ->where('thang', $thang)
            ->where('nam', $nam)
            ->with('kpi');

        $items = $query->get();

        // Apply filter
        $filtered = match ($filter) {
            'hoan_thanh' => $items->filter(fn($ku) =>
                $ku->trang_thai === 'Hợp Lệ'
                && $ku->reported_at && $ku->deadline_time
                && Carbon::parse($ku->reported_at)->startOfDay()->lte(Carbon::parse($ku->deadline_time))
            ),
            'tre_deadline' => $items->filter(fn($ku) =>
                $ku->trang_thai === 'Hợp Lệ'
                && $ku->reported_at && $ku->deadline_time
                && Carbon::parse($ku->reported_at)->startOfDay()->gt(Carbon::parse($ku->deadline_time))
            ),
            'dat' => $items->where('danh_gia', 'Đạt KPI'),
            'khong_dat' => $items->where('danh_gia', 'Không Đạt'),
            'vuot' => $items->where('danh_gia', 'Vượt KPI'),
            default => $items,
        };

        $result = $filtered->values()->map(function ($ku) {
            return [
                'id' => $ku->id,
                'tieu_de' => $ku->kpi->tieu_de ?? $ku->kpi->noi_dung ?? '—',
                'trang_thai' => $ku->trang_thai,
                'danh_gia' => $ku->danh_gia,
                'deadline_time' => $ku->deadline_time ? Carbon::parse($ku->deadline_time)->format('d/m/Y') : null,
                'reported_at' => $ku->reported_at ? Carbon::parse($ku->reported_at)->format('d/m/Y H:i') : null,
            ];
        });

        return response()->json($result);
    }
}
