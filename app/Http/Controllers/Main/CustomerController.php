<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    public function categories()
    {
        abort_unless(auth()->user()->can('Admin'), 403);

        $groups = DB::table('nhomkhachhang')->get();

        // Count data usage for each group
        foreach ($groups as $g) {
            $g->dataCount = DB::table('data')->where('MaNhomKH', $g->MaNhomKH)->count();
        }

        return view('main.customers.categories', compact('groups'));
    }

    public function storeGroup(Request $request)
    {
        $request->validate([
            'TenNhomKH' => 'required|string|max:255',
            'background' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:100',
        ]);

        DB::table('nhomkhachhang')->insert([
            'TenNhomKH' => $request->TenNhomKH,
            'background' => $request->background ?? '#3b82f6',
            'color' => $request->color ?? 'white',
        ]);

        return redirect()->route('customers.categories')->with('success', 'Đã thêm nhóm khách hàng.');
    }

    public function updateGroup(Request $request, $id)
    {
        $request->validate([
            'TenNhomKH' => 'required|string|max:255',
            'background' => 'nullable|string|max:100',
            'color' => 'nullable|string|max:100',
        ]);

        DB::table('nhomkhachhang')->where('MaNhomKH', $id)->update([
            'TenNhomKH' => $request->TenNhomKH,
            'background' => $request->background ?? '#3b82f6',
            'color' => $request->color ?? 'white',
        ]);

        return redirect()->route('customers.categories')->with('success', 'Đã cập nhật nhóm khách hàng.');
    }

    public function destroyGroup($id)
    {
        $count = DB::table('data')->where('MaNhomKH', $id)->count();
        if ($count > 0) {
            return redirect()->route('customers.categories')->with('error', "Không thể xóa nhóm này vì còn $count khách hàng thuộc nhóm.");
        }

        DB::table('nhomkhachhang')->where('MaNhomKH', $id)->delete();
        return redirect()->route('customers.categories')->with('success', 'Đã xóa nhóm khách hàng.');
    }

    public function callSettings()
    {
        abort_unless(auth()->user()->can('Admin'), 403);

        $phones = DB::table('dienthoai as dt')
            ->leftJoin('users as u', 'u.id', '=', 'dt.MaNV')
            ->select('dt.*', 'u.name as userName')
            ->orderBy('dt.extension')
            ->get();

        $staffList = DB::table('users')
            ->where('TinhTrang', 'Active')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        $callSettings = DB::table('call_setting')->get();

        return view('main.customers.call-settings', compact('phones', 'staffList', 'callSettings'));
    }

    public function storePhone(Request $request)
    {
        $request->validate([
            'extension' => 'required|string|max:10',
            'password' => 'required|string|max:100',
            'DauSo' => 'nullable|string|max:20',
            'MaNV' => 'nullable',
        ]);

        DB::table('dienthoai')->insert([
            'extension' => $request->extension,
            'password' => $request->password,
            'MaNV' => $request->MaNV ?: null,
            'DauSo' => $request->DauSo ?: null,
        ]);

        return redirect()->route('customers.callSettings')->with('success', 'Đã thêm máy nhánh ' . $request->extension);
    }

    public function updatePhone(Request $request, $id)
    {
        $request->validate([
            'extension' => 'required|string|max:10',
            'password' => 'required|string|max:100',
            'DauSo' => 'nullable|string|max:20',
            'MaNV' => 'nullable',
        ]);

        DB::table('dienthoai')->where('id', $id)->update([
            'extension' => $request->extension,
            'password' => $request->password,
            'MaNV' => $request->MaNV ?: null,
            'DauSo' => $request->DauSo ?: null,
        ]);

        return redirect()->route('customers.callSettings')->with('success', 'Đã cập nhật máy nhánh ' . $request->extension);
    }

    public function destroyPhone($id)
    {
        $phone = DB::table('dienthoai')->where('id', $id)->first();
        DB::table('dienthoai')->where('id', $id)->delete();
        return redirect()->route('customers.callSettings')->with('success', 'Đã xóa máy nhánh ' . ($phone->extension ?? ''));
    }

    public function storeCallSetting(Request $request)
    {
        $request->validate([
            'sockets' => 'required|string|max:500',
            'uri' => 'required|string|max:500',
            'call_duration' => 'required|integer|min:1|max:60',
        ]);

        DB::table('call_setting')->insert([
            'sockets' => $request->sockets,
            'uri' => $request->uri,
            'call_duration' => $request->call_duration,
        ]);

        return redirect()->route('customers.callSettings')->with('success', 'Đã thêm cài đặt cuộc gọi.');
    }

    public function updateCallSetting(Request $request, $id)
    {
        $request->validate([
            'sockets' => 'required|string|max:500',
            'uri' => 'required|string|max:500',
            'call_duration' => 'required|integer|min:1|max:60',
        ]);

        DB::table('call_setting')->where('id', $id)->update([
            'sockets' => $request->sockets,
            'uri' => $request->uri,
            'call_duration' => $request->call_duration,
        ]);

        return redirect()->route('customers.callSettings')->with('success', 'Đã cập nhật cài đặt cuộc gọi.');
    }

    public function destroyCallSetting($id)
    {
        DB::table('call_setting')->where('id', $id)->delete();
        return redirect()->route('customers.callSettings')->with('success', 'Đã xóa cài đặt cuộc gọi.');
    }

    public function dataDivision()
    {
        abort_unless(auth()->user()->can('Admin'), 403);

        $groups = DB::table('nhomkhachhang')->get();

        // Nhân viên phòng kinh doanh (cả Active + Deactive)
        $salesStaff = DB::table('users as u')
            ->join('model_has_permissions as mhp', 'mhp.model_id', '=', 'u.id')
            ->join('permissions as p', 'p.id', '=', 'mhp.permission_id')
            ->where('p.name', 'Sale')
            ->select('u.id', 'u.name', 'u.TinhTrang')
            ->orderBy('u.name')
            ->get();

        // Thống kê data trắng (MaNV IS NULL) theo nhóm KH
        $blankStats = DB::table('data')
            ->whereNull('MaNV')
            ->select('MaNhomKH', DB::raw('COUNT(*) as cnt'))
            ->groupBy('MaNhomKH')
            ->pluck('cnt', 'MaNhomKH');
        $totalBlank = DB::table('data')->whereNull('MaNV')->count();

        // Thống kê data theo từng nhân viên, phân loại nhóm KH
        $staffStats = DB::table('data')
            ->whereNotNull('MaNV')
            ->select('MaNV', 'MaNhomKH', DB::raw('COUNT(*) as cnt'))
            ->groupBy('MaNV', 'MaNhomKH')
            ->get()
            ->groupBy('MaNV');

        return view('main.customers.data-division', compact('groups', 'salesStaff', 'blankStats', 'totalBlank', 'staffStats'));
    }

    // API: Lấy thống kê data theo nhân viên
    public function dataDivisionStats(Request $request)
    {
        abort_unless(auth()->user()->can('Admin'), 403);

        $staffId = $request->input('staff_id');
        if (!$staffId) {
            return response()->json(['success' => false, 'message' => 'Thiếu staff_id']);
        }

        $stats = DB::table('data')
            ->where('MaNV', $staffId)
            ->select('MaNhomKH', DB::raw('COUNT(*) as cnt'))
            ->groupBy('MaNhomKH')
            ->get();

        $total = DB::table('data')->where('MaNV', $staffId)->count();

        return response()->json(['success' => true, 'stats' => $stats, 'total' => $total]);
    }

    // API: Thu hồi data
    public function recallData(Request $request)
    {
        abort_unless(auth()->user()->can('Admin'), 403);

        $request->validate([
            'staff_id' => 'required|integer',
            'group_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'mode' => 'required|in:reset,keep',
        ]);

        $staffId = $request->staff_id;
        $groupId = $request->group_id;
        $quantity = $request->quantity;
        $mode = $request->mode;

        // Lấy danh sách data cần thu hồi
        $records = DB::table('data')
            ->where('MaNV', $staffId)
            ->where('MaNhomKH', $groupId)
            ->limit($quantity)
            ->pluck('MaKH');

        if ($records->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không có data để thu hồi.']);
        }

        if ($mode === 'reset') {
            // Lấy SĐT trước khi reset để xóa call_log
            $phones = DB::table('data')->whereIn('MaKH', $records)->pluck('SoDienThoai');

            DB::table('data')->whereIn('MaKH', $records)->update([
                'MaNV' => null,
                'SoLanMua' => 0,
                'call_note' => '',
                'call_count' => 0,
                'No_Potential' => 0,
                'MaNhomKH' => 1,
            ]);

            // Xóa call_log theo SĐT
            if ($phones->isNotEmpty()) {
                DB::table('call_log')->whereIn('SoDienThoai', $phones)->delete();
            }
        } else {
            // Giữ nguyên: chỉ đổi MaNV thành null
            DB::table('data')->whereIn('MaKH', $records)->update([
                'MaNV' => null,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã thu hồi ' . $records->count() . ' data thành công.',
            'count' => $records->count(),
        ]);
    }

    // API: Chia data
    public function distributeData(Request $request)
    {
        abort_unless(auth()->user()->can('Admin'), 403);

        $request->validate([
            'staff_id' => 'required|integer',
            'group_id' => 'required|integer',
            'quantity' => 'required|integer|min:1',
            'mode' => 'required|in:reset,keep',
        ]);

        $staffId = $request->staff_id;
        $groupId = $request->group_id;
        $quantity = $request->quantity;
        $mode = $request->mode;

        // Lấy danh sách data trắng theo nhóm
        $records = DB::table('data')
            ->whereNull('MaNV')
            ->where('MaNhomKH', $groupId)
            ->limit($quantity)
            ->pluck('MaKH');

        if ($records->isEmpty()) {
            return response()->json(['success' => false, 'message' => 'Không còn data trắng trong nhóm này.']);
        }

        if ($mode === 'reset') {
            // Lấy SĐT trước khi reset để xóa call_log
            $phones = DB::table('data')->whereIn('MaKH', $records)->pluck('SoDienThoai');

            DB::table('data')->whereIn('MaKH', $records)->update([
                'MaNV' => $staffId,
                'SoLanMua' => 0,
                'call_note' => '',
                'call_count' => 0,
                'No_Potential' => 0,
                'MaNhomKH' => 1,
            ]);

            // Xóa call_log theo SĐT
            if ($phones->isNotEmpty()) {
                DB::table('call_log')->whereIn('SoDienThoai', $phones)->delete();
            }
        } else {
            // Giữ nguyên: chỉ đổi MaNV
            DB::table('data')->whereIn('MaKH', $records)->update([
                'MaNV' => $staffId,
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Đã chia ' . $records->count() . ' data thành công.',
            'count' => $records->count(),
        ]);
    }

    public function list(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->can('Admin');

        $staffList = DB::table('users')
            ->where('TinhTrang', 'Active')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        if ($isAdmin) {
            $staffFilter = $request->input('staff', $staffList->first()->id ?? null);
        } else {
            $staffFilter = $user->id;
        }

        $groupFilter = $request->input('group');
        $groups = DB::table('nhomkhachhang')->get();

        // Group counts based on active staff filter
        $countQuery = DB::table('data')->where('MaNV', $staffFilter);
        $groupCounts = (clone $countQuery)
            ->select('MaNhomKH', DB::raw('COUNT(*) as cnt'))
            ->groupBy('MaNhomKH')
            ->pluck('cnt', 'MaNhomKH');
        $totalCount = (clone $countQuery)->count();

        // SIP call config
        $callSetting = DB::table('call_setting')->first();
        $userPhone = DB::table('dienthoai')->where('MaNV', $user->id)->first();

        // Danh sách tỉnh thành cho select2
        $provinces = DB::table('diachinh')->select('Tinh')->distinct()->orderBy('Tinh')->pluck('Tinh');

        return view('main.customers.list', compact(
            'groups', 'groupCounts', 'totalCount',
            'staffList', 'groupFilter', 'staffFilter', 'isAdmin',
            'callSetting', 'userPhone', 'provinces'
        ));
    }

    public function listData(Request $request)
    {
        $user = auth()->user();
        $isAdmin = $user->can('Admin');

        $staffList = DB::table('users')
            ->where('TinhTrang', 'Active')
            ->select('id')
            ->orderBy('name')
            ->get();

        if ($isAdmin) {
            $staffFilter = $request->input('staff', $staffList->first()->id ?? null);
        } else {
            $staffFilter = $user->id;
        }

        $groupFilter = $request->input('group');

        $query = DB::table('data as d')
            ->leftJoin('users as u', 'u.id', '=', 'd.MaNV')
            ->leftJoin('nhomkhachhang as nkh', 'nkh.MaNhomKH', '=', 'd.MaNhomKH')
            ->select(
                'd.MaKH', 'd.TenKH', 'd.SoDienThoai', 'd.Tinh',
                'd.call_note', 'd.lastcall', 'd.call_count',
                'd.MaNhomKH', 'd.DiaChi',
                'u.name as telesaleName',
                'nkh.TenNhomKH', 'nkh.background as nhomBg', 'nkh.color as nhomColor'
            )
            ->where('d.MaNV', $staffFilter);

        if ($groupFilter) {
            $query->where('d.MaNhomKH', $groupFilter);
        }

        // DataTables server-side parameters
        $draw = intval($request->input('draw', 1));
        $start = intval($request->input('start', 0));
        $length = intval($request->input('length', 25));
        $search = $request->input('search.value', '');

        // Total before search
        $totalRecords = (clone $query)->count();

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('d.TenKH', 'LIKE', "%$search%")
                  ->orWhere('d.SoDienThoai', 'LIKE', "%$search%")
                  ->orWhere('d.Tinh', 'LIKE', "%$search%")
                  ->orWhere('d.call_note', 'LIKE', "%$search%");
            });
        }

        $filteredRecords = (clone $query)->count();

        // Ordering
        $orderCol = intval($request->input('order.0.column', 6));
        $orderDir = $request->input('order.0.dir', 'desc');
        $columns = ['TenKH', 'SoDienThoai', 'TenNhomKH', 'telesaleName', 'call_note', 'Tinh', 'lastcall', 'call_count'];
        $orderField = $columns[$orderCol] ?? 'lastcall';
        // Prefix with table alias for ambiguous columns
        if (in_array($orderField, ['TenKH','SoDienThoai','Tinh','call_note','lastcall','call_count'])) {
            $orderField = 'd.' . $orderField;
        }
        $query->orderBy($orderField, $orderDir);
        $query->offset($start)->limit($length);

        $rows = $query->get();
        $data = [];
        $now = \Carbon\Carbon::now();
        foreach ($rows as $r) {
            $nhomHtml = $r->TenNhomKH
                ? '<span style="display:inline-block;padding:3px 10px;border-radius:4px;font-size:12px;font-weight:700;background:'.$r->nhomBg.';color:'.$r->nhomColor.';">'.$r->TenNhomKH.'</span>'
                : '—';
            $lastcall = $r->lastcall ? \Carbon\Carbon::parse($r->lastcall)->format('d/m') : '';

            // Mask phone number for non-admin users (e.g., 0964***431)
            $phoneDisplay = $r->SoDienThoai;
            if (!$isAdmin && $r->SoDienThoai && mb_strlen($r->SoDienThoai) >= 7) {
                $phoneDisplay = mb_substr($r->SoDienThoai, 0, 4) . '***' . mb_substr($r->SoDienThoai, 7);
            }

            // Warning icon for stale calls
            $nameHtml = htmlspecialchars($r->TenKH ?: '—');
            if ($r->lastcall) {
                $lastDate = new \DateTime($r->lastcall);
                $nowDate = new \DateTime();
                $daysDiff = (int) $nowDate->diff($lastDate)->days;
                if ($daysDiff > 15) {
                    $nameHtml = '<span class="cl-warn" title="Quá '.$daysDiff.' ngày"><i class="fa-solid fa-triangle-exclamation"></i> <b>'.$daysDiff.'</b></span> ' . $nameHtml;
                }
            }

            // Action buttons
            $rowJson = htmlspecialchars(json_encode([
                'MaKH' => $r->MaKH, 'TenKH' => $r->TenKH, 'SoDienThoai' => $r->SoDienThoai,
                'Tinh' => $r->Tinh, 'MaNhomKH' => $r->MaNhomKH, 'call_note' => $r->call_note,
            ]), ENT_QUOTES);
            $ttHtml = '<button class="cl-btn cl-btn-update" onclick=\'openUpdate('.$rowJson.')\' title="Cập nhật"><i class="fa-solid fa-arrows-rotate"></i></button>';
            $ttHtml .= ' <button class="cl-btn cl-btn-history" onclick="openHistory(\''.$r->SoDienThoai.'\',\''.addslashes($r->TenKH).'\')" title="Lịch sử"><i class="fa-solid fa-clock-rotate-left"></i></button>';

            $data[] = [
                $nameHtml,
                '<a href="javascript:void(0)" onclick="makeCall(\''.$r->SoDienThoai.'\',\''.addslashes($r->TenKH ?: '').'\' )" style="text-decoration:none;cursor:pointer;"><i class="fa-solid fa-phone" style="color:#22c55e;font-size:12px;"></i> <b style="color:#1e293b;">'.$phoneDisplay.'</b></a>',
                $nhomHtml,
                $r->telesaleName ?: '—',
                '<div style="max-width:200px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="'.htmlspecialchars($r->call_note ?? '').'">'.htmlspecialchars($r->call_note ?? '').'</div>',
                '<b>'.($r->Tinh ?: '').'</b>',
                $lastcall,
                $r->call_count ?? 0,
                $ttHtml,
            ];
        }

        return response()->json([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data,
        ]);
    }

    public function updateCustomer(Request $request, $id)
    {
        $request->validate([
            'TenKH' => 'required|string|max:100',
            'SoDienThoai' => 'required|string|max:50',
            'Tinh' => 'nullable|string|max:100',
            'MaNhomKH' => 'nullable|integer',
            'call_note' => 'nullable|string',
        ]);

        $updateData = [
            'TenKH' => $request->TenKH,
            'SoDienThoai' => $request->SoDienThoai,
            'Tinh' => $request->Tinh,
            'MaNhomKH' => $request->MaNhomKH,
        ];

        // If note is provided, update call_note + lastcall and insert into call_log
        if ($request->filled('call_note')) {
            $updateData['call_note'] = $request->call_note;
            $updateData['lastcall'] = now();
            $updateData['call_count'] = DB::raw('IFNULL(call_count, 0) + 1');

            DB::table('call_log')->insert([
                'SoDienThoai' => $request->SoDienThoai,
                'MaNV' => auth()->id(),
                'call_note' => $request->call_note,
                'time' => now(),
            ]);
        }

        DB::table('data')->where('MaKH', $id)->update($updateData);

        return response()->json(['success' => true, 'message' => 'Đã cập nhật thông tin khách hàng.']);
    }

    public function customerHistory(Request $request)
    {
        $phone = $request->input('phone');
        $logs = DB::table('call_log as cl')
            ->leftJoin('users as u', 'u.id', '=', 'cl.MaNV')
            ->where('cl.SoDienThoai', $phone)
            ->select('cl.id', 'cl.call_note', 'cl.time', 'u.name as staffName')
            ->orderByDesc('cl.time')
            ->limit(100)
            ->get();

        return response()->json(['data' => $logs]);
    }

    public function deleteHistory($id)
    {
        DB::table('call_log')->where('id', $id)->delete();
        return response()->json(['success' => true, 'message' => 'Đã xóa lịch sử liên hệ.']);
    }

    // Thêm khách hàng mới (Admin only)
    public function storeCustomer(Request $request)
    {
        $request->validate([
            'TenKH' => 'required|string|max:255',
            'SoDienThoai' => 'required|string|max:20',
            'MaNV' => 'required|integer',
        ]);

        // Check trùng SĐT
        $exists = DB::table('data')->where('SoDienThoai', $request->SoDienThoai)->first();
        if ($exists) {
            return response()->json(['success' => false, 'message' => 'Số điện thoại đã tồn tại!']);
        }

        DB::table('data')->insert([
            'TenKH' => $request->TenKH,
            'SoDienThoai' => $request->SoDienThoai,
            'Tinh' => $request->Tinh ?? '',
            'MaNhomKH' => $request->MaNhomKH ?? null,
            'MaNV' => $request->MaNV,
        ]);

        return response()->json(['success' => true, 'message' => 'Thêm khách hàng thành công!']);
    }

    public function callHistory()
    {
        $user = auth()->user();
        $canManage = $user->can('Admin') || $user->can('Sale Manager') || $user->can('Nhân Sự');

        $phones = DB::table('dienthoai as dt')
            ->leftJoin('users as u', 'u.id', '=', 'dt.MaNV')
            ->select('dt.extension', 'u.name as userName', 'dt.MaNV')
            ->get();

        // Extension của user hiện tại (nếu không có quyền quản lý)
        $userExtension = '';
        if (!$canManage) {
            $userPhone = $phones->firstWhere('MaNV', $user->id);
            $userExtension = $userPhone ? $userPhone->extension : '';
        }

        return view('main.customers.call-history', compact('phones', 'canManage', 'userExtension'));
    }

    // API: Lấy lịch sử cuộc gọi từ eTelecom
    public function callHistoryData(Request $request)
    {
        $token = "shop1415713146516250402:99jzsRpRvE92w0et2KThHscAZeSL4Mg1PMzeT11h74dectynmD4rvPmvVJjwGCRJ";
        $thoigian = $request->thoigian;
        $extension = $request->extension ?? '';
        $call_type = $request->call_type ?? '';
        $call_state = $request->call_state ?? '';
        $before = $request->before ?? '';
        $after = $request->after ?? '';

        $tachthoigian = explode(" - ", $thoigian);
        if (count($tachthoigian) < 2) {
            return response()->json(['call_logs' => [], 'paging' => []]);
        }

        $date_start = $this->xulythoigian($tachthoigian[0]) . "T00:00:00.000Z";
        $date_end = $this->xulythoigian($tachthoigian[1]) . "T23:59:59.000Z";

        $filter = [
            'date_from' => $date_start,
            'date_to' => $date_end,
        ];
        if ($extension) $filter['call_number'] = $extension;
        if ($call_type) $filter['direction'] = $call_type;
        if ($call_state) $filter['call_state'] = $call_state;

        $postData = json_encode([
            'filter' => $filter,
            'paging' => [
                'limit' => 100,
                'after' => $after,
                'before' => $before,
            ],
        ]);

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.etelecom.vn/v1/partner.Etelecom/ListCallLogs',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $postData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token,
            ],
        ]);

        $response = curl_exec($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($response === false || $httpCode >= 400) {
            return response()->json(['call_logs' => [], 'paging' => [], 'error' => 'API error']);
        }

        $data = json_decode($response, true);
        $result = $data ?: ['call_logs' => [], 'paging' => []];

        $result['debug_postdata'] = $postData;

        // Map extension -> userName để frontend hiển thị Telesale
        $phones = DB::table('dienthoai as dt')
            ->leftJoin('users as u', 'u.id', '=', 'dt.MaNV')
            ->select('dt.extension', 'u.name as userName')
            ->get();
        $extensionMap = [];
        foreach ($phones as $p) {
            $extensionMap[$p->extension] = $p->userName ?? '';
        }
        $result['extension_map'] = $extensionMap;

        return response()->json($result);
    }

    private function xulythoigian($dateStr)
    {
        // Convert dd/mm/yyyy -> yyyy-mm-dd
        $parts = explode('/', trim($dateStr));
        if (count($parts) === 3) {
            return $parts[2] . '-' . $parts[1] . '-' . $parts[0];
        }
        return $dateStr;
    }

    // API: Tra cứu khách hàng theo số điện thoại (cho cuộc gọi đến)
    public function lookupPhone(Request $request)
    {
        $phone = $request->input('phone', '');
        if (!$phone) {
            return response()->json(['found' => false]);
        }

        // Tìm trong bảng data
        $customer = DB::table('data as d')
            ->leftJoin('users as u', 'u.id', '=', 'd.MaNV')
            ->where('d.SoDienThoai', $phone)
            ->select('d.TenKH', 'd.SoDienThoai', 'u.name as staffName')
            ->first();

        if ($customer) {
            return response()->json([
                'found' => true,
                'TenKH' => $customer->TenKH ?: 'Không rõ',
                'SoDienThoai' => $customer->SoDienThoai,
                'staffName' => $customer->staffName ?: '',
            ]);
        }

        return response()->json(['found' => false, 'SoDienThoai' => $phone]);
    }

    // API: Tìm kiếm data khách hàng (theo SĐT hoặc tên)
    public function searchData(Request $request)
    {
        $keyword = trim($request->input('keyword', ''));
        if (!$keyword || mb_strlen($keyword) < 3) {
            return response()->json(['found' => false, 'message' => 'Nhập ít nhất 3 ký tự']);
        }

        // Chuẩn hoá SĐT: bỏ dấu cách
        $cleanKeyword = str_replace(' ', '', $keyword);

        $results = DB::table('data as d')
            ->leftJoin('users as u', 'u.id', '=', 'd.MaNV')
            ->leftJoin('nhomkhachhang as nkh', 'nkh.MaNhomKH', '=', 'd.MaNhomKH')
            ->where(function($q) use ($cleanKeyword, $keyword) {
                $q->where('d.SoDienThoai', 'LIKE', "%{$cleanKeyword}%")
                  ->orWhere('d.TenKH', 'LIKE', "%{$keyword}%");
            })
            ->select(
                'd.TenKH', 'd.SoDienThoai', 'd.MaNhomKH',
                'u.name as staffName',
                'nkh.TenNhomKH'
            )
            ->limit(20)
            ->get();

        if ($results->isEmpty()) {
            return response()->json(['found' => false, 'message' => 'Không tìm thấy']);
        }

        return response()->json([
            'found' => true,
            'data' => $results,
        ]);
    }
}
