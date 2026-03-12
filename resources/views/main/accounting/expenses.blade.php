@extends('main.layouts.app')
@section('title', 'Quản Lý Chi Phí')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="{{ asset('css/main/month-picker.css') }}">
<style>
    .ex-page { padding: 10px; background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); }

    /* Header */
    .ex-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 12px; }
    .ex-header h2 { margin: 0; font-size: 22px; color: #1e293b; }
    .ex-add-btn {
        padding: 8px 18px; border: none; border-radius: 8px;
        background: linear-gradient(135deg, #ef4444, #dc2626); color: white;
        font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.2s; height: 36px;
    }
    .ex-add-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(239,68,68,0.4); }

    /* Date bar */
    .ex-topbar { display: flex; align-items: center; gap: 10px; margin-bottom: 14px; flex-wrap: wrap; }
    .ex-date-wrap { position: relative; }
    .ex-date-input {
        padding: 7px 12px; border: 2px solid #ef4444; border-radius: 6px;
        font-size: 13px; width: 220px; cursor: pointer; background: white; color: #1e293b; font-weight: 600;
    }
    .ex-date-presets {
        position: absolute; top: 100%; left: 0; background: white; border: 2px solid #ef4444;
        border-radius: 8px; overflow: hidden; z-index: 999; min-width: 160px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.15); display: none;
    }
    .ex-date-presets.open { display: block; }
    .ex-date-preset { padding: 8px 16px; font-size: 13px; font-weight: 600; cursor: pointer; border-bottom: 1px solid #f1f5f9; color: #1e293b; transition: all 0.15s; }
    .ex-date-preset:hover { background: #ef4444; color: white; }
    .ex-date-preset:last-child { border-bottom: none; }

    /* Status Tabs */
    .ex-tabs { display: flex; gap: 6px; flex-wrap: wrap; }
    .ex-tab {
        padding: 6px 14px; border-radius: 8px; cursor: pointer;
        font-size: 12px; font-weight: 600; border: 2px solid #e2e8f0;
        background: white; color: #64748b; transition: all 0.2s; text-decoration: none;
    }
    .ex-tab:hover { border-color: #94a3b8; }
    .ex-tab.active { border-color: #ef4444; background: #fef2f2; color: #dc2626; }
    .ex-tab .ex-count { display: inline-block; min-width: 18px; padding: 1px 6px; border-radius: 10px; font-size: 10px; margin-left: 4px; background: #e2e8f0; color: #475569; }
    .ex-tab.active .ex-count { background: #ef4444; color: white; }

    /* Summary Cards */
    .ex-cards { display: grid; grid-template-columns: repeat(3, 1fr); gap: 12px; margin-bottom: 16px; }
    @media (max-width: 700px) { .ex-cards { grid-template-columns: 1fr 1fr; } }
    .ex-card {
        background: linear-gradient(135deg, var(--c1), var(--c2));
        border-radius: 10px; padding: 14px 16px; color: white;
        box-shadow: 0 4px 14px rgba(0,0,0,0.12);
    }
    .ex-card-label { font-size: 11px; font-weight: 700; opacity: 0.85; letter-spacing: 0.5px; text-transform: uppercase; margin-bottom: 6px; }
    .ex-card-val { font-size: 20px; font-weight: 800; }
    .ex-card-sub { font-size: 11px; opacity: 0.7; margin-top: 3px; }

    /* Controls */
    .ex-controls { display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; flex-wrap: wrap; gap: 10px; }
    .ex-controls-left { display: flex; align-items: center; gap: 8px; font-size: 13px; color: #64748b; }
    .ex-controls-left select { padding: 4px 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; }
    .ex-search { padding: 7px 14px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; width: 260px; outline: none; }
    .ex-search:focus { border-color: #ef4444; box-shadow: 0 0 0 2px rgba(239,68,68,0.12); }

    /* Table */
    .ex-table-wrap { overflow-x: auto; border-radius: 8px; border: 1px solid #e2e8f0; }
    .ex-table { width: 100%; border-collapse: collapse; font-size: 12.5px; }
    .ex-table thead th {
        background: #7f1d1d; color: white; padding: 10px 10px;
        font-weight: 600; text-align: left; white-space: nowrap;
        border-right: 1px solid #991b1b; position: sticky; top: 0; font-size: 15px;
    }
    .ex-table thead th:last-child { border-right: none; text-align: center; }
    .ex-table tbody tr { border-bottom: 1px solid #e2e8f0; }
    .ex-table tbody tr:hover { background: #fff5f5; }
    .ex-table tbody tr:nth-child(even) { background: #fafbfc; }
    .ex-table tbody tr:nth-child(even):hover { background: #fff0f0; }
    .ex-table td { font-size: 15px; padding: 10px 10px; vertical-align: middle; }
    .ex-table tr.row-dahuy { opacity: 0.55; }

    /* Cols */
    .ex-col-id { width: 60px; text-align: center; color: #64748b; font-size: 13px !important; }
    .ex-col-ngay { width: 90px; }
    .ex-col-ngay .ngay-val { font-weight: 700; color: #1e293b; }
    .ex-col-nguoi { width: 110px; color: #dc2626; font-weight: 700; }
    .ex-col-noidung { min-width: 220px; }
    .ex-col-noidung .nd-text { font-weight: 600; color: #1e293b; }
    .ex-col-noidung .nd-loai { font-size: 11px; color: #94a3b8; margin-top: 3px; }
    .ex-col-sotien { width: 130px; text-align: right; }
    .ex-col-sotien .st-val { font-size: 17px; font-weight: 800; color: #dc2626; }
    .ex-col-tt { width: 110px; text-align: center; }
    .ex-badge { display: inline-block; padding: 4px 10px; border-radius: 4px; font-size: 12px; font-weight: 700; }
    .ex-badge-ok  { background: #dcfce7; color: #166534; }
    .ex-badge-huy { background: #fee2e2; color: #991b1b; }
    .ex-col-action { width: 80px; text-align: center; }

    /* Action buttons */
    .ex-btn { display: inline-flex; align-items: center; justify-content: center; width: 32px; height: 28px; border-radius: 4px; border: none; cursor: pointer; font-size: 14px; transition: all 0.15s; }
    .ex-btn:hover { transform: scale(1.15); }
    .ex-btn-cancel { background: #f97316; color: white; }
    .ex-btn-del    { background: #ef4444; color: white; }

    /* Pagination */
    .ex-pagination { display: flex; justify-content: space-between; align-items: center; margin-top: 14px; flex-wrap: wrap; gap: 10px; }
    .ex-pg-info { font-size: 13px; color: #64748b; }
    .ex-pg-btns { display: flex; gap: 4px; }
    .ex-pg-btn { padding: 6px 10px; border: 1px solid #d1d5db; border-radius: 6px; background: white; font-size: 12px; cursor: pointer; }
    .ex-pg-btn:hover { background: #f1f5f9; }
    .ex-pg-btn.active { background: #ef4444; color: white; border-color: #ef4444; }

    /* Empty */
    .ex-empty { text-align: center; padding: 60px 20px; color: #94a3b8; }
    .ex-empty .empty-icon { font-size: 48px; margin-bottom: 12px; }

    /* Modal */
    .ex-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.55); z-index: 9999; display: none; justify-content: center; align-items: flex-start; padding: 30px 10px; overflow-y: auto; }
    .ex-modal-overlay.show { display: flex; }
    .ex-modal { background: white; border-radius: 12px; width: 100%; max-width: 460px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
    .ex-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 20px; border-bottom: 1px solid #e2e8f0; background: linear-gradient(135deg, #fff5f5, #fee2e2); }
    .ex-modal-header h3 { margin: 0; font-size: 16px; font-weight: 700; color: #991b1b; }
    .ex-modal-close { background: none; border: none; font-size: 22px; cursor: pointer; color: #94a3b8; }
    .ex-modal-close:hover { color: #ef4444; }
    .ex-modal-body { padding: 20px; display: flex; flex-direction: column; gap: 14px; }
    .ex-form-group label { display: block; font-size: 13px; font-weight: 700; color: #1e293b; margin-bottom: 5px; }
    .ex-form-group input, .ex-form-group textarea { width: 100%; padding: 9px 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 13px; outline: none; box-sizing: border-box; }
    .ex-form-group input:focus, .ex-form-group textarea:focus { border-color: #ef4444; box-shadow: 0 0 0 2px rgba(239,68,68,0.12); }
    .ex-form-group textarea { resize: vertical; }
    .ex-modal-footer { display: flex; justify-content: flex-end; gap: 10px; padding: 14px 20px; border-top: 1px solid #e2e8f0; background: #f8fafc; }
    .ex-modal-btn { padding: 9px 22px; border: none; border-radius: 8px; font-size: 13px; font-weight: 700; cursor: pointer; transition: all 0.15s; }
    .ex-modal-btn-cancel-ui { background: #6b7280; color: white; }
    .ex-modal-btn-submit { background: #ef4444; color: white; }
    .ex-modal-btn-submit:hover { background: #dc2626; }

    /* Cancel reason modal */
    .ex-cancel-modal { position: fixed; inset: 0; background: rgba(0,0,0,0.5); z-index: 9999; display: none; justify-content: center; align-items: center; }
    .ex-cancel-modal.show { display: flex; }
    .ex-cancel-box { background: white; border-radius: 12px; width: 400px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); overflow: hidden; }
</style>
@endpush

@section('content')
<div class="ex-page">
    {{-- Header --}}
    <div class="ex-header">
        <h2><i class="fa-solid fa-money-bill-wave" style="color:#ef4444;"></i> Quản Lý Chi Phí</h2>
        @canany(['Admin', 'Kế Toán'])
        <button class="ex-add-btn" onclick="openExCreate()"><i class="fa-solid fa-plus"></i> Thêm Phiếu Chi</button>
        @endcanany
    </div>

    {{-- Top Bar: Tabs + Month picker --}}
    <div class="ex-topbar" style="justify-content:space-between;">
        {{-- Status Tabs --}}
        <div class="ex-tabs">
            <a href="{{ route('accounting.expenses', ['month'=>$month]) }}"
               class="ex-tab {{ $tinhTrangFilter=='all' ? 'active' : '' }}">
                Tất cả <span class="ex-count">{{ $countAll }}</span>
            </a>
            <a href="{{ route('accounting.expenses', ['month'=>$month,'tinh_trang'=>'Có Hiệu Lực']) }}"
               class="ex-tab {{ $tinhTrangFilter=='Có Hiệu Lực' ? 'active' : '' }}">
                Có Hiệu Lực <span class="ex-count">{{ $countCoHieuLuc }}</span>
            </a>
            <a href="{{ route('accounting.expenses', ['month'=>$month,'tinh_trang'=>'Đã Huỷ']) }}"
               class="ex-tab {{ $tinhTrangFilter=='Đã Huỷ' ? 'active' : '' }}">
                Đã Huỷ <span class="ex-count">{{ $countDaHuy }}</span>
            </a>
        </div>

        <div style="display:flex; align-items:center; gap:8px;">
            <span style="font-size:13px; color:#64748b;">Tổng: <strong>{{ $countAll }}</strong> phiếu</span>
            @php
                $exThang = (int) \Carbon\Carbon::parse($month . '-01')->format('m');
                $exNam   = (int) \Carbon\Carbon::parse($month . '-01')->format('Y');
            @endphp
            @include('main.components.month-picker', ['month' => $exThang, 'year' => $exNam])
        </div>
    </div>

    {{-- Summary Cards (only 2) --}}
    <div class="ex-cards" style="grid-template-columns: repeat(2,1fr);">
        <div class="ex-card" style="--c1:#ef4444; --c2:#b91c1c;">
            <div class="ex-card-label">Phiếu Có Hiệu Lực</div>
            <div class="ex-card-val" id="cardCount">{{ number_format($totalHoaDon) }}</div>
            <div class="ex-card-sub">phiếu chi hợp lệ</div>
        </div>
        <div class="ex-card" style="--c1:#f97316; --c2:#c2410c;">
            <div class="ex-card-label">Số Tiền</div>
            <div class="ex-card-val" id="cardTotal">{{ number_format($totalSoTien) }}</div>
            <div class="ex-card-sub">tổng tiền chi hợp lệ</div>
        </div>
    </div>

    {{-- Controls --}}
    <div class="ex-controls">
        <div class="ex-controls-left">
            Hiển thị
            <select id="exPageSize" onchange="changeExPageSize()">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50" selected>50</option>
                <option value="100">100</option>
            </select>
            dòng
        </div>
        <div>
            <label style="font-size:13px; color:#64748b;">Lọc nhanh:</label>
            <input type="text" class="ex-search" id="exSearch" placeholder="Tìm nội dung, người chi..." oninput="filterExTable()">
        </div>
    </div>

    {{-- Table --}}
    <div class="ex-table-wrap">
        <table class="ex-table" id="exTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Ngày</th>
                    <th>Người Chi</th>
                    <th>Nội Dung</th>
                    <th style="text-align:right;">Số Tiền</th>
                    <th>Tình Trạng</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach($expenses as $ex)
                @php
                    $ngay = $ex->Ngay ? \Carbon\Carbon::parse($ex->Ngay)->format('d/m/Y') : '—';
                    $isHuy = $ex->TinhTrang === 'Đã Huỷ';
                @endphp
                <tr class="ex-row {{ $isHuy ? 'row-dahuy' : '' }}"
                    data-search="{{ strtolower(($ex->NoiDung ?? '') . ' ' . ($ex->TenNguoiChi ?? '') . ' ' . ($ex->LoaiPhieu ?? '')) }}"
                    data-sotien="{{ $ex->SoTien ?? 0 }}"
                    data-valid="{{ $isHuy ? '0' : '1' }}">
                    <td class="ex-col-id">{{ $ex->id }}</td>
                    <td class="ex-col-ngay">
                        <div class="ngay-val">{{ $ngay }}</div>
                    </td>
                    <td class="ex-col-nguoi">{{ $ex->TenNguoiChi ?? '—' }}</td>
                    <td class="ex-col-noidung">
                        <div class="nd-text">{{ $ex->NoiDung ?? '—' }}</div>
                        <div class="nd-loai">{{ $ex->LoaiPhieu ?? '' }}</div>
                        @if($isHuy && $ex->NguyenNhan)
                            <div style="font-size:11px; color:#ef4444; margin-top:3px;">
                                <i class="fa-solid fa-circle-xmark"></i> {{ $ex->NguyenNhan }}
                                <span style="color:#94a3b8;">({{ $ex->TenNguoiHuy ?? '' }})</span>
                            </div>
                        @endif
                    </td>
                    <td class="ex-col-sotien">
                        <div class="st-val" style="{{ $isHuy ? 'color:#94a3b8;text-decoration:line-through;' : '' }}">
                            {{ number_format($ex->SoTien ?? 0, 0, ',', ',') }}
                        </div>
                    </td>
                    <td class="ex-col-tt">
                        @if($isHuy)
                            <span class="ex-badge ex-badge-huy"><i class="fa-solid fa-ban"></i> Đã Huỷ</span>
                        @else
                            <span class="ex-badge ex-badge-ok"><i class="fa-solid fa-circle-check"></i> Hiệu Lực</span>
                        @endif
                    </td>
                    <td class="ex-col-action">
                        @if(!$isHuy)
                            @canany(['Admin', 'Kế Toán'])
                            <button type="button" class="ex-btn ex-btn-cancel" title="Huỷ phiếu"
                                    onclick="openExCancel({{ $ex->id }}, '{{ addslashes($ex->NoiDung ?? '') }}')">
                                <i class="fa-solid fa-ban"></i>
                            </button>
                            @endcanany
                        @endif
                        @can('Admin')
                        <form method="POST" action="{{ route('accounting.expenses.destroy', $ex->id) }}"
                              onsubmit="return confirm('Xóa vĩnh viễn phiếu #{{ $ex->id }}?')" style="display:contents;">
                            @csrf @method('DELETE')
                            <button type="submit" class="ex-btn ex-btn-del" title="Xóa vĩnh viễn">
                                <i class="fa-solid fa-trash-can"></i>
                            </button>
                        </form>
                        @endcan
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($expenses->count() > 0)
    <div class="ex-pagination" id="exPagination">
        <span class="ex-pg-info" id="exPageInfo"></span>
        <div class="ex-pg-btns" id="exPageButtons"></div>
    </div>
    @endif

    @if($expenses->count() === 0)
    <div class="ex-empty">
        <div class="empty-icon">💸</div>
        <h3>Không có phiếu chi</h3>
        <p>Không tìm thấy dữ liệu trong khoảng thời gian này</p>
    </div>
    @endif
</div>

{{-- ===== THÊM PHIẾU CHI MODAL ===== --}}
<div class="ex-modal-overlay" id="exCreateModal">
    <div class="ex-modal">
        <div class="ex-modal-header">
            <h3><i class="fa-solid fa-plus"></i> THÊM PHIẾU CHI</h3>
            <button class="ex-modal-close" onclick="closeExCreate()">&times;</button>
        </div>
        <form method="POST" action="{{ route('accounting.expenses.store') }}">
            @csrf
            <div class="ex-modal-body">
                <div class="ex-form-group">
                    <label>Ngày Chi</label>
                    <input type="text" name="Ngay" id="exNgayChi" placeholder="DD/MM/YYYY" required>
                </div>
                <div class="ex-form-group">
                    <label>Số Tiền (VNĐ)</label>
                    <input type="text" id="exSoTienFmt" placeholder="0" oninput="fmtExInput(this)">
                    <input type="hidden" name="SoTien" id="exSoTienRaw">
                </div>
                <div class="ex-form-group">
                    <label>Nội Dung</label>
                    <textarea name="NoiDung" rows="3" placeholder="Mô tả nội dung chi phí..." required></textarea>
                </div>
            </div>
            <div class="ex-modal-footer">
                <button type="button" class="ex-modal-btn ex-modal-btn-cancel-ui" onclick="closeExCreate()">Đóng</button>
                <button type="submit" class="ex-modal-btn ex-modal-btn-submit"><i class="fa-solid fa-floppy-disk"></i> Lưu</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== HUỶ PHIẾU CHI MODAL ===== --}}
<div class="ex-cancel-modal" id="exCancelModal">
    <div class="ex-cancel-box">
        <div style="padding:14px 20px; border-bottom:1px solid #e2e8f0; background:linear-gradient(135deg,#fff5f5,#fee2e2);">
            <h3 style="margin:0; font-size:15px; font-weight:700; color:#991b1b;"><i class="fa-solid fa-ban"></i> Huỷ Phiếu Chi #<span id="exCancelIdTxt"></span></h3>
        </div>
        <form method="POST" id="exCancelForm">
            @csrf @method('PUT')
            <div style="padding:20px; display:flex; flex-direction:column; gap:12px;">
                <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:10px 14px; font-size:13px; color:#991b1b;">
                    <strong>Nội dung:</strong> <span id="exCancelNd"></span>
                </div>
                <div class="ex-form-group">
                    <label>Lý Do Huỷ</label>
                    <textarea name="NguyenNhan" rows="3" placeholder="Nhập lý do huỷ phiếu..." style="width:100%;padding:9px 12px;border:1px solid #d1d5db;border-radius:8px;font-size:13px;resize:vertical;box-sizing:border-box;"></textarea>
                </div>
            </div>
            <div style="display:flex; gap:10px; padding:14px 20px; border-top:1px solid #e2e8f0; background:#f8fafc; justify-content:flex-end;">
                <button type="button" style="padding:8px 18px;border:none;border-radius:8px;background:#e2e8f0;color:#475569;font-size:13px;font-weight:600;cursor:pointer;" onclick="closeExCancel()">Đóng</button>
                <button type="submit" style="padding:8px 18px;border:none;border-radius:8px;background:#ef4444;color:white;font-size:13px;font-weight:700;cursor:pointer;"><i class="fa-solid fa-ban"></i> Xác Nhận Huỷ</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="{{ asset('js/main/month-picker.js') }}"></script>
<script>
    // ===== MONTH PICKER CALLBACK =====
    window.onMonthPickerSelect = function(month, year) {
        var val = year + '-' + String(month).padStart(2, '0');
        var url = new URL(window.location.href);
        url.searchParams.set('month', val);
        window.location.href = url.toString();
    };

    // ===== CREATE MODAL =====
    function openExCreate() { document.getElementById('exCreateModal').classList.add('show'); }
    function closeExCreate() { document.getElementById('exCreateModal').classList.remove('show'); }
    document.getElementById('exCreateModal').addEventListener('click', function(e) {
        if (e.target === this) closeExCreate();
    });

    // ===== CANCEL MODAL =====
    function openExCancel(id, nd) {
        document.getElementById('exCancelIdTxt').textContent = id;
        document.getElementById('exCancelNd').textContent = nd;
        document.getElementById('exCancelForm').action = `/accounting/expenses/${id}/cancel`;
        document.getElementById('exCancelModal').classList.add('show');
    }
    function closeExCancel() { document.getElementById('exCancelModal').classList.remove('show'); }
    document.getElementById('exCancelModal').addEventListener('click', function(e) {
        if (e.target === this) closeExCancel();
    });

    // ===== FORMAT INPUT =====
    function fmtExInput(el) {
        const raw = el.value.replace(/[^0-9]/g, '');
        el.value = raw ? parseInt(raw).toLocaleString('vi-VN') : '';
        document.getElementById('exSoTienRaw').value = raw || 0;
    }

    // ===== SEARCH & PAGINATION =====
    let exAllRows = [], exFilteredRows = [], exCurrentPage = 1, exPageSize = 50;

    document.addEventListener('DOMContentLoaded', function() {
        flatpickr('#exNgayChi', { dateFormat: 'd/m/Y', allowInput: true, defaultDate: 'today' });
        exAllRows = Array.from(document.querySelectorAll('#exTable tbody tr.ex-row'));
        exFilteredRows = [...exAllRows];
        renderExPage();
    });

    function filterExTable() {
        const q = document.getElementById('exSearch').value.toLowerCase().trim();
        exFilteredRows = exAllRows.filter(r => !q || r.dataset.search.includes(q));
        exCurrentPage = 1;
        renderExPage();
    }

    function changeExPageSize() {
        exPageSize = parseInt(document.getElementById('exPageSize').value);
        exCurrentPage = 1;
        renderExPage();
    }

    function renderExPage() {
        const total = exFilteredRows.length;
        const totalPages = Math.ceil(total / exPageSize) || 1;
        if (exCurrentPage > totalPages) exCurrentPage = totalPages;
        const start = (exCurrentPage - 1) * exPageSize, end = Math.min(start + exPageSize, total);

        exAllRows.forEach(r => r.style.display = 'none');
        for (let i = start; i < end; i++) exFilteredRows[i].style.display = '';

        // Recalc summary
        let cnt = 0, total_st = 0;
        exFilteredRows.forEach(r => {
            if (r.dataset.valid === '1') { cnt++; total_st += parseInt(r.dataset.sotien || 0); }
        });
        const c = document.getElementById('cardCount'), t = document.getElementById('cardTotal');
        if (c) c.textContent = cnt.toLocaleString('vi-VN');
        if (t) t.textContent = total_st.toLocaleString('vi-VN');

        const info = document.getElementById('exPageInfo');
        if (info) info.textContent = total > 0 ? `Hiển thị ${start+1}–${end} / ${total} phiếu` : '';

        const bc = document.getElementById('exPageButtons');
        if (!bc) return;
        bc.innerHTML = '';
        const pb = (t, d, a, c) => {
            const b = document.createElement('button'); b.className = 'ex-pg-btn' + (a ? ' active' : '');
            b.textContent = t; b.disabled = d; b.onclick = c; bc.appendChild(b);
        };
        pb('‹', exCurrentPage===1, false, ()=>{exCurrentPage--; renderExPage();});
        let sp = Math.max(1, exCurrentPage-2), ep = Math.min(totalPages, sp+4);
        if (ep-sp < 4) sp = Math.max(1, ep-4);
        for (let i = sp; i <= ep; i++) pb(i, false, i===exCurrentPage, ()=>{exCurrentPage=i; renderExPage();});
        pb('›', exCurrentPage===totalPages, false, ()=>{exCurrentPage++; renderExPage();});
    }
</script>
@endpush
