{{-- Top 10 Best Seller — Tabbed: Thủy Sản & Tôm Giống --}}
<div class="top10-card">
    <div class="top10-header">
        <h3><i class="fa-solid fa-trophy"></i> Best Seller Of Month</h3>
        <span class="top10-period">Tháng {{ \Carbon\Carbon::parse($month . '-01')->format('m/Y') }}</span>
    </div>

    {{-- Tabs --}}
    <div class="top10-tabs">
        <button class="top10-tab active" onclick="switchTop10Tab('thuySan', this)">
            <i class="fa-solid fa-fish"></i> Thủy Sản
        </button>
        <button class="top10-tab" onclick="switchTop10Tab('tomGiong', this)">
            <i class="fa-solid fa-shrimp"></i> Tôm Giống
        </button>
    </div>

    {{-- Tab: Thủy Sản --}}
    <div class="top10-tab-content" id="top10-thuySan" style="display: block;">
        <div class="top10-table-container">
            @if($topThuySan->count() > 0)
            <table class="top10-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nhân Viên</th>
                        <th>Đơn Hàng</th>
                        <th>Doanh Thu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topThuySan as $index => $seller)
                    @php
                        $avatarUrl = $seller->Avatar
                            ? (file_exists(public_path('storage/avatars/' . $seller->Avatar))
                                ? asset('storage/avatars/' . $seller->Avatar)
                                : (file_exists(public_path('images/' . $seller->Avatar))
                                    ? asset('images/' . $seller->Avatar)
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($seller->TenNV ?? 'N') . '&size=36&background=6d28d9&color=fff&bold=true'))
                            : 'https://ui-avatars.com/api/?name=' . urlencode($seller->TenNV ?? 'N') . '&size=36&background=6d28d9&color=fff&bold=true';
                    @endphp
                    <tr class="seller-row {{ $index < 3 ? 'top-rank rank-' . ($index + 1) : '' }}" style="animation-delay: {{ $index * 0.05 }}s">
                        <td>
                            @if($index == 0)
                                <span class="rank-badge gold"><i class="fa-solid fa-crown"></i> 1</span>
                            @elseif($index == 1)
                                <span class="rank-badge silver"><i class="fa-solid fa-crown"></i> 2</span>
                            @elseif($index == 2)
                                <span class="rank-badge bronze"><i class="fa-solid fa-crown"></i> 3</span>
                            @else
                                <span class="rank-number">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="seller-info">
                                <img class="seller-avatar" src="{{ $avatarUrl }}" alt="{{ $seller->TenNV }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($seller->TenNV ?? 'N') }}&background=6d28d9&color=fff&size=36&rounded=true'">
                                <span class="seller-name">{{ $seller->TenNV ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="text-center">{{ $seller->SoDonHang }}</td>
                        <td class="text-right revenue">{{ number_format($seller->TongDoanhThu, 0, ',', '.') }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <i class="fa-solid fa-chart-simple"></i>
                <p>Chưa có dữ liệu tháng này</p>
            </div>
            @endif
        </div>
    </div>

    {{-- Tab: Tôm Giống --}}
    <div class="top10-tab-content" id="top10-tomGiong" style="display: none;">
        <div class="top10-table-container">
            @if($topTomGiong->count() > 0)
            <table class="top10-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nhân Viên</th>
                        <th>Đơn Hàng</th>
                        <th>Lượng Post</th>
                        <th>Doanh Thu</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topTomGiong as $index => $seller)
                    @php
                        $avatarUrl = $seller->Avatar
                            ? (file_exists(public_path('storage/avatars/' . $seller->Avatar))
                                ? asset('storage/avatars/' . $seller->Avatar)
                                : (file_exists(public_path('images/' . $seller->Avatar))
                                    ? asset('images/' . $seller->Avatar)
                                    : 'https://ui-avatars.com/api/?name=' . urlencode($seller->TenNV ?? 'N') . '&size=36&background=6d28d9&color=fff&bold=true'))
                            : 'https://ui-avatars.com/api/?name=' . urlencode($seller->TenNV ?? 'N') . '&size=36&background=6d28d9&color=fff&bold=true';
                    @endphp
                    <tr class="seller-row {{ $index < 3 ? 'top-rank rank-' . ($index + 1) : '' }}" style="animation-delay: {{ $index * 0.05 }}s">
                        <td>
                            @if($index == 0)
                                <span class="rank-badge gold"><i class="fa-solid fa-crown"></i> 1</span>
                            @elseif($index == 1)
                                <span class="rank-badge silver"><i class="fa-solid fa-crown"></i> 2</span>
                            @elseif($index == 2)
                                <span class="rank-badge bronze"><i class="fa-solid fa-crown"></i> 3</span>
                            @else
                                <span class="rank-number">{{ $index + 1 }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="seller-info">
                                <img class="seller-avatar" src="{{ $avatarUrl }}" alt="{{ $seller->TenNV }}" onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($seller->TenNV ?? 'N') }}&background=6d28d9&color=fff&size=36&rounded=true'">
                                <span class="seller-name">{{ $seller->TenNV ?? 'N/A' }}</span>
                            </div>
                        </td>
                        <td class="text-center">{{ $seller->SoDonHang }}</td>
                        <td class="text-center">{{ number_format($seller->TongLuongPost ?? 0) }}</td>
                        <td class="text-right revenue">{{ number_format($seller->TongDoanhThu, 0, ',', '.') }}đ</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            @else
            <div class="empty-state">
                <i class="fa-solid fa-chart-simple"></i>
                <p>Chưa có dữ liệu tháng này</p>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
function switchTop10Tab(tab, btn) {
    document.querySelectorAll('.top10-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.top10-tab').forEach(el => el.classList.remove('active'));
    document.getElementById('top10-' + tab).style.display = 'block';
    btn.classList.add('active');
}
</script>
