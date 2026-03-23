<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SuccessOrderController extends Controller
{
    public function index()
    {
        return view('main.success-orders.index');
    }

    public function fetchData(Request $request)
    {
        $ngay = $request->input('Ngay', now()->format('d/m/Y') . ' - ' . now()->format('d/m/Y'));

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://thuysansg.com/webhook/xem-don-thanh-cong',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'Ngay' => $ngay,
            ],
        ]);
        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($httpCode !== 200 || !$response) {
            return response()->json(['orders' => [], 'error' => 'Không thể kết nối']);
        }

        $orders = json_decode($response, false) ?: [];

        // Check MaDH tồn tại trong DB donhang
        if (count($orders) > 0) {
            $maDHList = array_map(fn($o) => $o->MaDH ?? '', (array) $orders);
            $maDHList = array_filter($maDHList);
            $existingMaDH = DB::table('donhang')->whereIn('MaDH', $maDHList)->pluck('MaDH')->toArray();

            foreach ($orders as $order) {
                $order->existsInDb = in_array($order->MaDH ?? '', $existingMaDH);
            }
        }

        return response()->json([
            'orders' => $orders,
        ]);
    }
}
