@extends('main.layouts.app')
@section('title', 'Hóa Đơn Bán Giống')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="{{ asset('css/main/month-picker.css') }}">
<style>
    .si-page { padding: 10px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

    /* Header */
    .si-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px; flex-wrap: wrap; gap: 12px; }
    .si-header h2 { margin: 0; font-size: 22px; color: #1e293b; }



    /* Summary Cards */
    .si-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 18px; }
    @media (max-width: 900px) { .si-cards { grid-template-columns: repeat(2, 1fr); } }
    .si-card {
        background: linear-gradient(135deg, var(--c1), var(--c2));
        border-radius: 10px; padding: 14px 16px; color: white;
        box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }
    .si-card-label { font-size: 11px; font-weight: 700; opacity: 0.85; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; }
    .si-card-val { font-size: 20px; font-weight: 800; }
    .si-card-sub { font-size: 11px; opacity: 0.7; margin-top: 3px; }

    /* Controls */
    .si-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px; }
    .si-controls-left { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; }
    .si-controls-left select { padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; }
    .si-search {
        padding: 7px 14px; border: 1px solid #d1d5db; border-radius: 8px;
        font-size: 13px; width: 280px; outline: none;
    }
    .si-search:focus { border-color: #8b5cf6; box-shadow: 0 0 0 2px rgba(139,92,246,0.15); }

    /* Table */
    .si-table-wrap { overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0; }
    .si-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    .si-table thead th {
        background: #1e1b4b; color: white; padding: 10px 10px;
        font-weight: 600; text-align: left; white-space: nowrap;
        border-right: 1px solid #312e81; position: sticky; top: 0; font-size: 15px;
    }
    .si-table thead th:last-child { border-right: none; text-align: center; }
    .si-table tbody tr { border-bottom: 1px solid #e2e8f0; }
    .si-table tbody tr:hover { background: #faf5ff; }
    .si-table tbody tr:nth-child(even) { background: #fafbfc; }
    .si-table tbody tr:nth-child(even):hover { background: #f5f0ff; }
    .si-table td { font-size: 15px; padding: 10px 10px; vertical-align: middle; }

    /* Col widths */
    .si-col-id { width: 80px; text-align: center; }
    .si-col-id .madh { font-weight: 700; color: #6d28d9; font-size: 14px; }
    .si-col-id .ngay { color: #64748b; font-size: 12px; margin-top: 2px; }
    .si-col-kh { min-width: 180px; }
    .si-col-kh .kh-name { font-weight: 700; color: #1e293b; }
    .si-col-kh .kh-phone { color: #475569; }
    .si-col-kh .kh-loc { color: #64748b; font-size: 12px; margin-top: 2px; }
    .si-col-nv { width: 90px; color: #dc2626; font-weight: 700; font-size: 13px; }
    .si-col-sl { width: 100px; text-align: right; }
    .si-col-sl .sl-val { font-size: 16px; font-weight: 700; color: #1e293b; }
    .si-col-money { width: 120px; text-align: right; }
    .si-col-money .money-main { font-size: 16px; font-weight: 800; color: #dc2626; }
    .si-col-money .money-label { font-size: 10px; color: #94a3b8; }
    .si-col-ds { width: 120px; text-align: right; }
    .si-col-ds .ds-val { font-size: 17px; font-weight: 800; color: #16a34a; }
    .si-col-note { min-width: 120px; color: #475569; font-size: 12px; }
    .si-col-action { width: 60px; text-align: center; }

    /* Delete button */
    .si-btn-del {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 28px; border-radius: 4px; border: none;
        cursor: pointer; font-size: 15px; transition: all 0.15s;
        background: #ef4444; color: white;
    }
    .si-btn-del:hover { transform: scale(1.15); background: #dc2626; }

    /* Pagination */
    .si-pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; flex-wrap: wrap; gap: 10px; }
    .si-pg-info { font-size: 13px; color: #64748b; }
    .si-pg-btns { display: flex; gap: 4px; }
    .si-pg-btn { padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; background: white; font-size: 12px; cursor: pointer; }
    .si-pg-btn:hover { background: #f1f5f9; }
    .si-pg-btn.active { background: #8b5cf6; color: white; border-color: #8b5cf6; }

    /* Empty */
    .si-empty { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .si-empty .empty-icon { font-size: 48px; margin-bottom: 12px; }
</style>
@endpush

@section('content')
<div class="si-page">
    {{-- Header with month picker on the right --}}
    <div class="si-header">
        <h2><i class="fa-solid fa-file-invoice-dollar" style="color:#8b5cf6;"></i> Hóa Đơn Bán Giống</h2>
        <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size:13px; color:#64748b;">
                Tổng: <strong style="color:#1e293b;">{{ $totalCount }}</strong> hóa đơn
            </span>
            @php
                $siThang = (int) \Carbon\Carbon::parse($month . '-01')->format('m');
                $siNam   = (int) \Carbon\Carbon::parse($month . '-01')->format('Y');
            @endphp
            @include('main.components.month-picker', ['month' => $siThang, 'year' => $siNam])
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="si-cards">
        <div class="si-card" style="--c1:#8b5cf6; --c2:#6d28d9;">
            <div class="si-card-label">Số Hóa Đơn</div>
            <div class="si-card-val">{{ number_format($totalCount) }}</div>
            <div class="si-card-sub">hóa đơn thanh toán</div>
        </div>
        <div class="si-card" style="--c1:#3b82f6; --c2:#1d4ed8;">
            <div class="si-card-label">Thực Nhận</div>
            <div class="si-card-val">{{ number_format($totalThucNhan) }}</div>
            <div class="si-card-sub">tổng tiền thu vào</div>
        </div>
        <div class="si-card" style="--c1:#f59e0b; --c2:#d97706;">
            <div class="si-card-label">Chuyển Trả Trại</div>
            <div class="si-card-val">{{ number_format($totalChuyenTra) }}</div>
            <div class="si-card-sub">trả về trại giống</div>
        </div>
        <div class="si-card" style="--c1:#10b981; --c2:#059669;">
            <div class="si-card-label">Doanh Số</div>
            <div class="si-card-val">{{ number_format($totalDoanhSo) }}</div>
            <div class="si-card-sub">= thực nhận - chuyển trả</div>
        </div>
    </div>

    {{-- Controls --}}
    <div class="si-controls">
        <div class="si-controls-left">
            Hiển thị
            <select id="siPageSize" onchange="changeSiPageSize()">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50" selected>50</option>
                <option value="100">100</option>
            </select>
            dòng
        </div>
        <div>
            <label style="font-size:13px; color:#64748b;">Lọc nhanh:</label>
            <input type="text" class="si-search" id="siSearch" placeholder="Tìm mã đơn, khách hàng, SĐT, tỉnh..." oninput="filterSiTable()">
        </div>
    </div>

    {{-- Table --}}
    <div class="si-table-wrap">
        <table class="si-table" id="siTable">
            <thead>
                <tr>
                    <th>Mã Đơn</th>
                    <th>Khách Hàng</th>
                    <th>Nhân Viên</th>
                    <th style="text-align:right;">Số Lượng</th>
                    <th style="text-align:right;">Thực Nhận</th>
                    <th style="text-align:right;">Chuyển Trả</th>
                    <th style="text-align:right;">Doanh Số</th>
                    <th>Ghi Chú</th>
                    @can('Admin')
                    <th></th>
                    @endcan
                </tr>
            </thead>
            <tbody>
                @foreach($invoices as $inv)
                @php
                    $ngay = $inv->NgayThanhToan ? \Carbon\Carbon::parse($inv->NgayThanhToan)->format('d/m/y') : '—';
                    $loc  = trim(implode(', ', array_filter([$inv->Tinh])));
                @endphp
                <tr class="si-row"
                    data-search="{{ strtolower(($inv->MaDH ?? '') . ' ' . ($inv->TenKH ?? '') . ' ' . ($inv->SoDienThoai ?? '') . ' ' . ($inv->Tinh ?? '') . ' ' . ($inv->TenNV ?? '')) }}">
                    <td class="si-col-id">
                        <div class="madh">{{ $inv->MaDH }}</div>
                        <div class="ngay">{{ $ngay }}</div>
                    </td>
                    <td class="si-col-kh">
                        <div class="kh-name">{{ $inv->TenKH ?? '—' }}</div>
                        <div class="kh-phone">{{ $inv->SoDienThoai ?? '' }}</div>
                        @if($loc)<div class="kh-loc">{{ $loc }}</div>@endif
                    </td>
                    <td class="si-col-nv">{{ $inv->TenNV ?? '—' }}</td>
                    <td class="si-col-sl">
                        <div class="sl-val">{{ number_format($inv->SoLuongNhan ?? 0, 0, ',', ',') }}</div>
                    </td>
                    <td class="si-col-money">
                        <div class="money-label">Thực Nhận</div>
                        <div class="money-main">{{ number_format($inv->ThucNhan ?? 0, 0, ',', ',') }}</div>
                    </td>
                    <td class="si-col-money">
                        <div class="money-label">Chuyển Trả</div>
                        <div class="money-main" style="color:#d97706;">{{ number_format($inv->ChuyenTraTrai ?? 0, 0, ',', ',') }}</div>
                    </td>
                    <td class="si-col-ds">
                        <div class="ds-val">{{ number_format($inv->DoanhSo ?? 0, 0, ',', ',') }}</div>
                    </td>
                    <td class="si-col-note">{{ $inv->GhiChu ?? '' }}</td>
                    @can('Admin')
                    <td class="si-col-action">
                        <form method="POST" action="{{ route('accounting.seedInvoices.destroy', $inv->id) }}"
                              onsubmit="return confirm('Xóa hóa đơn {{ $inv->MaDH }}?')" style="display:contents;">
                            @csrf @method('DELETE')
                            <button type="submit" class="si-btn-del" title="Xóa">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                    </td>
                    @endcan
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if(count($invoices) > 0)
    <div class="si-pagination" id="siPagination">
        <span class="si-pg-info" id="siPageInfo"></span>
        <div class="si-pg-btns" id="siPageButtons"></div>
    </div>
    @endif

    @if(count($invoices) === 0)
    <div class="si-empty">
        <div class="empty-icon">🧾</div>
        <h3>Không có hóa đơn bán giống</h3>
        <p>Chưa có dữ liệu trong khoảng thời gian này</p>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="{{ asset('js/main/month-picker.js') }}"></script>
<script>
    // ===== MONTH PICKER CALLBACK =====
    window.onMonthPickerSelect = function(month, year) {
        var val = year + '-' + String(month).padStart(2, '0');
        var url = new URL(window.location.href);
        url.searchParams.set('month', val);
        window.location.href = url.toString();
    };

    // ===== SEARCH =====
    let siAllRows = [], siFilteredRows = [], siCurrentPage = 1, siPageSize = 50;

    document.addEventListener('DOMContentLoaded', function() {
        siAllRows = Array.from(document.querySelectorAll('#siTable tbody tr.si-row'));
        siFilteredRows = [...siAllRows];
        renderSiPage();
    });

    function filterSiTable() {
        const q = document.getElementById('siSearch').value.toLowerCase().trim();
        siFilteredRows = siAllRows.filter(r => !q || r.dataset.search.includes(q));
        siCurrentPage = 1;
        renderSiPage();
    }

    function changeSiPageSize() {
        siPageSize = parseInt(document.getElementById('siPageSize').value);
        siCurrentPage = 1;
        renderSiPage();
    }

    // ===== PAGINATION =====
    function renderSiPage() {
        const total = siFilteredRows.length;
        const totalPages = Math.ceil(total / siPageSize) || 1;
        if (siCurrentPage > totalPages) siCurrentPage = totalPages;
        const start = (siCurrentPage - 1) * siPageSize;
        const end   = Math.min(start + siPageSize, total);

        siAllRows.forEach(r => r.style.display = 'none');
        for (let i = start; i < end; i++) siFilteredRows[i].style.display = '';

        updateFilteredTotals();

        const info = document.getElementById('siPageInfo');
        if (info) info.textContent = total > 0 ? `Hiển thị ${start+1}–${end} / ${total} hóa đơn` : '';

        const bc = document.getElementById('siPageButtons');
        if (!bc) return;
        bc.innerHTML = '';
        const pb = (t, d, a, c) => {
            const b = document.createElement('button');
            b.className = 'si-pg-btn' + (a ? ' active' : '');
            b.textContent = t; b.disabled = d; b.onclick = c;
            bc.appendChild(b);
        };
        pb('‹', siCurrentPage === 1, false, () => { siCurrentPage--; renderSiPage(); });
        let sp = Math.max(1, siCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
        if (ep - sp < 4) sp = Math.max(1, ep - 4);
        for (let i = sp; i <= ep; i++) pb(i, false, i === siCurrentPage, () => { siCurrentPage = i; renderSiPage(); });
        pb('›', siCurrentPage === totalPages, false, () => { siCurrentPage++; renderSiPage(); });
    }

    function updateFilteredTotals() {
        let thucNhan = 0, chuyenTra = 0, doanhSo = 0;
        siFilteredRows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const parseNum = (el) => parseInt((el?.textContent || '0').replace(/[^0-9-]/g,'')) || 0;
            thucNhan  += parseNum(cells[4]?.querySelector('.money-main'));
            chuyenTra += parseNum(cells[5]?.querySelector('.money-main'));
            doanhSo   += parseNum(cells[6]?.querySelector('.ds-val'));
        });

        const fmtCard = (n) => n.toLocaleString('vi-VN');
        const cards = document.querySelectorAll('.si-card-val');
        if (cards.length >= 4) {
            cards[0].textContent = siFilteredRows.length.toLocaleString('vi-VN');
            cards[1].textContent = fmtCard(thucNhan);
            cards[2].textContent = fmtCard(chuyenTra);
            cards[3].textContent = fmtCard(doanhSo);
        }
    }
</script>
@endpush
