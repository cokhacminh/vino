@extends('main.layouts.app')
@section('title', 'Khách Mua Giống')

@push('styles')
<style>
    .kmg-page { padding: 10px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }
    .kmg-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 16px; flex-wrap: wrap; gap: 12px;
    }
    .kmg-header h2 { margin: 0; font-size: 22px; color: #1e293b; }

    .kmg-controls {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 14px; flex-wrap: wrap; gap: 10px;
    }
    .kmg-controls-left { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; }
    .kmg-controls-left select {
        padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px;
    }
    .kmg-search {
        padding: 7px 14px; border: 1px solid #d1d5db; border-radius: 8px;
        font-size: 13px; width: 300px; outline: none;
    }
    .kmg-search:focus { border-color: #3b82f6; box-shadow: 0 0 0 2px rgba(59,130,246,0.15); }

    .kmg-table-wrap { overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0; }
    .kmg-table {
        width: 100%; border-collapse: collapse; font-size: 15px;
    }
    .kmg-table thead th {
        background: #334155; color: white; padding: 12px 14px;
        font-weight: 600; text-align: left; white-space: nowrap;
        border-right: 1px solid #475569; font-size: 16px;
        cursor: pointer; user-select: none; transition: background 0.15s;
    }
    .kmg-table thead th:hover { background: #475569; }
    .kmg-table thead th:last-child { border-right: none; }
    .kmg-table thead th .sort-icon {
        display: inline-block; margin-left: 5px; font-size: 11px; opacity: 0.5;
        transition: opacity 0.15s;
    }
    .kmg-table thead th.sort-active .sort-icon { opacity: 1; }
    .kmg-table tbody tr { border-bottom: 1px solid #e2e8f0; }
    .kmg-table tbody tr:hover { background: #f8fafc; }
    .kmg-table tbody tr:nth-child(even) { background: #fafbfc; }
    .kmg-table tbody tr:nth-child(even):hover { background: #f0f4f8; }
    .kmg-table td { padding: 12px 14px; vertical-align: middle; font-size: 15px; }

    .kmg-table .col-stt { width: 50px; text-align: center; font-weight: 700; color: #64748b; }
    .kmg-table .col-kh { min-width: 160px; font-weight: 700; color: #1e293b; }
    .kmg-table .col-sdt { width: 130px; font-weight: 600; color: #475569; letter-spacing: 0.5px; }
    .kmg-table .col-dc { min-width: 200px; color: #475569; }
    .kmg-table .col-huyen { width: 140px; color: #1e293b; font-weight: 600; }
    .kmg-table .col-tinh { width: 140px; color: #1e293b; font-weight: 700; }
    .kmg-table .col-sl { width: 120px; text-align: center; font-size: 17px; font-weight: 800; color: #dc2626; }
    .kmg-table .col-nv { width: 140px; color: #dc2626; font-weight: 600; }

    .kmg-pagination {
        display: flex; justify-content: space-between; align-items: center;
        margin-top: 14px; flex-wrap: wrap; gap: 10px;
    }
    .kmg-pg-info { font-size: 13px; color: #64748b; }
    .kmg-pg-btns { display: flex; gap: 4px; }
    .kmg-pg-btn {
        padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px;
        background: white; font-size: 12px; cursor: pointer;
    }
    .kmg-pg-btn:hover { background: #f1f5f9; }
    .kmg-pg-btn.active { background: #3b82f6; color: white; border-color: #3b82f6; }

    .kmg-empty { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .kmg-empty .empty-icon { font-size: 48px; margin-bottom: 12px; }

    .kmg-stat {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 6px 14px; background: #f0fdf4; border: 1px solid #bbf7d0;
        border-radius: 8px; font-size: 14px; font-weight: 700; color: #166534;
    }
</style>
@endpush

@section('content')
<div class="kmg-page">
    <div class="kmg-header">
        <h2><i class="fa-solid fa-users" style="color:#22c55e;"></i> Khách Mua Giống</h2>
        <span class="kmg-stat">
            <i class="fa-solid fa-clipboard-list"></i> Tổng: {{ number_format($customers->count(), 0, ',', '.') }} đơn
        </span>
    </div>

    <div class="kmg-controls">
        <div class="kmg-controls-left">
            Hiển thị
            <select id="kmgPageSize" onchange="changeKmgPageSize()">
                <option value="20">20</option>
                <option value="50" selected>50</option>
                <option value="100">100</option>
                <option value="200">200</option>
            </select>
            dòng
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <form method="GET" action="{{ route('seedOrders.khachMuaGiong') }}" style="display:flex; gap:8px;">
                <input type="text" name="search" class="kmg-search" placeholder="Tìm khách hàng, SĐT, tỉnh, huyện, người bán..." value="{{ $search }}">
                <button type="submit" style="padding:7px 16px; border:none; border-radius:8px; background:#3b82f6; color:white; font-size:13px; font-weight:700; cursor:pointer;">
                    <i class="fa-solid fa-search"></i>
                </button>
                @if($search)
                <a href="{{ route('seedOrders.khachMuaGiong') }}" style="padding:7px 16px; border:none; border-radius:8px; background:#ef4444; color:white; font-size:13px; font-weight:700; cursor:pointer; text-decoration:none; display:inline-flex; align-items:center;">
                    <i class="fa-solid fa-xmark"></i>
                </a>
                @endif
            </form>
        </div>
    </div>

    <div class="kmg-table-wrap">
        <table class="kmg-table" id="kmgTable">
            <thead>
                <tr>
                    <th data-col="0" data-type="text" onclick="sortKmgTable(this)">Khách Hàng <span class="sort-icon">⇅</span></th>
                    <th data-col="1" data-type="text" onclick="sortKmgTable(this)">Số Điện Thoại <span class="sort-icon">⇅</span></th>
                    <th data-col="2" data-type="text" onclick="sortKmgTable(this)">Địa Chỉ <span class="sort-icon">⇅</span></th>
                    <th data-col="3" data-type="text" onclick="sortKmgTable(this)" style="width: 180px;">Huyện <span class="sort-icon">⇅</span></th>
                    <th data-col="4" data-type="text" onclick="sortKmgTable(this)"  style="text-align:center;">Tỉnh <span class="sort-icon">⇅</span></th>
                    <th style="text-align:center" data-col="5" data-type="number" onclick="sortKmgTable(this)">Số Lượng <span class="sort-icon">⇅</span></th>
                    <th data-col="6" data-type="text" onclick="sortKmgTable(this)"  style="text-align:center;">Người Bán <span class="sort-icon">⇅</span></th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $i => $c)
                @php
                    // Mask phone: 0905***888
                    $phone = $c->SoDienThoai ?? '';
                    if (strlen($phone) >= 7) {
                        $maskedPhone = substr($phone, 0, 4) . '***' . substr($phone, -3);
                    } else {
                        $maskedPhone = $phone;
                    }
                @endphp
                <tr class="kmg-row"
                    data-search="{{ strtolower(($c->TenKH ?? '') . ' ' . ($c->SoDienThoai ?? '') . ' ' . ($c->Tinh ?? '') . ' ' . ($c->Huyen ?? '') . ' ' . ($c->TenNV ?? '')) }}">
                    <td class="col-kh">{{ $c->TenKH ?? '—' }}</td>
                    <td class="col-sdt">{{ $maskedPhone }}</td>
                    <td class="col-dc">{{ $c->DiaChi ?? '—' }}</td>
                    <td class="col-huyen">{{ $c->Huyen ?? '—' }}</td>
                    <td class="col-tinh" style="text-align:center;">{{ $c->Tinh ?? '—' }}</td>
                    <td class="col-sl">{{ number_format($c->SoLuong ?? 0, 0, ',', ',') }}</td>
                    <td class="col-nv" style="text-align:center;">{{ $c->TenNV ?? '—' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="kmg-empty">
                        <div class="empty-icon">🌱</div>
                        <h3>Không có dữ liệu</h3>
                        <p>Không tìm thấy khách mua giống</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($customers->count() > 0)
    <div class="kmg-pagination" id="kmgPagination">
        <span class="kmg-pg-info" id="kmgPageInfo"></span>
        <div class="kmg-pg-btns" id="kmgPageButtons"></div>
    </div>
    @endif
</div>

<script>
    let kmgAllRows = [], kmgFilteredRows = [], kmgCurrentPage = 1, kmgPageSize = 50;

    document.addEventListener('DOMContentLoaded', function() {
        kmgAllRows = Array.from(document.querySelectorAll('#kmgTable tbody tr.kmg-row'));
        kmgFilteredRows = [...kmgAllRows];
        renderKmgPage();
    });

    function renderKmgPage() {
        const total = kmgFilteredRows.length;
        const totalPages = Math.ceil(total / kmgPageSize) || 1;
        if (kmgCurrentPage > totalPages) kmgCurrentPage = totalPages;
        const start = (kmgCurrentPage - 1) * kmgPageSize, end = Math.min(start + kmgPageSize, total);
        kmgAllRows.forEach(r => r.style.display = 'none');
        for (let i = start; i < end; i++) kmgFilteredRows[i].style.display = '';

        const info = document.getElementById('kmgPageInfo');
        if (info) info.textContent = total > 0 ? `Hiển thị ${start+1}–${end} / ${total} khách` : '';

        const bc = document.getElementById('kmgPageButtons');
        if (!bc) return;
        bc.innerHTML = '';
        const pb = (t, d, a, c) => {
            const b = document.createElement('button');
            b.className = 'kmg-pg-btn' + (a ? ' active' : '');
            b.textContent = t; b.disabled = d; b.onclick = c;
            bc.appendChild(b);
        };
        pb('‹', kmgCurrentPage === 1, false, () => { kmgCurrentPage--; renderKmgPage(); });
        let sp = Math.max(1, kmgCurrentPage - 2), ep = Math.min(totalPages, sp + 4);
        if (ep - sp < 4) sp = Math.max(1, ep - 4);
        for (let i = sp; i <= ep; i++) pb(i, false, i === kmgCurrentPage, () => { kmgCurrentPage = i; renderKmgPage(); });
        pb('›', kmgCurrentPage === totalPages, false, () => { kmgCurrentPage++; renderKmgPage(); });
    }

    function changeKmgPageSize() {
        kmgPageSize = parseInt(document.getElementById('kmgPageSize').value);
        kmgCurrentPage = 1;
        renderKmgPage();
    }

    // ========== SORT ==========
    let kmgSortCol = -1, kmgSortAsc = true;

    function sortKmgTable(th) {
        const col = parseInt(th.dataset.col);
        const type = th.dataset.type || 'text';

        // Toggle direction
        if (kmgSortCol === col) {
            kmgSortAsc = !kmgSortAsc;
        } else {
            kmgSortCol = col;
            kmgSortAsc = true;
        }

        // Update header visuals
        document.querySelectorAll('#kmgTable thead th').forEach(h => {
            h.classList.remove('sort-active');
            const icon = h.querySelector('.sort-icon');
            if (icon) icon.textContent = '⇅';
        });
        th.classList.add('sort-active');
        const icon = th.querySelector('.sort-icon');
        if (icon) icon.textContent = kmgSortAsc ? '↑' : '↓';

        // Sort rows
        const tbody = document.querySelector('#kmgTable tbody');
        kmgFilteredRows.sort((a, b) => {
            let va = a.cells[col]?.textContent.trim() || '';
            let vb = b.cells[col]?.textContent.trim() || '';

            if (type === 'number') {
                va = parseFloat(va.replace(/[,\.]/g, '')) || 0;
                vb = parseFloat(vb.replace(/[,\.]/g, '')) || 0;
                return kmgSortAsc ? va - vb : vb - va;
            }

            va = va.toLowerCase();
            vb = vb.toLowerCase();
            if (va < vb) return kmgSortAsc ? -1 : 1;
            if (va > vb) return kmgSortAsc ? 1 : -1;
            return 0;
        });

        // Re-append sorted rows to DOM
        kmgFilteredRows.forEach(r => tbody.appendChild(r));
        kmgAllRows = Array.from(document.querySelectorAll('#kmgTable tbody tr.kmg-row'));
        kmgFilteredRows = [...kmgAllRows];

        // Renumber STT
        kmgFilteredRows.forEach((r, i) => {
            const sttCell = r.querySelector('.col-stt');
            if (sttCell) sttCell.textContent = i + 1;
        });

        kmgCurrentPage = 1;
        renderKmgPage();
    }
</script>
@endsection
