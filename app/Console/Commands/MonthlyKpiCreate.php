<?php

namespace App\Console\Commands;

use App\Models\Kpi;
use App\Models\KpiUser;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;

class MonthlyKpiCreate extends Command
{
    protected $signature = 'kpi:monthly-create';
    protected $description = 'Tạo kpi_users cho tháng mới cho các KPI Hàng Tháng';

    public function handle()
    {
        $thang = now()->month;
        $nam = now()->year;

        $kpis = Kpi::where('tan_suat', 'Hàng Tháng')
            ->where('nam', '<=', $nam)
            ->get();

        $count = 0;

        foreach ($kpis as $kpi) {
            // Determine target users
            if ($kpi->loai_ap_dung === 'Cá Nhân') {
                $targetUserIds = $kpi->target_user_id ? collect([$kpi->target_user_id]) : collect();
            } else {
                $targetUserIds = User::where('TinhTrang', 'Active')
                    ->where('MaCV', $kpi->MaCV)
                    ->pluck('id');
            }

            // Get existing for this month
            $existingUserIds = KpiUser::where('kpi_id', $kpi->id)
                ->where('thang', $thang)
                ->where('nam', $nam)
                ->pluck('user_id');

            $deadlineTime = Carbon::create($nam, $thang)->endOfMonth()->toDateString();

            // Add new users
            $newUserIds = $targetUserIds->diff($existingUserIds);
            foreach ($newUserIds as $userId) {
                KpiUser::create([
                    'kpi_id' => $kpi->id,
                    'user_id' => $userId,
                    'thang' => $thang,
                    'nam' => $nam,
                    'deadline_time' => $deadlineTime,
                    'trang_thai' => 'Chưa Báo Cáo',
                ]);
                $count++;
            }
        }

        $this->info("Đã tạo {$count} kpi_user mới cho tháng {$thang}/{$nam}.");
    }
}
