<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SeedOrderController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user && $user->can('Admin');

        // Date filter — default to current month
        $dateFrom = $request->input('date_from', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $dateTo = $request->input('date_to', Carbon::now()->endOfMonth()->format('Y-m-d'));

        $query = DB::table('donhang_giong as dg')
            ->leftJoin('khachhang as kh', 'kh.MaDH', '=', 'dg.MaDH')
            ->leftJoin('users as nv', 'nv.id', '=', DB::raw('CAST(dg.MaNV AS UNSIGNED)'))
            ->select(
                'dg.*',
                'kh.TenKH', 'kh.SoDienThoai', 'kh.DiaChi',
                'kh.Tinh', 'kh.Huyen', 'kh.Xa',
                'nv.name as TenNV'
            );

        // Permission: Admin, Sale Manager see all; others see own only
        $canViewAll = $user && ($user->can('Admin') || $user->can('Sale Manager') || $user->can('Kế Toán'));
        if (!$canViewAll) {
            $query->where('dg.MaNV', $user->id);
        }

        // Search filter
        $search = $request->input('search', '');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('dg.MaDH', 'like', "%{$search}%")
                  ->orWhere('kh.TenKH', 'like', "%{$search}%")
                  ->orWhere('kh.SoDienThoai', 'like', "%{$search}%")
                  ->orWhere('kh.Tinh', 'like', "%{$search}%");
            });
        }

        // Status filter
        $statusFilter = $request->input('status', 'all');
        if ($statusFilter && $statusFilter !== 'all') {
            $query->where('dg.TrangThai', $statusFilter);
        }

        // Date range filter
        if ($dateFrom && $dateTo) {
            $query->whereBetween('dg.NgayGiao', [$dateFrom, $dateTo]);
        }

        // Status counts (respecting date filter)
        $countBase = DB::table('donhang_giong');
        if ($dateFrom && $dateTo) {
            $countBase = $countBase->whereBetween('NgayGiao', [$dateFrom, $dateTo]);
        }
        if (!$canViewAll) {
            $countBase = $countBase->where('MaNV', $user->id);
        }
        $allCount = (clone $countBase)->count();
        $statusCounts = [
            'Chờ Giao Giống' => (clone $countBase)->where('TrangThai', 'Chờ Giao Giống')->count(),
            'Đã Giao Giống'  => (clone $countBase)->where('TrangThai', 'Đã Giao Giống')->count(),
            'Đã Huỷ Đơn'     => (clone $countBase)->where('TrangThai', 'Đã Huỷ Đơn')->count(),
            'Xả Bỏ'          => (clone $countBase)->where('TrangThai', 'Xả Bỏ')->count(),
        ];

        $orders = $query->orderByDesc('dg.NgayGiao')->orderByDesc('dg.id')->get();

        // Staff list for dropdown
        $staffList = DB::table('users')->select('id', 'name')->orderBy('name')->get();

        // Province list for create form
        $provinces = DB::table('diachinh')->select('ProvinceID', 'Tinh')->distinct()->orderBy('Tinh')->get();

        // Get MaDH that already have payment records
        $paidMaDHs = DB::table('hoadonbangiong')->pluck('MaDH')->toArray();

        return view('main.orders.seed_orders', compact(
            'orders', 'isAdmin', 'canViewAll',
            'statusFilter', 'search', 'allCount', 'statusCounts', 'staffList',
            'dateFrom', 'dateTo', 'provinces', 'paidMaDHs'
        ));
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        // Generate MaDH: prefix DG + random
        $lastId = DB::table('donhang_giong')->max('id') ?? 0;
        $maDH = 'DG' . str_pad($lastId + 1, 4, '0', STR_PAD_LEFT);

        // Parse NgayGiao from DD/MM/YYYY
        $ngayGiao = null;
        if ($request->input('ngaygiao')) {
            try {
                $ngayGiao = Carbon::createFromFormat('d/m/Y', $request->input('ngaygiao'))->format('Y-m-d');
            } catch (\Exception $e) {
                $ngayGiao = $request->input('ngaygiao');
            }
        }

        // Insert seed order
        $orderId = DB::table('donhang_giong')->insertGetId([
            'MaDH'              => $maDH,
            'NgayGiao'          => $ngayGiao,
            'DoMan'             => $request->input('doman'),
            'MaNV'              => $request->input('manv'),
            'SoLuong'           => $request->input('soluong'),
            'SLTT'              => $request->input('sltt'),
            'KhuyenMai'         => $request->input('khuyenmai'),
            'TongTien'          => $request->input('tongtien'),
            'GiaBan'            => $request->input('giaban'),
            'TrangThai'         => 'Chờ Giao Giống',
            'ThanhToan'         => 'Chưa Thanh Toán',
            'GhiChu'            => $request->input('ghichu'),
            'TinhTrangHienTai'  => '',
            'NhanGiong'         => '',
        ]);

        // Insert customer info
        DB::table('khachhang')->insert([
            'MaDH'          => $maDH,
            'TenKH'         => $request->input('tenkh'),
            'SoDienThoai'   => $request->input('sodienthoai'),
            'DiaChi'        => $request->input('diachi'),
            'Tinh'          => $request->input('tinh_text'),
            'Huyen'         => $request->input('huyen_text'),
            'Xa'            => $request->input('xa_text'),
        ]);

        return redirect()->route('seedOrders.index')->with('success', 'Đã tạo đơn ' . $maDH);
    }

    public function update(Request $request, $id)
    {
        $user = auth()->user();
        $order = DB::table('donhang_giong')->where('id', $id)->first();
        if (!$order) return redirect()->back()->with('error', 'Không tìm thấy đơn hàng.');

        $isAdmin = $user && $user->can('Admin');
        if (!$isAdmin && $order->MaNV != $user->id) {
            return redirect()->back()->with('error', 'Không có quyền sửa đơn này.');
        }

        // Check if this is a full edit (from the edit modal) or a partial update
        if ($request->has('tenkh')) {
            // Full edit from create/edit modal
            $ngayGiao = null;
            if ($request->input('ngaygiao')) {
                try {
                    $ngayGiao = Carbon::createFromFormat('d/m/Y', $request->input('ngaygiao'))->format('Y-m-d');
                } catch (\Exception $e) {
                    $ngayGiao = $request->input('ngaygiao');
                }
            }

            DB::table('donhang_giong')->where('id', $id)->update([
                'NgayGiao'          => $ngayGiao,
                'DoMan'             => $request->input('doman'),
                'MaNV'              => $request->input('manv'),
                'SoLuong'           => $request->input('soluong'),
                'SLTT'              => $request->input('sltt'),
                'KhuyenMai'         => $request->input('khuyenmai'),
                'TongTien'          => $request->input('tongtien'),
                'GiaBan'            => $request->input('giaban'),
                'GhiChu'            => $request->input('ghichu'),
            ]);

            // Update customer info
            DB::table('khachhang')->where('MaDH', $order->MaDH)->update([
                'TenKH'         => $request->input('tenkh'),
                'SoDienThoai'   => $request->input('sodienthoai'),
                'DiaChi'        => $request->input('diachi'),
                'Tinh'          => $request->input('tinh_text'),
                'Huyen'         => $request->input('huyen_text'),
                'Xa'            => $request->input('xa_text'),
            ]);
        } elseif ($request->input('update_type') === 'cap_nhat_tinh_trang') {
            // Cập Nhật tình trạng + insert call_log_giong
            $newTinhTrang = $request->input('TinhTrangHienTai', '');
            $oldTinhTrang = $order->TinhTrangHienTai ?? '';

            // Update TinhTrangHienTai
            DB::table('donhang_giong')->where('id', $id)->update([
                'TinhTrangHienTai' => $newTinhTrang,
            ]);

            // Insert into call_log_giong
            DB::table('call_log_giong')->insert([
                'MaDH'              => $order->MaDH,
                'MaNV'              => $user->id,
                'NgayLienHe'        => Carbon::now()->toDateString(),
                'ChiTiet'           => $newTinhTrang,
                'ThoiGian'          => Carbon::now(),
            ]);
        } else {
            // Partial update (status/deliver modals)
            $data = [];
            $fillable = ['NhanGiong', 'TinhTrangHienTai', 'TrangThai', 'ThanhToan', 'GhiChu', 'NgayLienHe'];
            foreach ($fillable as $field) {
                if ($request->has($field)) {
                    $data[$field] = $request->input($field);
                }
            }
            if (!empty($data)) {
                DB::table('donhang_giong')->where('id', $id)->update($data);
            }
        }

        return redirect()->back()->with('success', 'Đã cập nhật đơn ' . $order->MaDH);
    }

    public function destroy($id)
    {
        $user = auth()->user();
        if (!$user || !$user->can('Admin')) {
            return redirect()->back()->with('error', 'Chỉ Admin mới có quyền xóa.');
        }

        $order = DB::table('donhang_giong')->where('id', $id)->first();
        if (!$order) return redirect()->back()->with('error', 'Không tìm thấy đơn.');

        DB::table('khachhang')->where('MaDH', $order->MaDH)->delete();
        DB::table('donhang_giong')->where('id', $id)->delete();

        return redirect()->route('seedOrders.index')->with('success', 'Đã xóa đơn ' . $order->MaDH);
    }

    public function storePayment(Request $request)
    {
        $user = auth()->user();
        if (!$user || (!$user->can('Admin') && !$user->can('Kế Toán'))) {
            return redirect()->back()->with('error', 'Bạn không có quyền thanh toán.');
        }

        // Parse NgayThanhToan from DD/MM/YYYY
        $ngayTT = null;
        if ($request->input('NgayThanhToan')) {
            try {
                $ngayTT = Carbon::createFromFormat('d/m/Y', $request->input('NgayThanhToan'))->format('Y-m-d');
            } catch (\Exception $e) {
                $ngayTT = $request->input('NgayThanhToan');
            }
        }

        $thucNhan = (int) $request->input('ThucNhan', 0);
        $chuyenTra = (int) $request->input('ChuyenTraTrai', 0);
        $doanhSo = $thucNhan - $chuyenTra;

        DB::table('hoadonbangiong')->insert([
            'seed_order_id'  => $request->input('seed_order_id'),
            'MaDH'           => $request->input('MaDH'),
            'NgayThanhToan'  => $ngayTT,
            'SoLuongNhan'    => $request->input('SoLuongNhan', 0),
            'ThucNhan'       => $thucNhan,
            'ChuyenTraTrai'  => $chuyenTra,
            'DoanhSo'        => $doanhSo,
            'GhiChu'         => $request->input('GhiChu'),
        ]);

        // Update order payment status
        DB::table('donhang_giong')
            ->where('id', $request->input('seed_order_id'))
            ->update(['ThanhToan' => 'Đã Thanh Toán']);

        return redirect()->back()->with('success', 'Đã lưu thanh toán ' . $request->input('MaDH'));
    }

    public function khachMuaGiong(Request $request)
    {
        $search = $request->input('search', '');

        $query = DB::table('donhang_giong as dg')
            ->leftJoin('khachhang as kh', 'kh.MaDH', '=', 'dg.MaDH')
            ->leftJoin('users as nv', 'nv.id', '=', DB::raw('CAST(dg.MaNV AS UNSIGNED)'))
            ->select(
                'kh.TenKH',
                'kh.SoDienThoai',
                'kh.DiaChi',
                'kh.Huyen',
                'kh.Tinh',
                'dg.SoLuong',
                'nv.name as TenNV',
                'dg.NgayGiao'
            )
            ->whereNotNull('kh.TenKH')
            ->orderByDesc('dg.NgayGiao');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kh.TenKH', 'like', "%{$search}%")
                  ->orWhere('kh.SoDienThoai', 'like', "%{$search}%")
                  ->orWhere('kh.Tinh', 'like', "%{$search}%")
                  ->orWhere('kh.Huyen', 'like', "%{$search}%")
                  ->orWhere('nv.name', 'like', "%{$search}%");
            });
        }

        $customers = $query->get();

        return view('main.orders.khach_mua_giong', compact('customers', 'search'));
    }
}
