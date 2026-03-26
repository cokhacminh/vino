@extends('main.layouts.app')
@section('title', 'Đơn Thành Công')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
:root{--hdr:#1e2a4a;--hdr-text:#fff}
.dtc-wrap{padding:16px}
.dtc-card{background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.08);overflow:hidden}
.dtc-card-header{background:var(--hdr);color:var(--hdr-text);padding:12px 16px;font-weight:700;font-size:14px;text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:8px}
.dtc-filter{display:flex;align-items:center;gap:10px;padding:12px 16px;border-bottom:1px solid #e2e8f0;flex-wrap:wrap}
.dtc-filter input[type=text]{width:220px;padding:7px 12px;background:#f5deb3;text-align:center;font-size:13px;font-weight:700;border:1px solid #d1d5db;border-radius:6px}
.dtc-btn{padding:7px 16px;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;transition:all .15s}
.dtc-btn-red{background:#dc2626;color:#fff}.dtc-btn-red:hover{background:#b91c1c}
.dtc-table{width:100%;border-collapse:collapse;font-size:13px}
.dtc-table th{background:var(--hdr);color:#fff;padding:8px 12px;text-align:center;font-size:12px;font-weight:600;border:1px solid #2a3a5e}
.dtc-table td{padding:7px 12px;text-align:center;border:1px solid #e2e8f0;vertical-align:middle}
.dtc-table tbody tr{background:#fafbfc}
.dtc-table tbody tr:hover{background:#f1f5f9}
.dtc-empty{text-align:center;padding:40px 0;color:#94a3b8;font-size:13px}
.dtc-loading{text-align:center;padding:40px 0;color:#94a3b8;display:none}
.dtc-badge-ok{background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600}
.dtc-badge-no{background:#fef2f2;color:#991b1b;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600}
.dtc-summary{display:flex;gap:16px;padding:10px 16px;border-top:1px solid #e2e8f0;background:#f8fafc;font-size:13px;font-weight:600;flex-wrap:wrap}
.dtc-summary-item{display:flex;align-items:center;gap:6px}
</style>
@endpush

@section('content')
<div class="dtc-wrap">
    <div class="dtc-card">
        <div class="dtc-card-header">
            <i class="fa-solid fa-circle-check"></i> Danh Sách Đơn Thành Công
        </div>
        <div class="dtc-filter">
            <label style="font-size:13px;font-weight:600">Khoảng thời gian:</label>
            <input type="text" id="dtcDateRange" autocomplete="off" style="width:260px" placeholder="Chọn khoảng ngày">
            <button class="dtc-btn dtc-btn-red" onclick="loadDonTC()" id="btnXem"><i class="fa-solid fa-eye"></i> XEM</button>
        </div>
        <div style="padding:12px;overflow-x:auto">
            <div class="dtc-loading" id="dtcLoading"><i class="fa-solid fa-spinner fa-spin fa-2x"></i></div>
            <table class="dtc-table">
                <thead>
                    <tr>
                        <th style="width:50px">STT</th>
                        <th>Ngày Gửi</th>
                        <th>Ngày Thành Công</th>
                        <th>Mã Đơn Hàng</th>
                        <th>Tổng Tiền</th>
                        <th>Khách Hàng</th>
                        <th>Sản Phẩm</th>
                        <th>Tình Trạng</th>
                        <th>TT</th>
                    </tr>
                </thead>
                <tbody id="dtcBody">
                    <tr><td colspan="9" class="dtc-empty">Chọn ngày và nhấn XEM</td></tr>
                </tbody>
            </table>
        </div>
        <div class="dtc-summary" id="dtcSummary" style="display:none">
            <div class="dtc-summary-item"><i class="fa-solid fa-list" style="color:#2563eb"></i> Tổng: <span id="sumTotal">0</span> đơn</div>
            <div class="dtc-summary-item"><i class="fa-solid fa-check-circle" style="color:#059669"></i> Đã chuyển: <span id="sumOk">0</span></div>
            <div class="dtc-summary-item"><i class="fa-solid fa-times-circle" style="color:#dc2626"></i> Chưa chuyển: <span id="sumNo">0</span></div>
            <div class="dtc-summary-item"><i class="fa-solid fa-coins" style="color:#d97706"></i> Tổng tiền: <span id="sumMoney">0</span>đ</div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script>
const fpRange = flatpickr('#dtcDateRange', {
    mode: 'range',
    dateFormat: 'd/m/Y',
    locale: 'vn',
    allowInput: true,
    defaultDate: [new Date(), new Date()]
});

function toDmy(d) {
    return String(d.getDate()).padStart(2,'0') + '/' + String(d.getMonth()+1).padStart(2,'0') + '/' + d.getFullYear();
}

function loadDonTC() {
    const dates = fpRange.selectedDates;
    if (!dates.length) { alert('Vui lòng chọn khoảng thời gian'); return; }
    const tuNgay = toDmy(dates[0]);
    const denNgay = dates.length > 1 ? toDmy(dates[1]) : tuNgay;
    const ngay = tuNgay + ' - ' + denNgay;
    const loading = document.getElementById('dtcLoading');
    const tbody = document.getElementById('dtcBody');
    const btn = document.getElementById('btnXem');

    loading.style.display = 'block';
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tải...';
    document.getElementById('dtcSummary').style.display = 'none';

    fetch('/success-orders/fetch', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        },
        body: JSON.stringify({ Ngay: ngay }),
    })
    .then(r => r.json())
    .then(data => {
        loading.style.display = 'none';
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-eye"></i> XEM';

        const orders = data.orders || [];
        if (!orders.length) {
            tbody.innerHTML = '<tr><td colspan="9" class="dtc-empty">Không có đơn thành công trong khoảng thời gian này</td></tr>';
            return;
        }

        let totalMoney = 0, countOk = 0, countNo = 0;
        tbody.innerHTML = orders.map((o, i) => {
            const tien = Number(o.TongTien || 0);
            totalMoney += tien;
            const tienFmt = tien.toLocaleString('vi-VN');
            const isExist = o.existsInDb;
            if (isExist) countOk++; else countNo++;
            const badge = isExist
                ? '<span class="dtc-badge-ok"><i class="fa-solid fa-check-circle"></i> Đã Chuyển Đơn</span>'
                : '<span class="dtc-badge-no"><i class="fa-solid fa-times-circle"></i> Chưa Chuyển Đơn</span>';
            // Convert Ngay from Y-m-d H:i:s to d/m/Y
            let ngayFmt = '';
            if (o.Ngay) {
                const d = new Date(o.Ngay);
                if (!isNaN(d)) ngayFmt = String(d.getDate()).padStart(2,'0') + '/' + String(d.getMonth()+1).padStart(2,'0') + '/' + d.getFullYear();
            }
            // Convert ThoiGian from Y-m-d H:i:s to d/m/Y
            let thoiGianFmt = '';
            if (o.ThoiGian) {
                const t = new Date(o.ThoiGian);
                if (!isNaN(t)) thoiGianFmt = String(t.getDate()).padStart(2,'0') + '/' + String(t.getMonth()+1).padStart(2,'0') + '/' + t.getFullYear();
            }
            // Khách hàng
            let khHtml = '<span style="color:#94a3b8">—</span>';
            if (o.TenKH) {
                khHtml = `<b>${o.TenKH}</b>`;
                if (o.SoDienThoai) khHtml += `<br><span style="color:#64748b;font-size:11px">${o.SoDienThoai}</span>`;
                const addr = [o.DiaChi, o.Xa, o.Huyen, o.Tinh].filter(Boolean).join(', ');
                if (addr) khHtml += `<br><span style="color:#64748b;font-size:11px">${addr}</span>`;
            }
            // Sản phẩm
            const spHtml = o.SanPham && o.SanPham.length ? o.SanPham.join('<br>') : '<span style="color:#94a3b8">—</span>';
            return `<tr>
                <td>${i + 1}</td>
                <td style="white-space:nowrap">${ngayFmt}</td>
                <td style="white-space:nowrap">${thoiGianFmt}</td>
                <td><code style="color:#1e40af;background:#dbeafe;padding:2px 8px;border-radius:4px;font-size:12px">${o.MaDH || ''}</code></td>
                <td style="font-weight:600">${tienFmt}</td>
                <td style="text-align:left;font-size:12px">${khHtml}</td>
                <td style="text-align:left;font-size:12px">${spHtml}</td>
                <td>${badge}</td>
                <td></td>
            </tr>`;
        }).join('');

        document.getElementById('sumTotal').textContent = orders.length;
        document.getElementById('sumOk').textContent = countOk;
        document.getElementById('sumNo').textContent = countNo;
        document.getElementById('sumMoney').textContent = totalMoney.toLocaleString('vi-VN');
        document.getElementById('dtcSummary').style.display = 'flex';
    })
    .catch(() => {
        loading.style.display = 'none';
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-eye"></i> XEM';
        tbody.innerHTML = '<tr><td colspan="9" class="dtc-empty" style="color:#dc2626">Lỗi kết nối đến server</td></tr>';
    });
}

// Auto load khi vừa vào trang
document.addEventListener('DOMContentLoaded', function() {
    loadDonTC();
});
</script>
@endpush
