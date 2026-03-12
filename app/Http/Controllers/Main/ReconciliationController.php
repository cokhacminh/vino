<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReconciliationController extends Controller
{
    public function index()
    {
        return view('main.reconciliation.index');
    }

    public function fetchData(Request $request)
    {
        $ngay = $request->input('ngay'); // dd/mm/yyyy
        if (!$ngay) {
            return response()->json(['success' => false, 'message' => 'Vui lòng chọn ngày']);
        }

        // Chuyển dd/mm/yyyy -> yyyy-mm-dd
        $parts = explode('/', $ngay);
        if (count($parts) !== 3) {
            return response()->json(['success' => false, 'message' => 'Ngày không hợp lệ']);
        }
        $dbDate = "{$parts[2]}-{$parts[1]}-{$parts[0]}";

        // Gửi curl
        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://thuysansg.com/webhook/doi-chieu',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => ['Ngay' => $dbDate],
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200 || !$response) {
            return response()->json(['success' => false, 'message' => 'Không thể kết nối đến server']);
        }

        $data = json_decode($response, true);
        if (!is_array($data)) {
            return response()->json(['success' => false, 'message' => 'Dữ liệu trả về không hợp lệ']);
        }

        // Lấy danh sách MaDH
        $maDHs = array_column($data, 'MaDH');

        // Check donhang tồn tại
        $existing = DB::table('donhang')
            ->whereIn('MaDH', $maDHs)
            ->select('MaDH', 'TongTien')
            ->get()
            ->keyBy('MaDH');

        // Gắn thêm thông tin đối chiếu
        foreach ($data as &$row) {
            $maDH = $row['MaDH'] ?? '';
            if (isset($existing[$maDH])) {
                $row['doiChieu'] = 'ok';
                $row['tongTienLocal'] = $existing[$maDH]->TongTien;
            } else {
                $row['doiChieu'] = 'missing';
                $row['tongTienLocal'] = null;
            }
        }

        return response()->json([
            'success' => true,
            'data' => $data,
            'total' => count($data),
            'matched' => count($existing),
            'missing' => count($data) - count($existing),
        ]);
    }
}
