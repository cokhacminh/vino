@extends('main.layouts.app')
@section('title', 'Đơn Hàng Đã Xóa')
@push('styles')
<style>
:root{--hdr:#1e2a4a;--hdr-text:#fff}
.al-wrap{padding:16px}
.al-card{background:#fff;border-radius:10px;box-shadow:0 2px 8px rgba(0,0,0,.08);overflow:hidden}
.al-card-header{background:#991b1b;color:#fff;padding:12px 16px;font-weight:700;font-size:14px;text-transform:uppercase;letter-spacing:.5px;display:flex;align-items:center;gap:8px}
.al-table{width:100%;border-collapse:collapse;font-size:13px}
.al-table th{background:var(--hdr);color:#fff;padding:8px 12px;text-align:center;font-size:12px;font-weight:600;border:1px solid #2a3a5e}
.al-table td{padding:7px 12px;text-align:center;border:1px solid #e2e8f0;vertical-align:middle}
.al-table tbody tr{background:#fafbfc}
.al-table tbody tr:hover{background:#f1f5f9}
.al-table tbody tr.restored-row{background:#f0fdf4}
.al-empty{text-align:center;padding:40px 0;color:#94a3b8;font-size:13px}
.al-badge-restored{background:#dcfce7;color:#15803d;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600}
.al-badge-deleted{background:#fef2f2;color:#991b1b;padding:3px 10px;border-radius:12px;font-size:11px;font-weight:600}
.al-btn{padding:5px 12px;border:none;border-radius:6px;font-size:11px;font-weight:600;cursor:pointer;transition:all .15s}
.al-btn-restore{background:#2563eb;color:#fff}.al-btn-restore:hover{background:#1d4ed8}
.al-btn-restore:disabled{background:#94a3b8;cursor:not-allowed}
.al-sp-list{text-align:left;font-size:11px;line-height:1.6}
</style>
@endpush

@section('content')
<div class="al-wrap">
    <div class="al-card">
        <div class="al-card-header">
            <i class="fa-solid fa-trash-can-arrow-up"></i> Đơn Hàng Đã Xóa (Activity Log)
        </div>
        <div style="padding:12px;overflow-x:auto">
            <table class="al-table">
                <thead>
                    <tr>
                        <th style="width:40px">STT</th>
                        <th>Ngày Xóa</th>
                        <th>Mã Đơn Hàng</th>
                        <th>Tổng Tiền</th>
                        <th>Nhân Viên</th>
                        <th>Khách Hàng</th>
                        <th>Sản Phẩm</th>
                        <th>Người Xóa</th>
                        <th>Trạng Thái</th>
                        <th>Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($logs as $i => $log)
                    @php
                        $kh = $log->khach_hang ?? [];
                        $ct = $log->chi_tiet ?? [];
                        $ngayXoa = $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('d/m/Y H:i') : '';
                    @endphp
                    <tr class="{{ $log->restored ? 'restored-row' : '' }}" id="logRow{{ $log->id }}">
                        <td>{{ $i + 1 }}</td>
                        <td style="white-space:nowrap">{{ $ngayXoa }}</td>
                        <td>
                            <code style="color:#991b1b;background:#fef2f2;padding:2px 8px;border-radius:4px;font-size:12px">{{ $log->MaDH }}</code>
                        </td>
                        <td style="font-weight:600">{{ number_format($log->TongTien) }}</td>
                        <td>{{ $log->TenNV }}</td>
                        <td>{{ $kh['TenKH'] ?? '' }}</td>
                        <td class="al-sp-list">
                            @foreach($ct as $sp)
                                {{ $sp['TenSP'] ?? $sp['MaSP'] }} x {{ $sp['SoLuong'] }}<br>
                            @endforeach
                        </td>
                        <td>{{ $log->deleted_by }}</td>
                        <td>
                            @if($log->restored)
                                <span class="al-badge-restored"><i class="fa-solid fa-check-circle"></i> Đã Khôi Phục</span>
                            @else
                                <span class="al-badge-deleted"><i class="fa-solid fa-trash"></i> Đã Xóa</span>
                            @endif
                        </td>
                        <td>
                            @if(!$log->restored)
                                <button class="al-btn al-btn-restore" onclick="restoreOrder({{ $log->id }})" id="btnRestore{{ $log->id }}">
                                    <i class="fa-solid fa-rotate-left"></i> Khôi Phục
                                </button>
                            @else
                                <span style="color:#059669;font-size:11px">
                                    <i class="fa-solid fa-check"></i>
                                    {{ $log->restored_at ? \Carbon\Carbon::parse($log->restored_at)->format('d/m H:i') : '' }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="10" class="al-empty">Chưa có đơn hàng nào bị xóa</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

async function restoreOrder(id) {
    if (!confirm('Bạn có chắc muốn khôi phục đơn hàng này?')) return;

    const btn = document.getElementById('btnRestore' + id);
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...';

    try {
        const res = await fetch(`/activity-log/${id}/restore`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
            },
        });
        const data = await res.json();
        if (data.success) {
            const row = document.getElementById('logRow' + id);
            row.classList.add('restored-row');
            // Update status cell
            const cells = row.querySelectorAll('td');
            cells[8].innerHTML = '<span class="al-badge-restored"><i class="fa-solid fa-check-circle"></i> Đã Khôi Phục</span>';
            cells[9].innerHTML = '<span style="color:#059669;font-size:11px"><i class="fa-solid fa-check"></i> Vừa khôi phục</span>';
            alert('Khôi phục đơn hàng thành công!');
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fa-solid fa-rotate-left"></i> Khôi Phục';
            alert(data.message || 'Lỗi khôi phục');
        }
    } catch (e) {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-rotate-left"></i> Khôi Phục';
        alert('Lỗi kết nối đến server');
    }
}
</script>
@endpush
