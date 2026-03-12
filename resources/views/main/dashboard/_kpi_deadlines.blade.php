{{-- KPI List for this month --}}
<div class="kpi-deadline-card">
    <div class="kpi-deadline-header">
        <h3><i class="fa-solid fa-bullseye"></i> KPI Tháng {{ \Carbon\Carbon::parse($month . '-01')->format('m/Y') }}</h3>
        <span class="kpi-deadline-count">{{ $kpiDeadlines->count() }} KPI</span>
    </div>
    <div class="kpi-deadline-list">
        @if($kpiDeadlines->count() > 0)
            @foreach($kpiDeadlines as $kpi)
            @php
                $deadlineDate = $kpi->deadline_time ? \Carbon\Carbon::parse($kpi->deadline_time) : null;
                $isOverdue = $deadlineDate && $deadlineDate->isPast() && $kpi->trang_thai !== 'Hoàn Thành';
                $statusClass = match($kpi->trang_thai) {
                    'Hoàn Thành' => 'status-done',
                    'Chưa Hoàn Thành' => 'status-pending',
                    default => 'status-default',
                };
                $danhGiaClass = match($kpi->danh_gia) {
                    'Đạt' => 'eval-pass',
                    'Không Đạt' => 'eval-fail',
                    'Xuất Sắc' => 'eval-excellent',
                    default => 'eval-none',
                };
            @endphp
            <div class="kpi-item {{ $isOverdue ? 'overdue' : '' }}">
                <div class="kpi-item-left">
                    <div class="kpi-item-title">{{ $kpi->tieu_de }}</div>
                    <div class="kpi-item-meta">
                        <span class="kpi-item-user"><i class="fa-solid fa-user"></i> {{ $kpi->TenNV ?? 'N/A' }}</span>
                        @if($deadlineDate)
                        <span class="kpi-item-deadline {{ $isOverdue ? 'text-overdue' : '' }}">
                            <i class="fa-solid fa-clock"></i> {{ $deadlineDate->format('d/m/Y') }}
                        </span>
                        @endif
                    </div>
                </div>
                <div class="kpi-item-right">
                    <span class="kpi-status-badge {{ $statusClass }}">{{ $kpi->trang_thai ?? 'Chưa Báo Cáo' }}</span>
                    @if($kpi->danh_gia)
                    <span class="kpi-eval-badge {{ $danhGiaClass }}">{{ $kpi->danh_gia }}</span>
                    @endif
                </div>
            </div>
            @endforeach
        @else
        <div class="empty-state">
            <i class="fa-solid fa-clipboard-check"></i>
            <p>Không có KPI nào trong tháng này</p>
        </div>
        @endif
    </div>
</div>
