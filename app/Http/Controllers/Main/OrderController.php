<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        // Mặc định lọc theo tháng hiện tại nếu không có filter ngày
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');
        $maNV = $request->input('manv', '');

        if (!$dateFrom && !$dateTo && !$request->has('date_from')) {
            $dateFrom = '01/' . now()->format('m/Y');
            $dateTo = now()->format('d/m/Y');
        }

        $query = DB::table('donhang as dh')
            ->leftJoin('khachhang as kh', 'kh.MaDH', '=', 'dh.MaDH')
            ->leftJoin('users as nv', 'nv.id', '=', 'dh.MaNV')
            ->leftJoin(DB::raw("(SELECT ct2.MaDH, GROUP_CONCAT(CONCAT(sp.TenSP, ' : ', CAST(ct2.SoLuong AS UNSIGNED)) SEPARATOR '\n') as ChiTietSP FROM chitietdonhang ct2 LEFT JOIN sanpham sp ON sp.MaSP = ct2.MaSP GROUP BY ct2.MaDH) as ct"), 'ct.MaDH', '=', 'dh.MaDH')
            ->select(
                'dh.id', 'dh.MaDH', 'dh.MaNV', 'dh.TongTien', 'dh.GiamGia',
                'dh.Ngay', 'dh.DonHang',
                'kh.TenKH', 'kh.SoDienThoai', 'kh.DiaChi', 'kh.Tinh', 'kh.Huyen', 'kh.Xa',
                'nv.name as TenNV',
                'ct.ChiTietSP'
            );

        if (!in_array($user->Permission, ['Admin', 'Kế Toán'])) {
            $query->where('dh.MaNV', $user->id);
        }

        if ($dateFrom) {
            $parts = explode('/', $dateFrom);
            if (count($parts) == 3) $query->where('dh.Ngay', '>=', "{$parts[2]}-{$parts[1]}-{$parts[0]}");
        }
        if ($dateTo) {
            $parts = explode('/', $dateTo);
            if (count($parts) == 3) $query->where('dh.Ngay', '<=', "{$parts[2]}-{$parts[1]}-{$parts[0]}");
        }
        if ($maNV) $query->where('dh.MaNV', $maNV);

        $orders = $query->orderByDesc('dh.Ngay')->orderByDesc('dh.id')->get();

        $employees = DB::table('users')->where('TinhTrang', 'Đang Làm Việc')->get(['id', 'name']);
        $products = DB::table('sanpham')->get(['MaSP', 'TenSP', 'GiaBan_SG']);
        $dvghs = DB::table('donvigiaohang')->get();

        return view('main.orders.index', compact(
            'orders', 'dateFrom', 'dateTo', 'maNV',
            'employees', 'products', 'dvghs', 'user'
        ));
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        $lastOrder = DB::table('donhang')->orderByDesc('id')->first();
        $newId = $lastOrder ? ($lastOrder->id + 1) : 1;
        $maDH = 'DH' . str_pad($newId, 6, '0', STR_PAD_LEFT);

        $items = $request->input('items', []);
        $totalPrice = 0;
        foreach ($items as $item) {
            if (empty($item['MaSP'])) continue;
            $sp = DB::table('sanpham')->where('MaSP', $item['MaSP'])->first();
            $giaBan = !empty($item['GiaBan']) ? $item['GiaBan'] : ($sp->GiaBan_SG ?? 0);
            $totalPrice += $giaBan * ($item['SoLuong'] ?? 1);
        }

        $giamGia = $request->input('GiamGia', 0);

        DB::table('donhang')->insertGetId([
            'MaDH' => $maDH,
            'MaNV' => $user->id,
            'TongTien' => $totalPrice - $giamGia,
            'GiamGia' => $giamGia,
            'Ngay' => now()->format('Y-m-d'),
            'DonHang' => $request->input('GhiChu', ''),
        ]);

        DB::table('khachhang')->insert([
            'MaDH' => $maDH,
            'TenKH' => $request->input('TenKH', ''),
            'SoDienThoai' => $request->input('SoDienThoai', ''),
            'DiaChi' => $request->input('DiaChi', ''),
            'Tinh' => $request->input('Tinh', ''),
            'Huyen' => $request->input('Huyen', ''),
            'Xa' => $request->input('Xa', ''),
            'SoLanMua' => 1,
        ]);

        foreach ($items as $item) {
            if (empty($item['MaSP'])) continue;
            $sp = DB::table('sanpham')->where('MaSP', $item['MaSP'])->first();
            $giaBan = !empty($item['GiaBan']) ? $item['GiaBan'] : ($sp->GiaBan_SG ?? 0);
            DB::table('chitietdonhang')->insert([
                'MaDH' => $maDH,
                'MaSP' => $item['MaSP'],
                'SoLuong' => $item['SoLuong'] ?? 1,
                'GiaNhap' => $sp->GiaNhap ?? 0,
                'GiaBan' => $giaBan,
                'NhomSanPham' => $sp->NhomSP ?? null,
                'NgayBan' => now()->format('Y-m-d'),
            ]);
        }

        return redirect()->route('orders.index')->with('success', 'Tạo đơn hàng thành công!');
    }

    public function editData($id)
    {
        $order = DB::table('donhang as dh')
            ->leftJoin('khachhang as kh', 'kh.MaDH', '=', 'dh.MaDH')
            ->where('dh.id', $id)
            ->select('dh.*', 'kh.TenKH', 'kh.SoDienThoai', 'kh.DiaChi', 'kh.Tinh', 'kh.Huyen', 'kh.Xa')
            ->first();

        $items = DB::table('chitietdonhang as ct')
            ->leftJoin('sanpham as sp', 'sp.MaSP', '=', 'ct.MaSP')
            ->where('ct.MaDH', $order->MaDH)
            ->select('ct.*', 'sp.TenSP')
            ->get();

        // Lấy sản phẩm kèm tồn kho
        $products = DB::table('sanpham as sp')
            ->leftJoin('quanlysanpham as q', 'q.MaSP', '=', 'sp.MaSP')
            ->select('sp.MaSP', 'sp.TenSP', 'sp.GiaBan_SG', 'sp.GiaNhap', DB::raw('COALESCE(CAST(q.SoLuong AS SIGNED), 0) as TonKho'))
            ->get();

        // Tính tồn kho khả dụng = tồn hiện tại + SP cũ (vì khi sửa sẽ trả lại kho)
        $oldItemsMap = [];
        foreach ($items as $it) {
            $oldItemsMap[$it->MaSP] = ($oldItemsMap[$it->MaSP] ?? 0) + (int) $it->SoLuong;
        }
        foreach ($products as $p) {
            $p->TonKhaDung = (int) $p->TonKho + ($oldItemsMap[$p->MaSP] ?? 0);
        }

        $order->items = $items;
        $order->products = $products;
        return response()->json($order);
    }

    public function update(Request $request, $id)
    {
        $order = DB::table('donhang')->where('id', $id)->first();
        if (!$order) return response()->json(['success' => false, 'message' => 'Không tìm thấy đơn hàng']);

        // Lấy SP cũ để tính tồn kho khả dụng
        $oldItems = DB::table('chitietdonhang')->where('MaDH', $order->MaDH)->get();
        $oldItemsMap = [];
        foreach ($oldItems as $old) {
            $oldItemsMap[$old->MaSP] = ($oldItemsMap[$old->MaSP] ?? 0) + (int) $old->SoLuong;
        }

        // Kiểm tra tồn kho trước khi thực hiện
        $newItems = $request->input('items', []);
        foreach ($newItems as $item) {
            if (empty($item['MaSP'])) continue;
            $stock = DB::table('quanlysanpham')->where('MaSP', $item['MaSP'])->first();
            $currentStock = $stock ? (int) $stock->SoLuong : 0;
            $returnedFromOld = $oldItemsMap[$item['MaSP']] ?? 0;
            $available = $currentStock + $returnedFromOld;
            $needed = (int) ($item['SoLuong'] ?? 1);

            if ($needed > $available) {
                $sp = DB::table('sanpham')->where('MaSP', $item['MaSP'])->first();
                $tenSP = $sp->TenSP ?? $item['MaSP'];
                return response()->json([
                    'success' => false,
                    'message' => "Không đủ tồn kho cho {$tenSP}: cần {$needed}, khả dụng {$available}"
                ]);
            }
        }

        DB::beginTransaction();
        try {
            // 1. Bù lại tồn kho từ sản phẩm cũ
            foreach ($oldItems as $old) {
                $stock = DB::table('quanlysanpham')->where('MaSP', $old->MaSP)->first();
                if ($stock) {
                    DB::table('quanlysanpham')
                        ->where('MaSP', $old->MaSP)
                        ->update(['SoLuong' => $stock->SoLuong + $old->SoLuong]);
                } else {
                    // Tạo mới nếu chưa có trong bảng quanlysanpham
                    $sp = DB::table('sanpham')->where('MaSP', $old->MaSP)->first();
                    DB::table('quanlysanpham')->insert([
                        'MaSP' => $old->MaSP,
                        'TenSP' => $sp->TenSP ?? $old->MaSP,
                        'SoLuong' => $old->SoLuong,
                    ]);
                }
            }

            // 2. Xóa chi tiết đơn hàng cũ
            DB::table('chitietdonhang')->where('MaDH', $order->MaDH)->delete();

            // 3. Thêm sản phẩm mới + trừ tồn kho
            $tongGiaTriSP = 0;
            foreach ($newItems as $item) {
                if (empty($item['MaSP'])) continue;
                $sp = DB::table('sanpham')->where('MaSP', $item['MaSP'])->first();
                $giaBan = !empty($item['GiaBan']) ? (int) $item['GiaBan'] : ($sp->GiaBan_SG ?? 0);
                $soLuong = (int) ($item['SoLuong'] ?? 1);

                DB::table('chitietdonhang')->insert([
                    'MaDH' => $order->MaDH,
                    'MaSP' => $item['MaSP'],
                    'SoLuong' => $soLuong,
                    'GiaNhap' => $sp->GiaNhap ?? 0,
                    'GiaBan' => $giaBan,
                    'NgayBan' => $order->Ngay,
                ]);

                $tongGiaTriSP += $giaBan * $soLuong;

                // Trừ tồn kho
                $stock = DB::table('quanlysanpham')->where('MaSP', $item['MaSP'])->first();
                if ($stock) {
                    DB::table('quanlysanpham')
                        ->where('MaSP', $item['MaSP'])
                        ->update(['SoLuong' => max(0, $stock->SoLuong - $soLuong)]);
                }
            }

            // 4. Tính giảm giá: nếu tổng SP > TongTien -> chênh lệch = GiamGia
            $tongTien = (int) $order->TongTien;
            $giamGia = $tongGiaTriSP > $tongTien ? ($tongGiaTriSP - $tongTien) : 0;

            // 5. Cập nhật đơn hàng (TongTien không đổi)
            DB::table('donhang')->where('id', $id)->update([
                'GiamGia' => $giamGia,
                'DonHang' => $request->input('DonHang', $order->DonHang),
            ]);

            // 6. Cập nhật thông tin khách hàng
            DB::table('khachhang')->where('MaDH', $order->MaDH)->update([
                'TenKH' => $request->input('TenKH', ''),
                'SoDienThoai' => $request->input('SoDienThoai', ''),
                'DiaChi' => $request->input('DiaChi', ''),
            ]);

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Cập nhật thành công']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()]);
        }
    }

    public function destroy($id)
    {
        $user = Auth::user();
        if ($user->Permission !== 'Admin') {
            return response()->json(['success' => false, 'message' => 'Không có quyền']);
        }

        $order = DB::table('donhang')->where('id', $id)->first();
        if ($order) {
            // Lấy thông tin liên quan trước khi xóa
            $chiTiet = DB::table('chitietdonhang as ct')
                ->leftJoin('sanpham as sp', 'sp.MaSP', '=', 'ct.MaSP')
                ->where('ct.MaDH', $order->MaDH)
                ->select('ct.*', 'sp.TenSP')
                ->get();
            $khachHang = DB::table('khachhang')->where('MaDH', $order->MaDH)->first();
            $nhanVien = DB::table('users')->where('id', $order->MaNV)->first();

            // Lưu Activity Log
            DB::table('activity_log')->insert([
                'action' => 'delete',
                'MaDH' => $order->MaDH,
                'MaNV' => $order->MaNV,
                'TenNV' => $nhanVien->name ?? '',
                'TongTien' => $order->TongTien,
                'GiamGia' => $order->GiamGia ?? 0,
                'Ngay' => $order->Ngay,
                'DonHang' => $order->DonHang,
                'khach_hang_data' => $khachHang ? json_encode((array) $khachHang) : null,
                'chi_tiet_data' => json_encode($chiTiet->map(fn($ct) => (array) $ct)->toArray()),
                'deleted_by' => $user->name,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Bù lại tồn kho
            foreach ($chiTiet as $ct) {
                DB::table('quanlysanpham')
                    ->where('MaSP', $ct->MaSP)
                    ->increment('SoLuong', $ct->SoLuong);
            }

            DB::table('chitietdonhang')->where('MaDH', $order->MaDH)->delete();
            DB::table('khachhang')->where('MaDH', $order->MaDH)->delete();
            DB::table('donhang')->where('id', $id)->delete();
        }

        return response()->json(['success' => true, 'message' => 'Đã xóa đơn hàng']);
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $dateFrom = $request->input('date_from', '');
        $dateTo = $request->input('date_to', '');
        $maNV = $request->input('manv', '');

        if (!$dateFrom && !$dateTo) {
            $dateFrom = '01/' . now()->format('m/Y');
            $dateTo = now()->format('d/m/Y');
        }

        $query = DB::table('donhang as dh')
            ->leftJoin('khachhang as kh', 'kh.MaDH', '=', 'dh.MaDH')
            ->leftJoin('users as nv', 'nv.id', '=', 'dh.MaNV')
            ->leftJoin(DB::raw("(SELECT ct2.MaDH, GROUP_CONCAT(CONCAT(sp.TenSP, ' : ', CAST(ct2.SoLuong AS UNSIGNED)) SEPARATOR ', ') as ChiTietSP FROM chitietdonhang ct2 LEFT JOIN sanpham sp ON sp.MaSP = ct2.MaSP GROUP BY ct2.MaDH) as ct"), 'ct.MaDH', '=', 'dh.MaDH')
            ->select(
                'dh.MaDH', 'dh.Ngay', 'dh.TongTien', 'dh.GiamGia', 'dh.DonHang',
                'kh.TenKH', 'kh.SoDienThoai', 'kh.DiaChi', 'kh.Tinh', 'kh.Huyen', 'kh.Xa',
                'nv.name as TenNV', 'ct.ChiTietSP'
            );

        if (!in_array($user->Permission, ['Admin', 'Kế Toán'])) {
            $query->where('dh.MaNV', $user->id);
        }

        if ($dateFrom) {
            $parts = explode('/', $dateFrom);
            if (count($parts) == 3) $query->where('dh.Ngay', '>=', "{$parts[2]}-{$parts[1]}-{$parts[0]}");
        }
        if ($dateTo) {
            $parts = explode('/', $dateTo);
            if (count($parts) == 3) $query->where('dh.Ngay', '<=', "{$parts[2]}-{$parts[1]}-{$parts[0]}");
        }
        if ($maNV) $query->where('dh.MaNV', $maNV);

        $orders = $query->orderByDesc('dh.Ngay')->get();

        // Tạo file Excel
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Đơn Hàng');

        // Header row
        $headers = ['STT', 'MÃ ĐƠN HÀNG', 'TÊN NHÂN VIÊN', 'TÊN KHÁCH HÀNG', 'SỐ ĐIỆN THOẠI', 'ĐỊA CHỈ', 'XÃ', 'HUYỆN', 'TỈNH', 'SẢN PHẨM', 'GHI CHÚ', 'TỔNG TIỀN', 'TÌNH TRẠNG', 'THỜI GIAN'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '1', $h);
            $col++;
        }

        // Style header
        $headerRange = 'A1:N1';
        $sheet->getStyle($headerRange)->applyFromArray([
            'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER, 'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => '000000']]],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(25);

        // Data rows
        $row = 2;
        $stt = 1;
        foreach ($orders as $o) {
            $ngay = $o->Ngay ? \Carbon\Carbon::parse($o->Ngay)->format('Y-m-d H:i:s') : '';

            $sheet->setCellValue("A{$row}", $stt++);
            $sheet->setCellValue("B{$row}", $o->MaDH);
            $sheet->setCellValue("C{$row}", $o->TenNV ?? '');
            $sheet->setCellValue("D{$row}", $o->TenKH ?? '');
            $sheet->setCellValueExplicit("E{$row}", $o->SoDienThoai ?? '', \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);
            $sheet->setCellValue("F{$row}", $o->DiaChi ?? '');
            $sheet->setCellValue("G{$row}", $o->Xa ?? '');
            $sheet->setCellValue("H{$row}", $o->Huyen ?? '');
            $sheet->setCellValue("I{$row}", $o->Tinh ?? '');
            $sheet->setCellValue("J{$row}", $o->ChiTietSP ?? '');
            $sheet->setCellValue("K{$row}", $o->DonHang ?? '');
            $sheet->setCellValue("L{$row}", $o->TongTien ?? 0);
            $sheet->setCellValue("M{$row}", '');
            $sheet->setCellValue("N{$row}", $ngay);

            $row++;
        }

        // Style data area
        $lastRow = $row - 1;
        if ($lastRow >= 2) {
            $dataRange = "A2:N{$lastRow}";
            $sheet->getStyle($dataRange)->applyFromArray([
                'borders' => ['allBorders' => ['borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, 'color' => ['rgb' => 'D9D9D9']]],
                'alignment' => ['vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER],
            ]);
            // Format Tổng Tiền as number
            $sheet->getStyle("L2:L{$lastRow}")->getNumberFormat()->setFormatCode('#,##0');
        }

        // Auto-size columns
        foreach (range('A', 'N') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }

        // Output
        $filename = 'DonHang_' . now()->format('d-m-Y') . '.xlsx';
        $temp = tempnam(sys_get_temp_dir(), 'excel_');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save($temp);

        return response()->download($temp, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend(true);
    }
}
