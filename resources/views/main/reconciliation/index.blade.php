@extends('main.layouts.app')
@section('title', 'Đối Chiếu Đơn Hàng')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.rc-page{margin:0 auto}
.rc-toolbar{display:flex;align-items:center;gap:12px;margin-bottom:20px;flex-wrap:wrap}
.rc-toolbar input{padding:8px 14px;border:1.5px solid var(--border);border-radius:8px;font-size:14px;width:200px;background:#fff;cursor:pointer}
.rc-toolbar .btn-fetch{padding:8px 20px;background:linear-gradient(135deg,var(--primary),#818cf8);color:#fff;border:none;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;display:flex;align-items:center;gap:6px}
.rc-toolbar .btn-fetch:hover{opacity:.9}
.rc-toolbar .btn-fetch:disabled{opacity:.5;cursor:not-allowed}
.rc-stats{display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap}
.rc-stat{background:#fff;border:1px solid var(--border);border-radius:10px;padding:14px 20px;min-width:140px;box-shadow:var(--shadow-sm)}
.rc-stat .rc-stat-value{font-size:22px;font-weight:800;color:var(--text)}
.rc-stat .rc-stat-label{font-size:12px;color:var(--text-secondary);font-weight:500;margin-top:2px}
.rc-stat.green .rc-stat-value{color:#059669}
.rc-stat.red .rc-stat-value{color:#dc2626}
.rc-table-wrap{background:#fff;border:1px solid var(--border);border-radius:var(--radius);box-shadow:var(--shadow-sm);overflow-x:auto}
.rc-table{width:100%;border-collapse:collapse;font-size:13px}
.rc-table th{background:#f8fafc;color:var(--text-secondary);font-weight:600;font-size:11px;padding:10px 12px;text-align:left;border-bottom:2px solid var(--border);white-space:nowrap;text-transform:uppercase}
.rc-table td{padding:8px 12px;border-bottom:1px solid var(--border-light);vertical-align:middle}
.rc-table tr:hover td{background:#f8fafc}
.rc-table th.col-dc{background:#eef2ff;color:#4f46e5}
.rc-table td.col-dc{background:#f5f3ff}
.rc-table td.col-dc.ok{color:#059669;font-weight:700}
.rc-table td.col-dc.missing{color:#dc2626;font-weight:600;font-style:italic}
.rc-empty{text-align:center;color:var(--text-muted);padding:40px;font-size:14px}
.rc-loading{display:none;text-align:center;padding:30px;color:var(--text-secondary)}
.rc-loading i{margin-right:6px}
code.madh{color:#1e40af;background:#dbeafe;padding:2px 8px;border-radius:5px;font-size:12px}
.badge-trangThai{display:inline-block;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:600}
.badge-tc{background:#d1fae5;color:#059669}
.badge-dg{background:#dbeafe;color:#2563eb}
.badge-cg{background:#fef3c7;color:#d97706}
.badge-tb{background:#fee2e2;color:#dc2626}
.badge-hh{background:#fce7f3;color:#db2777}
.badge-other{background:#f1f5f9;color:#64748b}
.rc-stat.amber .rc-stat-value{color:#d97706}
.rc-stat.blue .rc-stat-value{color:#2563eb}
.rc-filter{display:flex;align-items:center;gap:12px;margin-bottom:16px;flex-wrap:wrap;padding:10px 16px;background:#fff;border:1px solid var(--border);border-radius:10px;box-shadow:var(--shadow-sm)}
.rc-filter label{font-size:12px;font-weight:600;color:var(--text-secondary)}
.rc-filter .rc-cb-group{display:flex;gap:10px;flex-wrap:wrap}
.rc-filter .rc-cb-item{display:flex;align-items:center;gap:4px;font-size:13px;cursor:pointer;user-select:none}
.rc-filter .rc-cb-item input{accent-color:var(--primary);cursor:pointer}
</style>
@endpush
@section('content')
<div class="rc-page">
    <div class="page-header"><h2><i class="fa-solid fa-scale-balanced"></i> ĐỐI CHIẾU ĐƠN HÀNG</h2></div>
    <div class="rc-toolbar">
        <input type="text" id="rcDate" autocomplete="off" readonly placeholder="Chọn ngày...">
        <button class="btn-fetch" id="btnFetch" onclick="fetchReconciliation()">
            <i class="fa-solid fa-magnifying-glass"></i> Đối Chiếu
        </button>
    </div>
    <div class="rc-stats" id="rcStats" style="display:none">
        <div class="rc-stat"><div class="rc-stat-value" id="rcTotal">0</div><div class="rc-stat-label">Tổng Đơn</div></div>
        <div class="rc-stat green"><div class="rc-stat-value" id="rcMatched">0</div><div class="rc-stat-label">Đã Chuyển</div></div>
        <div class="rc-stat red"><div class="rc-stat-value" id="rcMissing">0</div><div class="rc-stat-label">Chưa Chuyển</div></div>
        <div class="rc-stat amber"><div class="rc-stat-value" id="rcRevenueTS">0đ</div><div class="rc-stat-label">Tổng Doanh Thu Thuỷ Sản</div></div>
        <div class="rc-stat blue"><div class="rc-stat-value" id="rcRevenueDP">0đ</div><div class="rc-stat-label">Tổng Doanh Thu Đã Chuyển</div></div>
    </div>
    <div class="rc-filter" id="rcFilter" style="display:none">
        <label><i class="fa-solid fa-filter"></i> ĐVGH:</label>
        <div class="rc-cb-group" id="rcDvghGroup"></div>
    </div>
    <div class="rc-loading" id="rcLoading"><i class="fa-solid fa-spinner fa-spin"></i> Đang tải dữ liệu...</div>
    <div class="rc-table-wrap">
        <table class="rc-table">
            <thead>
                <tr>
                    <th>STT</th>
                    <th>Ngày</th>
                    <th>Mã Đơn</th>
                    <th>Tổng Tiền</th>
                    <th>Trạng Thái</th>
                    <th>ĐVGH</th>
                    <th class="col-dc">Đối Chiếu</th>
                    <th class="col-dc">Tổng Tiền (Local)</th>
                </tr>
            </thead>
            <tbody id="rcBody">
                <tr><td colspan="8" class="rc-empty">Chọn ngày và nhấn Đối Chiếu</td></tr>
            </tbody>
        </table>
    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let fp;
document.addEventListener('DOMContentLoaded', function() {
    fp = flatpickr('#rcDate', {
        dateFormat: 'd/m/Y', locale: 'vn', defaultDate: new Date(), allowInput: false
    });
});

function fmtN(n) { return Number(n || 0).toLocaleString('vi-VN'); }
function fmtDate(d) {
    if (!d) return '';
    const dt = new Date(d);
    return `${String(dt.getDate()).padStart(2,'0')}/${String(dt.getMonth()+1).padStart(2,'0')}/${dt.getFullYear()}`;
}
function parseMoney(s) {
    if (typeof s === 'number') return s;
    return parseInt((s || '0').replace(/[^\d]/g, '')) || 0;
}
function statusBadge(s) {
    if (!s) return '';
    if (s.includes('Thành Công')) return `<span class="badge-trangThai badge-tc">${s}</span>`;
    if (s.includes('Đang Giao')) return `<span class="badge-trangThai badge-dg">${s}</span>`;
    if (s.includes('Chưa Gửi')) return `<span class="badge-trangThai badge-cg">${s}</span>`;
    if (s.includes('Thất Bại')) return `<span class="badge-trangThai badge-tb">${s}</span>`;
    if (s.includes('Hoàn') || s.includes('Hủy')) return `<span class="badge-trangThai badge-hh">${s}</span>`;
    return `<span class="badge-trangThai badge-other">${s}</span>`;
}

let allData = [];

async function fetchReconciliation() {
    const dateVal = document.getElementById('rcDate').value;
    if (!dateVal) { alert('Vui lòng chọn ngày'); return; }

    const btn = document.getElementById('btnFetch');
    const loading = document.getElementById('rcLoading');
    const tbody = document.getElementById('rcBody');
    const stats = document.getElementById('rcStats');
    const filter = document.getElementById('rcFilter');

    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tải...';
    loading.style.display = '';
    tbody.innerHTML = '';
    stats.style.display = 'none';
    filter.style.display = 'none';
    allData = [];

    try {
        const res = await fetch('/reconciliation/fetch', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ ngay: dateVal }),
        });
        const result = await res.json();

        if (!result.success) {
            tbody.innerHTML = `<tr><td colspan="8" class="rc-empty">${result.message || 'Lỗi'}</td></tr>`;
            return;
        }

        allData = result.data || [];

        if (!allData.length) {
            tbody.innerHTML = '<tr><td colspan="8" class="rc-empty">Không có đơn hàng ngày này</td></tr>';
            return;
        }

        // Build DVGH checkboxes
        const dvghSet = [...new Set(allData.map(r => r.DVGH || 'Khác'))];
        const grp = document.getElementById('rcDvghGroup');
        grp.innerHTML = dvghSet.map(dv =>
            `<label class="rc-cb-item"><input type="checkbox" value="${dv}" checked onchange="applyFilter()"> ${dv}</label>`
        ).join('');
        filter.style.display = 'flex';

        applyFilter();

    } catch (e) {
        tbody.innerHTML = `<tr><td colspan="8" class="rc-empty">Lỗi kết nối: ${e.message}</td></tr>`;
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-magnifying-glass"></i> Đối Chiếu';
        loading.style.display = 'none';
    }
}

function applyFilter() {
    const checked = [...document.querySelectorAll('#rcDvghGroup input:checked')].map(c => c.value);
    const filtered = allData.filter(r => checked.includes(r.DVGH || 'Khác'));

    const total = filtered.length;
    const matched = filtered.filter(r => r.doiChieu === 'ok').length;
    const missing = total - matched;
    const revenueTS = filtered.reduce((s, r) => s + parseMoney(r.TongTien), 0);
    const revenueDP = filtered.filter(r => r.doiChieu === 'ok').reduce((s, r) => s + (r.tongTienLocal || 0), 0);

    document.getElementById('rcTotal').textContent = total;
    document.getElementById('rcMatched').textContent = matched;
    document.getElementById('rcMissing').textContent = missing;
    document.getElementById('rcRevenueTS').textContent = fmtN(revenueTS) + 'đ';
    document.getElementById('rcRevenueDP').textContent = fmtN(revenueDP) + 'đ';
    document.getElementById('rcStats').style.display = 'flex';

    const tbody = document.getElementById('rcBody');
    if (!filtered.length) {
        tbody.innerHTML = '<tr><td colspan="8" class="rc-empty">Không có dữ liệu phù hợp</td></tr>';
        return;
    }

    tbody.innerHTML = filtered.map((r, i) => {
        const tongTien = parseMoney(r.TongTien);
        const isOk = r.doiChieu === 'ok';
        const dcClass = isOk ? 'ok' : 'missing';
        const dcText = isOk ? '<i class="fa-solid fa-check-circle"></i> Đã chuyển' : '<i class="fa-solid fa-xmark-circle"></i> Chưa Chuyển Đơn';
        const localTien = isOk ? fmtN(r.tongTienLocal) + 'đ' : '—';
        return `<tr>
            <td>${i + 1}</td>
            <td>${fmtDate(r.Ngay)}</td>
            <td><code class="madh">${r.MaDH}</code></td>
            <td style="font-weight:600">${fmtN(tongTien)}đ</td>
            <td>${statusBadge(r.TrangThai)}</td>
            <td>${r.DVGH || ''}</td>
            <td class="col-dc ${dcClass}">${dcText}</td>
            <td class="col-dc ${dcClass}">${localTien}</td>
        </tr>`;
    }).join('');
}
</script>
@endpush
