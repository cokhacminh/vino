@extends('main.layouts.app')
@section('title', 'Chuyển Đơn Hàng')
@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<style>
.flatpickr-calendar{z-index:9999!important}
:root{--hdr:#1e2a4a;--hdr-text:#fff;--danger-row:#e35a5a}
.to-grid{display:grid;grid-template-columns:5fr 3fr 3fr;gap:12px;padding:12px}
.to-card{background:#fff;border-radius:8px;box-shadow:0 1px 4px rgba(0,0,0,.08);overflow:hidden}
.to-card-header{background:var(--hdr);color:var(--hdr-text);padding:10px 14px;font-weight:700;font-size:13px;text-transform:uppercase;text-align:center;letter-spacing:.5px}
.to-card-body{padding:10px}
.to-table{width:100%;border-collapse:collapse;font-size:13px}
.to-table th{background:var(--hdr);color:#fff;padding:7px 10px;text-align:center;font-size:12px;font-weight:600;border:1px solid #2a3a5e}
.to-table td{padding:6px 10px;text-align:center;border:1px solid #e2e8f0;vertical-align:middle}
.to-table tbody tr{background:#fafbfc}
.to-table tbody tr:hover{background:#f1f5f9}
.to-table tbody tr.low-stock{background:var(--danger-row);color:#fff;font-weight:700}
.to-table tbody tr.low-stock td{border-color:#c0392b}
.to-filter{display:flex;align-items:center;gap:8px;padding:8px 10px;border-bottom:1px solid #e2e8f0;flex-wrap:wrap}
.to-filter input[type=text]{width:170px;padding:6px 10px;background:#f5deb3;text-align:center;font-size:13px;font-weight:700;border:1px solid #d1d5db;border-radius:6px}
.to-btn{padding:6px 14px;border:none;border-radius:6px;font-size:12px;font-weight:600;cursor:pointer;transition:all .15s}
.to-btn-red{background:#dc2626;color:#fff}.to-btn-red:hover{background:#b91c1c}
.to-btn-gray{background:#6b7280;color:#fff}.to-btn-gray:hover{background:#4b5563}
.to-loading{text-align:center;padding:40px 0;color:#94a3b8;display:none}
.to-empty{text-align:center;padding:30px 0;color:#94a3b8;font-size:13px}
.to-btn-blue{background:#2563eb;color:#fff}.to-btn-blue:hover{background:#1d4ed8}
.to-modal-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,.5);z-index:1000;justify-content:center;align-items:center}
.to-modal-overlay.show{display:flex}
.to-modal{background:#fff;border-radius:10px;width:800px;max-width:95vw;max-height:90vh;overflow-y:auto;box-shadow:0 10px 40px rgba(0,0,0,.2)}
.to-modal-hdr{background:var(--hdr);color:#fff;padding:12px 16px;border-radius:10px 10px 0 0;display:flex;justify-content:space-between;align-items:center;font-weight:700;font-size:14px}
.to-modal-hdr button{background:none;border:none;color:#fff;font-size:20px;cursor:pointer}
.to-modal-body{padding:16px}
.to-modal-body table{width:100%;border-collapse:collapse;font-size:13px}
.to-modal-body th{background:#f1f5f9;padding:8px 10px;text-align:center;font-size:12px;font-weight:600;border:1px solid #e2e8f0}
.to-modal-body th:nth-child(1){width:15%}
.to-modal-body th:nth-child(2){width:15%}
.to-modal-body th:nth-child(3){width:20%}
.to-modal-body th:nth-child(4){width:20%}
.to-modal-body th:nth-child(5){width:30%}
.to-modal-body td{padding:6px 8px;text-align:center;border:1px solid #e2e8f0}
.to-modal-body input[type=number],.to-modal-body select{width:100%;padding:5px 8px;border:1px solid #d1d5db;border-radius:5px;font-size:13px;text-align:center;box-sizing:border-box}
.to-modal-body .setting-row{display:flex;align-items:center;gap:10px;margin-bottom:12px}
.to-modal-body .setting-row label{font-weight:600;font-size:13px;min-width:160px}
.to-modal-body .setting-row input{width:100px;padding:6px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;text-align:center}
.to-modal-footer{padding:12px 16px;border-top:1px solid #e2e8f0;text-align:right}
.to-btn-green{background:#059669;color:#fff;font-size:11px;padding:4px 10px}.to-btn-green:hover{background:#047857}
.to-btn-bulk{background:#2563eb;color:#fff;padding:10px 16px;border:none;border-radius:8px;font-size:14px;font-weight:700;cursor:pointer;width:100%;margin-top:10px;transition:all .15s}
.to-btn-bulk:hover{background:#1d4ed8}
.to-btn-bulk:disabled{background:#94a3b8;cursor:not-allowed}
.to-bulk-info{text-align:center;color:#64748b;font-size:12px;margin-top:6px}
.to-log{max-height:200px;overflow-y:auto;font-size:12px;margin-top:8px;border:1px solid #e2e8f0;border-radius:6px;padding:6px 8px;background:#f8fafc}
.to-log-item{padding:3px 0;border-bottom:1px solid #f1f5f9}
.to-log-ok{color:#059669}.to-log-err{color:#dc2626}
.fmt-money{width:150px;padding:4px 20px;border-radius:4px;border:1px solid #bab6b6}
@media(max-width:1024px){.to-grid{grid-template-columns:1fr}}
</style>
@endpush

@section('content')
<div class="to-grid">
    {{-- CỘT TRÁI: DANH SÁCH ĐƠN CHUYỂN --}}
    <div class="to-card">
        <div class="to-card-header"><i class="fa-solid fa-truck-fast"></i> Danh Sách Đơn Chuyển</div>
        <div class="to-filter">
            <input type="text" id="toDatePicker" value="{{ now()->format('d/m/Y') }}" autocomplete="off">
            <button class="to-btn to-btn-red" onclick="loadData()">XEM</button>
            <button class="to-btn to-btn-gray" onclick="navDate(-1)">Prev</button>
            <button class="to-btn to-btn-gray" onclick="navDate(1)">Next</button>
            <button class="to-btn to-btn-blue" onclick="document.getElementById('settingsModal').classList.add('show')" title="Cài đặt thuật toán"><i class="fa-solid fa-gear"></i> Cài Đặt</button>
            <button class="to-btn" style="background:#059669;color:#fff" onclick="loadSuccessOrders()" id="btnSuccess"><i class="fa-solid fa-circle-check"></i> Đơn TC Hôm Nay</button>
        </div>
        <div class="to-card-body">
            <div class="to-loading" id="toLoading"><i class="fa-solid fa-spinner fa-spin fa-2x"></i></div>
            <table class="to-table">
                <thead><tr><th>STT</th><th>Ngày</th><th>Mã Đơn</th><th>Sản Phẩm</th><th>Tổng Tiền</th><th>Thao Tác</th></tr></thead>
                <tbody id="toOrdersBody">
                    <tr><td colspan="6" class="to-empty">Chọn ngày và nhấn XEM</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- CỘT GIỮA: TỔNG SẢN PHẨM XUẤT --}}
    <div class="to-card">
        <div class="to-card-header"><i class="fa-solid fa-boxes-stacked"></i> Tổng Sản Phẩm Xuất</div>
        <div class="to-card-body">
            <table class="to-table">
                <thead><tr><th>Sản Phẩm</th><th>Số Lượng Xuất</th><th>Tồn Mới</th></tr></thead>
                <tbody id="toTongXuatBody">
                    <tr><td colspan="3" class="to-empty">—</td></tr>
                </tbody>
            </table>
            <button class="to-btn-bulk" id="btnBulkTransfer" onclick="bulkTransfer()" disabled>
                <i class="fa-solid fa-list-check"></i> CHUYỂN TOÀN BỘ (<span id="bulkCount">0</span> đơn)
            </button>
            <div class="to-bulk-info" id="bulkInfo">Còn <span id="bulkRemain">0</span> đơn không chọn được sản phẩm</div>
            <div class="to-log" id="transferLog" style="display:none"></div>
        </div>
    </div>

    {{-- CỘT PHẢI: DANH SÁCH TỒN KHO --}}
    <div class="to-card">
        <div class="to-card-header" style="background:#8b1a1a"><i class="fa-solid fa-warehouse"></i> Danh Sách Tồn Kho</div>
        <div class="to-card-body">
            <table class="to-table">
                <thead><tr>
                    <th style="background:#8b1a1a">Sản Phẩm</th>
                    <th style="background:#8b1a1a">Số Lượng</th>
                    <th style="background:#8b1a1a">Giá Bán</th>
                </tr></thead>
                <tbody id="toStockBody">
                    @foreach($tonKho as $sp)
                    <tr class="{{ $sp->SoLuong <= 20 ? 'low-stock' : '' }}" data-masp="{{ $sp->MaSP }}">
                        <td style="text-align:left;padding-left:12px">{{ $sp->TenSP }}</td>
                        <td class="stock-qty">{{ number_format($sp->SoLuong) }}</td>
                        <td>{{ number_format($sp->GiaBan_SG ?? 0) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- MODAL CÀI ĐẶT THUẬT TOÁN --}}
<div class="to-modal-overlay" id="settingsModal" onclick="if(event.target===this)this.classList.remove('show')">
<div class="to-modal">
    <div class="to-modal-hdr">
        <span><i class="fa-solid fa-gear"></i> Cài Đặt Thuật Toán Chọn SP</span>
        <button onclick="this.closest('.to-modal-overlay').classList.remove('show')">&times;</button>
    </div>
    <div class="to-modal-body">
        <div class="setting-row">
            <label>Mức tăng thêm (%):</label>
            <input type="number" id="cfgMarkup" value="20" min="0" max="100" step="1">
        </div>
        <table>
            <thead><tr>
                <th>Từ (VNĐ)</th>
                <th>Đến (VNĐ)</th>
                <th>SL SP tối đa</th>
                <th>Số SP khác nhau</th>
                <th>Ưu Tiên</th>
            </tr></thead>
            <tbody>
                <tr>
                    <td><input type="text" id="cfgT1From" class="fmt-money" value="0"></td>
                    <td><input type="text" id="cfgT1To" class="fmt-money" value="1,000,000"></td>
                    <td><input type="number" id="cfgT1MaxQty" value="999" min="1"></td>
                    <td><input type="number" id="cfgT1MinSP" value="1" min="1"></td>
                    <td><select id="cfgT1Priority"><option value="random" selected>Ngẫu nhiên</option><option value="high">Giá cao trước</option></select></td>
                </tr>
                <tr>
                    <td><input type="text" id="cfgT2From" class="fmt-money" value="1,000,000"></td>
                    <td><input type="text" id="cfgT2To" class="fmt-money" value="4,000,000"></td>
                    <td><input type="number" id="cfgT2MaxQty" value="4" min="1"></td>
                    <td><input type="number" id="cfgT2MinSP" value="2" min="1"></td>
                    <td><select id="cfgT2Priority"><option value="random">Ngẫu nhiên</option><option value="high" selected>Giá cao trước</option></select></td>
                </tr>
                <tr>
                    <td><input type="text" id="cfgT3From" class="fmt-money" value="4,000,000"></td>
                    <td><input type="text" id="cfgT3To" class="fmt-money" value="8,000,000"></td>
                    <td><input type="number" id="cfgT3MaxQty" value="5" min="1"></td>
                    <td><input type="number" id="cfgT3MinSP" value="3" min="1"></td>
                    <td><select id="cfgT3Priority"><option value="random">Ngẫu nhiên</option><option value="high" selected>Giá cao trước</option></select></td>
                </tr>
                <tr>
                    <td><input type="text" id="cfgT4From" class="fmt-money" value="8,000,000"></td>
                    <td><input type="text" id="cfgT4To" class="fmt-money" value="999,999,999"></td>
                    <td><input type="number" id="cfgT4MaxQty" value="10" min="1"></td>
                    <td><input type="number" id="cfgT4MinSP" value="3" min="1"></td>
                    <td><select id="cfgT4Priority"><option value="random">Ngẫu nhiên</option><option value="high" selected>Giá cao trước</option></select></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="to-modal-footer">
        <button class="to-btn to-btn-blue" onclick="saveSettings()"><i class="fa-solid fa-check"></i> Lưu & Áp Dụng</button>
    </div>
</div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script>
const INVENTORY_ORIGINAL = @json($tonKhoJs);
const fp = flatpickr('#toDatePicker', {dateFormat:'d/m/Y', locale:'vn', allowInput:true, defaultDate:'today'});

function navDate(dir) {
    const cur = fp.selectedDates[0] || new Date();
    const d = new Date(cur);
    d.setDate(d.getDate() + dir);
    fp.setDate(d);
    // Reset bulk transfer button
    const btn = document.getElementById('btnBulkTransfer');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-list-check"></i> CHUYỂN TOÀN BỘ (<span id="bulkCount">0</span> đơn)';
    document.getElementById('transferLog').style.display = 'none';
    document.getElementById('transferLog').innerHTML = '';
    loadData();
}

function parseAmount(v) { return Number(String(v || 0).replace(/[,\.]/g, '')) || 0; }
function fmtNum(n) { return Number(n || 0).toLocaleString('vi-VN'); }
function parseFmt(s) { return Number(String(s || 0).replace(/[^0-9]/g, '')) || 0; }

function shuffle(arr) {
    for (let i = arr.length - 1; i > 0; i--) {
        const j = Math.floor(Math.random() * (i + 1));
        [arr[i], arr[j]] = [arr[j], arr[i]];
    }
    return arr;
}

// Auto-format money inputs
document.addEventListener('input', function(e) {
    if (e.target.classList.contains('fmt-money')) {
        const raw = parseFmt(e.target.value);
        const pos = e.target.selectionStart;
        const oldLen = e.target.value.length;
        e.target.value = fmtNum(raw);
        const newLen = e.target.value.length;
        e.target.setSelectionRange(pos + newLen - oldLen, pos + newLen - oldLen);
    }
});

// ====== CÀI ĐẶT THUẬT TOÁN ======
const DB_SETTINGS = @json($algoSettings);
let algoSettings = DB_SETTINGS || {
    markup: 20,
    tiers: [
        { from: 0, to: 1000000, maxQty: 999, minSP: 1, priority: 'random' },
        { from: 1000000, to: 4000000, maxQty: 4, minSP: 2, priority: 'high' },
        { from: 4000000, to: 8000000, maxQty: 5, minSP: 3, priority: 'high' },
        { from: 8000000, to: 999999999, maxQty: 10, minSP: 3, priority: 'high' },
    ]
};

function fillSettingsForm() {
    document.getElementById('cfgMarkup').value = algoSettings.markup || 20;
    for (let i = 0; i < 4; i++) {
        const t = algoSettings.tiers[i] || {};
        document.getElementById(`cfgT${i+1}From`).value = fmtNum(t.from || 0);
        document.getElementById(`cfgT${i+1}To`).value = fmtNum(t.to || 999999999);
        document.getElementById(`cfgT${i+1}MaxQty`).value = t.maxQty || 1;
        document.getElementById(`cfgT${i+1}MinSP`).value = t.minSP || 1;
        document.getElementById(`cfgT${i+1}Priority`).value = t.priority || 'random';
    }
}
fillSettingsForm();

function saveSettings() {
    algoSettings.markup = parseInt(document.getElementById('cfgMarkup').value) || 20;
    for (let i = 1; i <= 4; i++) {
        algoSettings.tiers[i-1] = {
            from: parseFmt(document.getElementById(`cfgT${i}From`).value),
            to: parseFmt(document.getElementById(`cfgT${i}To`).value),
            maxQty: parseInt(document.getElementById(`cfgT${i}MaxQty`).value) || 1,
            minSP: parseInt(document.getElementById(`cfgT${i}MinSP`).value) || 1,
            priority: document.getElementById(`cfgT${i}Priority`).value,
        };
    }
    fetch('/transfer-orders/save-settings', {
        method: 'POST',
        headers: {'Content-Type':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content},
        body: JSON.stringify({ settings: algoSettings }),
    });
    document.getElementById('settingsModal').classList.remove('show');
    loadData();
}

// ====== THUẬT TOÁN TỰ ĐỘNG CHỌN SẢN PHẨM ======
function autoSelectProducts(orders) {
    const stock = INVENTORY_ORIGINAL.map(p => ({...p}));
    const markupPct = algoSettings.markup / 100;

    const sorted = [...orders].map((o, idx) => ({...o, _idx: idx, _tongTien: parseAmount(o.TongTien)}));
    sorted.sort((a, b) => b._tongTien - a._tongTien);

    const results = new Array(orders.length);

    for (const order of sorted) {
        const tongTien = order._tongTien;
        if (order.existsInDb || tongTien <= 0) { results[order._idx] = []; continue; }

        const targetMin = tongTien;
        const targetMax = tongTien * (1 + markupPct);

        let tier = algoSettings.tiers.find(t => tongTien >= t.from && tongTien < t.to);
        if (!tier) tier = algoSettings.tiers[algoSettings.tiers.length - 1];

        const maxQty = tier.maxQty;
        const priorityHigh = tier.priority === 'high';
        const minDistinct = tier.minSP;
        const targetDistinct = tier.priority === 'random' ? Math.min(1 + Math.floor(Math.random() * 3), minDistinct || 3) : minDistinct;

        const selected = selectForOrder(stock, targetMin, targetMax, maxQty, targetDistinct, priorityHigh);

        for (const sp of selected) {
            const s = stock.find(x => x.MaSP === sp.MaSP);
            if (s) s.SoLuong = Math.max(0, s.SoLuong - sp.SoLuong);
        }

        results[order._idx] = selected;
    }

    return { results, remainingStock: stock };
}

function selectForOrder(stock, targetMin, targetMax, maxQty, minDistinct, priorityHigh) {
    let available = stock.filter(p => p.SoLuong > 0 && p.GiaBan > 0);
    if (!available.length) return [];

    const selected = {};
    let total = 0;

    let candidates;
    if (priorityHigh) {
        candidates = [...available].sort((a, b) => b.GiaBan - a.GiaBan);
    } else {
        candidates = shuffle([...available]);
    }

    for (const p of candidates) {
        if (Object.keys(selected).length >= minDistinct) break;
        if (total + p.GiaBan > targetMax) continue;
        selected[p.MaSP] = { MaSP: p.MaSP, TenSP: p.TenSP, GiaBan: p.GiaBan, SoLuong: 1 };
        total += p.GiaBan;
    }

    const selectedList = Object.values(selected).sort((a, b) => b.GiaBan - a.GiaBan);
    for (const sp of selectedList) {
        const stockItem = stock.find(s => s.MaSP === sp.MaSP);
        const maxAdd = Math.min(maxQty, stockItem ? stockItem.SoLuong : 0) - sp.SoLuong;
        for (let i = 0; i < maxAdd; i++) {
            if (total + sp.GiaBan > targetMax) break;
            sp.SoLuong++;
            total += sp.GiaBan;
            if (total >= targetMin) break;
        }
        if (total >= targetMin) break;
    }

    if (total < targetMin) {
        let extras;
        if (priorityHigh) {
            extras = [...available].filter(p => !selected[p.MaSP]).sort((a, b) => b.GiaBan - a.GiaBan);
        } else {
            extras = shuffle([...available].filter(p => !selected[p.MaSP]));
        }
        for (const p of extras) {
            if (total >= targetMin) break;
            const stockItem = stock.find(s => s.MaSP === p.MaSP);
            const maxAdd = Math.min(maxQty, stockItem ? stockItem.SoLuong : 0);
            if (maxAdd <= 0 || total + p.GiaBan > targetMax) continue;
            selected[p.MaSP] = { MaSP: p.MaSP, TenSP: p.TenSP, GiaBan: p.GiaBan, SoLuong: 0 };
            for (let i = 0; i < maxAdd; i++) {
                if (total + p.GiaBan > targetMax) break;
                selected[p.MaSP].SoLuong++;
                total += p.GiaBan;
                if (total >= targetMin) break;
            }
        }
    }

    return Object.values(selected).filter(s => s.SoLuong > 0);
}

// ====== LOAD & RENDER ======
let currentOrders = [];
let currentResults = [];

function loadData() {
    const date = document.getElementById('toDatePicker').value;
    const loading = document.getElementById('toLoading');
    loading.style.display = 'block';
    document.getElementById('transferLog').style.display = 'none';
    document.getElementById('transferLog').innerHTML = '';

    fetch(`/transfer-orders/data?date=${encodeURIComponent(date)}`)
        .then(r => r.json())
        .then(data => {
            loading.style.display = 'none';
            currentOrders = data.orders || [];
            const { results, remainingStock } = autoSelectProducts(currentOrders);
            currentResults = results;
            renderOrders(currentOrders, currentResults);
            renderTongXuat(currentResults);
            updateBulkButton();
        })
        .catch(() => { loading.style.display = 'none'; });
}

function loadSuccessOrders() {
    const loading = document.getElementById('toLoading');
    const btn = document.getElementById('btnSuccess');
    loading.style.display = 'block';
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang tải...';
    document.getElementById('transferLog').style.display = 'none';
    document.getElementById('transferLog').innerHTML = '';

    fetch('/transfer-orders/success-orders')
        .then(r => r.json())
        .then(data => {
            loading.style.display = 'none';
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Đơn TC Hôm Nay';
            currentOrders = data.orders || [];
            const { results } = autoSelectProducts(currentOrders);
            currentResults = results;
            renderOrders(currentOrders, currentResults);
            renderTongXuat(currentResults);
            updateBulkButton();
        })
        .catch(() => {
            loading.style.display = 'none';
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-circle-check"></i> Đơn TC Hôm Nay';
            alert('Lỗi kết nối đến server');
        });
}

function updateBulkButton() {
    let transferable = 0, noProducts = 0;
    currentOrders.forEach((o, i) => {
        if (o.existsInDb) return;
        const sel = currentResults[i] || [];
        if (sel.length > 0) transferable++;
        else noProducts++;
    });
    const cntEl = document.getElementById('bulkCount');
    const remEl = document.getElementById('bulkRemain');
    if (cntEl) cntEl.textContent = transferable;
    if (remEl) remEl.textContent = noProducts;
    document.getElementById('btnBulkTransfer').disabled = transferable === 0;
}

function renderOrders(orders, results) {
    const tbody = document.getElementById('toOrdersBody');
    if (!orders.length) {
        tbody.innerHTML = '<tr><td colspan="6" class="to-empty">Không có đơn hàng</td></tr>';
        return;
    }
    tbody.innerHTML = orders.map((o, i) => {
        const ngay = o.Ngay ? new Date(o.Ngay).toLocaleDateString('vi-VN') : '';
        const tien = parseAmount(o.TongTien);
        const tienFmt = tien.toLocaleString('vi-VN');
        const isExisting = o.existsInDb;
        const selected = results[i] || [];
        let spHtml, spTotalFmt = '';
        if (isExisting) {
            spHtml = '<span style="color:#059669;font-weight:600"><i class="fa-solid fa-check-circle"></i> Đã chuyển đơn rồi</span>';
        } else if (selected.length) {
            spHtml = selected.map(s => `${s.TenSP} x ${s.SoLuong}`).join('<br>');
            const spTotal = selected.reduce((sum, s) => sum + s.GiaBan * s.SoLuong, 0);
            spTotalFmt = spTotal > 0 ? `<br><b style="color:#059669">${spTotal.toLocaleString('vi-VN')}</b>` : '';
        } else {
            spHtml = '<span style="color:#94a3b8">—</span>';
        }
        const rowStyle = isExisting ? 'background:#f0fdf4' : '';
        const actionHtml = isExisting
            ? ''
            : (selected.length
                ? `<button class="to-btn to-btn-green btn-transfer" onclick="transferSingle(${i})"><i class="fa-solid fa-paper-plane"></i> Chuyển Đơn</button>`
                : '<span style="color:#94a3b8;font-size:11px">—</span>');
        return `<tr style="${rowStyle}" id="orderRow${i}">
            <td>${i + 1}</td>
            <td>${ngay}</td>
            <td><code style="color:#1e40af;background:#dbeafe;padding:2px 6px;border-radius:4px;font-size:12px">${o.MaDH}</code></td>
            <td style="text-align:left;font-size:12px">${spHtml}${spTotalFmt}</td>
            <td style="font-weight:600">${tienFmt}</td>
            <td>${actionHtml}</td>
        </tr>`;
    }).join('');
}

function renderTongXuat(results) {
    const tbody = document.getElementById('toTongXuatBody');
    const totals = {};
    for (const items of results) {
        if (!items) continue;
        for (const sp of items) {
            if (!totals[sp.MaSP]) totals[sp.MaSP] = { TenSP: sp.TenSP, MaSP: sp.MaSP, SoLuong: 0 };
            totals[sp.MaSP].SoLuong += sp.SoLuong;
        }
    }
    const list = Object.values(totals).sort((a, b) => a.TenSP.localeCompare(b.TenSP));
    if (!list.length) { tbody.innerHTML = '<tr><td colspan="3" class="to-empty">Không có dữ liệu</td></tr>'; return; }
    tbody.innerHTML = list.map(i => {
        return `<tr><td style="text-align:left;padding-left:10px">${i.TenSP}</td><td style="font-weight:600">${i.SoLuong.toLocaleString('vi-VN')}</td><td>—</td></tr>`;
    }).join('');
}

function renderRemainingStock(remainingStock) {
    const rows = document.querySelectorAll('#toStockBody tr');
    for (const row of rows) {
        const masp = row.dataset.masp;
        const remain = remainingStock.find(s => s.MaSP === masp);
        if (remain) {
            row.querySelector('.stock-qty').textContent = remain.SoLuong.toLocaleString('vi-VN');
            remain.SoLuong <= 20 ? row.classList.add('low-stock') : row.classList.remove('low-stock');
        }
    }
}

// ====== CHUYỂN ĐƠN ======
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

async function transferSingle(idx) {
    const order = currentOrders[idx];
    const selected = currentResults[idx] || [];
    if (!order || !selected.length) return { success: false, message: 'Không có sản phẩm' };

    const btn = document.querySelector(`#orderRow${idx} .btn-transfer`);
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>'; }

    const dateParts = document.getElementById('toDatePicker').value.split('/');
    const ngay = dateParts.length === 3 ? `${dateParts[2]}-${dateParts[1]}-${dateParts[0]}` : '';

    try {
        const res = await fetch('/transfer-orders/transfer', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                MaDH: order.MaDH, Ngay: ngay, TongTien: parseAmount(order.TongTien),
                items: selected.map(s => ({ MaSP: s.MaSP, SoLuong: s.SoLuong, GiaBan: s.GiaBan })),
            }),
        });
        if (!res.ok) {
            const errText = await res.text();
            throw new Error(errText.substring(0, 200) || `HTTP ${res.status}`);
        }
        const result = await res.json();
        if (result.success) {
            const row = document.getElementById(`orderRow${idx}`);
            if (row) {
                row.style.background = '#f0fdf4';
                row.querySelector('td:last-child').innerHTML = '<span style="color:#059669;font-size:11px"><i class="fa-solid fa-check"></i> Đã chuyển</span>';
            }
            order.existsInDb = true;
            // Trừ tồn kho hiển thị
            const items = currentResults[idx] || [];
            for (const sp of items) {
                const stockRows = document.querySelectorAll(`#toStockBody tr[data-masp="${sp.MaSP}"]`);
                stockRows.forEach(r => {
                    const qtyEl = r.querySelector('.stock-qty');
                    if (qtyEl) {
                        const cur = parseInt(qtyEl.textContent.replace(/\D/g, '')) || 0;
                        const newQty = Math.max(0, cur - sp.SoLuong);
                        qtyEl.textContent = newQty.toLocaleString('vi-VN');
                        newQty <= 20 ? r.classList.add('low-stock') : r.classList.remove('low-stock');
                    }
                });
            }
            updateBulkButton();
        } else if (btn) {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Chuyển Đơn';
        }
        return result;
    } catch (e) {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Chuyển Đơn'; }
        return { success: false, message: 'Lỗi: ' + (e.message || 'Kết nối thất bại') };
    }
}

async function bulkTransfer() {
    const btn = document.getElementById('btnBulkTransfer');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> ĐANG CHUYỂN...';

    const logEl = document.getElementById('transferLog');
    logEl.style.display = 'block';
    logEl.innerHTML = '';

    const indices = [];
    currentOrders.forEach((o, i) => {
        if (!o.existsInDb && (currentResults[i] || []).length > 0) indices.push(i);
    });

    let okCount = 0, errCount = 0;
    for (const idx of indices) {
        const order = currentOrders[idx];
        const result = await transferSingle(idx);
        const cls = result.success ? 'to-log-ok' : 'to-log-err';
        const icon = result.success ? 'fa-check-circle' : 'fa-times-circle';
        if (result.success) okCount++; else errCount++;
        logEl.innerHTML += `<div class="to-log-item ${cls}"><i class="fa-solid ${icon}"></i> ${order.MaDH}: ${result.message}</div>`;
        logEl.scrollTop = logEl.scrollHeight;
    }

    btn.innerHTML = `<i class="fa-solid fa-list-check"></i> HOÀN TẤT (${okCount} thành công, ${errCount} lỗi)`;
    updateBulkButton();
}

document.addEventListener('DOMContentLoaded', loadData);
</script>
@endpush
