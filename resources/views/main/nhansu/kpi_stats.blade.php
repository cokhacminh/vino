@extends('main.layouts.app')

@section('title', 'Thống Kê KPI')

@push('styles')
<link rel="stylesheet" href="{{ asset('css/main/month-picker.css') }}">
<style>
    .ks-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:20px; flex-wrap:wrap; gap:12px; }
    .ks-header h2 { margin:0; font-size:22px; font-weight:700; color:#1e293b; }
    .ks-filters { display:flex; gap:10px; align-items:center; flex-wrap:wrap; }
    .ks-search {
        padding:8px 14px 8px 36px;
        border:2px solid #e2e8f0;
        border-radius:10px;
        font-size:13px;
        background:white;
        width:220px;
        transition: border-color 0.2s;
    }
    .ks-search:focus { outline:none; border-color:#6d28d9; }
    .ks-search-wrap { position:relative; }
    .ks-search-wrap i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#94a3b8; font-size:13px; }



    /* Table */
    .ks-card { background:white; border-radius:6px; box-shadow:0 1px 3px rgba(0,0,0,0.06),0 6px 16px rgba(0,0,0,0.04); overflow-x:auto; }
    .ks-table { width:100%; border-collapse:collapse; }
    .ks-table thead th {
        padding:12px 14px; background:#f8fafc; font-size:11px; font-weight:700;
        color:#64748b; text-transform:uppercase; text-align:center;
        border-bottom:2px solid #e2e8f0; white-space:nowrap; position:sticky; top:0;
    }
    .ks-table tbody td {
        padding:10px 14px; border-bottom:1px solid #f1f5f9; font-size:13px;
        color:#334155; vertical-align:middle; text-align:center;
    }
    .ks-table tbody tr:hover { background:#f8fafc; }
    .ks-table .td-name { text-align:left; font-weight:600; white-space:nowrap; }
    .ks-table .td-dept { text-align:left; }

    .badge { display:inline-flex; align-items:center; gap:4px; padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; }

    /* Number cells */
    .num-cell { font-weight:700; font-size:14px; }
    .num-zero { color:#e2e8f0; }
    .num-total { color:#6d28d9; }
    .num-done { color:#16a34a; }
    .num-late { color:#dc2626; }
    .num-dat { color:#16a34a; }
    .num-kdat { color:#ea580c; }
    .num-vuot { color:#2563eb; }

    .ks-summary-row td { background:#f5f3ff !important; font-weight:700 !important; font-size:14px !important; }

    @media (max-width: 600px) {
        .ks-search { width:160px; }
    }
</style>
@endpush

@section('content')
<div>
    <div class="ks-header">
        <h2><i class="fa-solid fa-chart-bar" style="color:#6d28d9;"></i> Thống Kê KPI</h2>
        <div class="ks-filters">
            <div class="ks-search-wrap">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="ks-search" id="ksSearch" placeholder="Tìm nhân viên, phòng ban..."
                       value="{{ $search }}" onkeydown="if(event.key==='Enter') applySearch()">
            </div>
            @include('main.components.month-picker', ['month' => $thang, 'year' => $nam])
        </div>
    </div>

    <div class="ks-card">
        <table class="ks-table">
            <thead>
                <tr>
                    <th style="width:40px;">#</th>
                    <th style="text-align:left;">Nhân Viên</th>
                    <th style="text-align:left;">Phòng Ban</th>
                    <th style="text-align:left;">Chức Vụ</th>
                    <th><i class="fa-solid fa-list-check" style="color:#6d28d9;"></i> Tổng</th>
                    <th><i class="fa-solid fa-circle-check" style="color:#16a34a;"></i> Hoàn Thành</th>
                    <th><i class="fa-solid fa-clock" style="color:#dc2626;"></i> Trễ DL</th>
                    <th><i class="fa-solid fa-trophy" style="color:#16a34a;"></i> Đạt</th>
                    <th><i class="fa-solid fa-xmark" style="color:#ea580c;"></i> K.Đạt</th>
                    <th><i class="fa-solid fa-rocket" style="color:#2563eb;"></i> Vượt</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $sumTotal = 0; $sumHT = 0; $sumTre = 0; $sumDat = 0; $sumKDat = 0; $sumVuot = 0;
                @endphp
                @forelse($userStats as $idx => $s)
                @php
                    $sumTotal += $s['total'];
                    $sumHT += $s['hoan_thanh'];
                    $sumTre += $s['tre_deadline'];
                    $sumDat += $s['dat'];
                    $sumKDat += $s['khong_dat'];
                    $sumVuot += $s['vuot'];
                @endphp
                <tr>
                    <td style="color:#94a3b8; font-size:12px;">{{ $idx + 1 }}</td>
                    <td class="td-name">
                        <i class="fa-solid fa-user-circle" style="color:#6d28d9; margin-right:4px;"></i>
                        {{ $s['user']->name }}
                    </td>
                    <td class="td-dept">
                        @if($s['user']->TenPB)
                            <span class="badge" style="background:#ede9fe; color:#7c3aed;">{{ $s['user']->TenPB }}</span>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td class="td-dept">
                        @if($s['user']->TenCV)
                            <span class="badge" style="background:#dbeafe; color:#2563eb;">{{ $s['user']->TenCV }}</span>
                        @else
                            <span style="color:#cbd5e1;">—</span>
                        @endif
                    </td>
                    <td class="num-cell {{ $s['total'] ? 'num-total' : 'num-zero' }}">{{ $s['total'] }}</td>
                    <td class="num-cell {{ $s['hoan_thanh'] ? 'num-done' : 'num-zero' }}">{{ $s['hoan_thanh'] }}</td>
                    <td class="num-cell {{ $s['tre_deadline'] ? 'num-late' : 'num-zero' }}">{{ $s['tre_deadline'] }}</td>
                    <td class="num-cell {{ $s['dat'] ? 'num-dat' : 'num-zero' }}">{{ $s['dat'] }}</td>
                    <td class="num-cell {{ $s['khong_dat'] ? 'num-kdat' : 'num-zero' }}">{{ $s['khong_dat'] }}</td>
                    <td class="num-cell {{ $s['vuot'] ? 'num-vuot' : 'num-zero' }}">{{ $s['vuot'] }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center; padding:40px; color:#94a3b8;">
                        <i class="fa-solid fa-inbox" style="font-size:28px; display:block; margin-bottom:8px;"></i>
                        Không có dữ liệu
                    </td>
                </tr>
                @endforelse
                @if(count($userStats) > 0)
                <tr class="ks-summary-row">
                    <td></td>
                    <td class="td-name" style="color:#6d28d9;"><i class="fa-solid fa-calculator" style="margin-right:4px;"></i> TỔNG CỘNG</td>
                    <td></td>
                    <td></td>
                    <td class="num-cell num-total">{{ $sumTotal }}</td>
                    <td class="num-cell num-done">{{ $sumHT }}</td>
                    <td class="num-cell num-late">{{ $sumTre }}</td>
                    <td class="num-cell num-dat">{{ $sumDat }}</td>
                    <td class="num-cell num-kdat">{{ $sumKDat }}</td>
                    <td class="num-cell num-vuot">{{ $sumVuot }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>
</div>
@stop

@push('scripts')
<script src="{{ asset('js/main/month-picker.js') }}"></script>
<script>
    function applySearch() { applyFilters(); }

    window.onMonthPickerSelect = function(month, year) {
        applyFilters(month, year);
    };

    function applyFilters(month, year) {
        var m = month || {{ $thang }};
        var y = year  || {{ $nam }};
        var search = document.getElementById('ksSearch').value;
        var url = '{{ route("kpiStats") }}?thang=' + m + '&nam=' + y;
        if (search) url += '&search=' + encodeURIComponent(search);
        window.location.href = url;
    }
</script>
@endpush
