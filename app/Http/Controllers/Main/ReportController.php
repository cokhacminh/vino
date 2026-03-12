<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ReportController extends Controller
{
    // Helper: kiểm tra Admin
    private function isAdmin()
    {
        $user = Auth::user();
        return $user && ($user->hasRole('Admin') || $user->can('Admin'));
    }

    // Helper: kiểm tra Trưởng Phòng
    private function isTruongPhong()
    {
        $user = Auth::user();
        return $user && !$this->isAdmin() && $user->can('Trưởng Phòng');
    }

    // Helper: kiểm tra có quyền quản lý (Admin hoặc Trưởng Phòng)
    private function canManage()
    {
        return $this->isAdmin() || $this->isTruongPhong();
    }

    /**
     * Trang chính Báo Cáo
     */
    public function index()
    {
        return view('main.reports.index');
    }

    /**
     * API: Lấy danh sách mẫu báo cáo (cho user hiện tại hoặc tất cả nếu admin)
     */
    public function getTemplates(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $this->isAdmin();
        $isTruongPhong = $this->isTruongPhong();

        if ($isAdmin) {
            $templates = DB::table('report_templates')->where('is_active', true)->orderBy('name')->get();
        } elseif ($isTruongPhong) {
            // Trưởng Phòng: thấy mẫu PB mình + mẫu gán cho mình
            $assignedIds = DB::table('report_template_users')->where('user_id', $user->id)->pluck('template_id');
            $templates = DB::table('report_templates')
                ->where('is_active', true)
                ->where(function ($q) use ($user, $assignedIds) {
                    $q->where('MaPB', $user->MaPB)
                      ->orWhereIn('id', $assignedIds)
                      ->orWhereNotIn('id', DB::table('report_template_users')->distinct()->pluck('template_id'));
                })
                ->orderBy('name')->get();
        } else {
            $assignedIds = DB::table('report_template_users')->where('user_id', $user->id)->pluck('template_id');
            $templates = DB::table('report_templates')
                ->where('is_active', true)
                ->where(function ($q) use ($assignedIds) {
                    $q->whereIn('id', $assignedIds)
                      ->orWhereNotIn('id', DB::table('report_template_users')->distinct()->pluck('template_id'));
                })
                ->orderBy('name')->get();
        }

        foreach ($templates as $t) {
            $t->fields = DB::table('report_template_fields')
                ->where('template_id', $t->id)
                ->orderBy('sort_order')
                ->get();
            foreach ($t->fields as $f) {
                $f->options = $f->options ? json_decode($f->options) : null;
            }
            $t->schedule_config = $t->schedule_config ? json_decode($t->schedule_config) : null;
            if ($t->MaPB) {
                $t->phongban_name = DB::table('phongban')->where('MaPB', $t->MaPB)->value('TenPB');
            } else {
                $t->phongban_name = 'Tất cả';
            }
        }

        return response()->json([
            'success' => true,
            'templates' => $templates,
            'is_admin' => $isAdmin,
            'is_truong_phong' => $isTruongPhong,
        ]);
    }

    /**
     * API: Lấy tất cả mẫu (Admin only - cho quản lý mẫu)
     */
    public function getAllTemplates()
    {
        $user = Auth::user();
        if (!$this->canManage()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
        }

        $query = DB::table('report_templates');
        // Trưởng Phòng chỉ thấy mẫu PB mình
        if ($this->isTruongPhong()) {
            $query->where('MaPB', $user->MaPB);
        }
        $templates = $query->orderBy('name')->get();

        foreach ($templates as $t) {
            $t->fields = DB::table('report_template_fields')
                ->where('template_id', $t->id)
                ->orderBy('sort_order')
                ->get();
            foreach ($t->fields as $f) {
                $f->options = $f->options ? json_decode($f->options) : null;
            }
            $t->schedule_config = $t->schedule_config ? json_decode($t->schedule_config) : null;
            if ($t->MaPB) {
                $t->phongban_name = DB::table('phongban')->where('MaPB', $t->MaPB)->value('TenPB');
            } else {
                $t->phongban_name = 'Tất cả';
            }
            $t->user_ids = DB::table('report_template_users')->where('template_id', $t->id)->pluck('user_id');
        }

        $phongbans = DB::table('phongban')->where('TrangThai', 1)->orderBy('TenPB')->get(['MaPB', 'TenPB']);

        return response()->json([
            'success' => true,
            'templates' => $templates,
            'phongbans' => $phongbans,
        ]);
    }

    /**
     * API: Tạo mẫu báo cáo
     */
    public function storeTemplate(Request $request)
    {
        $user = Auth::user();
        if (!$this->canManage()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'fields' => 'required|array|min:1',
            'fields.*.label' => 'required|string|max:255',
            'fields.*.field_type' => 'required|in:text,textarea,number,select,checkbox,image',
        ]);

        // Trưởng Phòng chỉ tạo mẫu cho PB mình
        if ($this->isTruongPhong()) {
            $request->merge(['MaPB' => $user->MaPB]);
        }

        $templateId = DB::table('report_templates')->insertGetId([
            'name' => $request->name,
            'MaPB' => $request->MaPB,
            'type' => $request->type ?? 'daily',
            'schedule_config' => $request->schedule_config ? json_encode($request->schedule_config) : null,
            'is_active' => true,
            'created_by' => $user->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($request->fields as $i => $field) {
            DB::table('report_template_fields')->insert([
                'template_id' => $templateId,
                'label' => $field['label'],
                'field_type' => $field['field_type'],
                'options' => isset($field['options']) ? json_encode($field['options']) : null,
                'is_required' => $field['is_required'] ?? false,
                'sort_order' => $i,
            ]);
        }

        // Lưu danh sách nhân viên được gán
        if ($request->has('user_ids') && is_array($request->user_ids)) {
            foreach ($request->user_ids as $userId) {
                DB::table('report_template_users')->insert([
                    'template_id' => $templateId,
                    'user_id' => $userId,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Tạo mẫu thành công']);
    }

    /**
     * API: Cập nhật mẫu báo cáo
     */
    public function updateTemplate(Request $request, $id)
    {
        $user = Auth::user();
        if (!$this->canManage()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
        }
        // Trưởng Phòng chỉ sửa mẫu PB mình
        if ($this->isTruongPhong()) {
            $template = DB::table('report_templates')->find($id);
            if (!$template || $template->MaPB != $user->MaPB) {
                return response()->json(['success' => false, 'message' => 'Không có quyền sửa mẫu này'], 403);
            }
            $request->merge(['MaPB' => $user->MaPB]);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'fields' => 'required|array|min:1',
        ]);

        DB::table('report_templates')->where('id', $id)->update([
            'name' => $request->name,
            'MaPB' => $request->MaPB,
            'type' => $request->type ?? 'daily',
            'schedule_config' => $request->schedule_config ? json_encode($request->schedule_config) : null,
            'is_active' => $request->is_active ?? true,
            'updated_at' => now(),
        ]);

        // Delete old fields and re-insert
        DB::table('report_template_fields')->where('template_id', $id)->delete();
        foreach ($request->fields as $i => $field) {
            DB::table('report_template_fields')->insert([
                'template_id' => $id,
                'label' => $field['label'],
                'field_type' => $field['field_type'],
                'options' => isset($field['options']) ? json_encode($field['options']) : null,
                'is_required' => $field['is_required'] ?? false,
                'sort_order' => $i,
            ]);
        }

        // Cập nhật danh sách nhân viên được gán
        DB::table('report_template_users')->where('template_id', $id)->delete();
        if ($request->has('user_ids') && is_array($request->user_ids)) {
            foreach ($request->user_ids as $userId) {
                DB::table('report_template_users')->insert([
                    'template_id' => $id,
                    'user_id' => $userId,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Cập nhật mẫu thành công']);
    }

    /**
     * API: Xóa mẫu báo cáo
     */
    public function destroyTemplate($id)
    {
        $user = Auth::user();
        if (!$this->canManage()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
        }
        $template = DB::table('report_templates')->find($id);
        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy mẫu'], 404);
        }
        if ($this->isTruongPhong() && $template->created_by != $user->id) {
            return response()->json(['success' => false, 'message' => 'Bạn chỉ có thể xóa mẫu do chính mình tạo'], 403);
        }

        // Xóa dữ liệu liên quan trước (FK không có cascade)
        $reportIds = DB::table('daily_reports')->where('template_id', $id)->pluck('id');
        if ($reportIds->count()) {
            DB::table('daily_report_values')->whereIn('report_id', $reportIds)->delete();
            DB::table('daily_reports')->where('template_id', $id)->delete();
        }
        DB::table('report_template_fields')->where('template_id', $id)->delete();
        DB::table('report_template_users')->where('template_id', $id)->delete();
        DB::table('report_templates')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Xóa mẫu thành công']);
    }

    /**
     * API: Toggle active/deactive mẫu báo cáo
     */
    public function toggleActive($id)
    {
        $user = Auth::user();
        if (!$this->canManage()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
        }
        $template = DB::table('report_templates')->find($id);
        if (!$template) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy mẫu'], 404);
        }
        if ($this->isTruongPhong() && $template->created_by != $user->id) {
            return response()->json(['success' => false, 'message' => 'Bạn chỉ có thể thao tác mẫu do chính mình tạo'], 403);
        }

        $newStatus = !$template->is_active;
        DB::table('report_templates')->where('id', $id)->update(['is_active' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => $newStatus ? 'Đã kích hoạt mẫu' : 'Đã tắt mẫu',
            'is_active' => $newStatus,
        ]);
    }

    /**
     * API: Nộp / Cập nhật báo cáo
     */
    public function submitReport(Request $request)
    {
        $user = Auth::user();
        $templateId = $request->template_id;
        $reportDate = $request->report_date ?? now()->toDateString();
        $status = $request->status ?? 'submitted';

        // Check if already exists for this date
        $existing = DB::table('daily_reports')
            ->where('template_id', $templateId)
            ->where('user_id', $user->id)
            ->where('report_date', $reportDate)
            ->first();

        if ($existing) {
            // Update
            DB::table('daily_reports')->where('id', $existing->id)->update([
                'status' => $status,
                'updated_at' => now(),
            ]);
            $reportId = $existing->id;
            // Delete old values
            DB::table('daily_report_values')->where('report_id', $reportId)->delete();
        } else {
            // Insert
            $reportId = DB::table('daily_reports')->insertGetId([
                'template_id' => $templateId,
                'user_id' => $user->id,
                'report_date' => $reportDate,
                'status' => $status,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // Insert values
        if ($request->values && is_array($request->values)) {
            foreach ($request->values as $fieldId => $value) {
                DB::table('daily_report_values')->insert([
                    'report_id' => $reportId,
                    'field_id' => $fieldId,
                    'value' => is_array($value) ? json_encode($value) : $value,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => $existing ? 'Cập nhật báo cáo thành công' : 'Nộp báo cáo thành công']);
    }

    /**
     * API: Lấy báo cáo theo ngày (của user hiện tại hoặc tất cả nếu admin)
     */
    public function getReports(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $this->isAdmin();
        $isTruongPhong = $this->isTruongPhong();
        $date = $request->date ?? now()->toDateString();
        $templateId = $request->template_id;
        $userId = $request->user_id;

        $query = DB::table('daily_reports as dr')
            ->join('report_templates as rt', 'rt.id', '=', 'dr.template_id')
            ->leftJoin('users as u', 'u.id', '=', 'dr.user_id')
            ->leftJoin('users as rv', 'rv.id', '=', 'dr.reviewer_id')
            ->select(
                'dr.*',
                'rt.name as template_name',
                'u.name as user_name',
                'rv.name as reviewer_name'
            );

        if ($date) {
            $query->where('dr.report_date', $date);
        }
        if ($templateId) {
            $query->where('dr.template_id', $templateId);
        }

        if ($isAdmin) {
            if ($userId) $query->where('dr.user_id', $userId);
        } elseif ($isTruongPhong) {
            // Trưởng Phòng: thấy BC NV cùng PB
            $deptUserIds = DB::table('users')->where('MaPB', $user->MaPB)->where('TinhTrang', 1)->pluck('id');
            $query->whereIn('dr.user_id', $deptUserIds);
            if ($userId) $query->where('dr.user_id', $userId);
        } else {
            $query->where('dr.user_id', $user->id);
        }

        $reports = $query->orderByDesc('dr.created_at')->get();

        foreach ($reports as $r) {
            $r->values = DB::table('daily_report_values as dv')
                ->join('report_template_fields as tf', 'tf.id', '=', 'dv.field_id')
                ->where('dv.report_id', $r->id)
                ->select('dv.field_id', 'dv.value', 'tf.label', 'tf.field_type')
                ->orderBy('tf.sort_order')
                ->get();
        }

        $notSubmitted = [];
        if (($isAdmin || $isTruongPhong) && $templateId && $date) {
            $template = DB::table('report_templates')->find($templateId);
            $usersQuery = DB::table('users')->where('TinhTrang', 1);
            if ($isTruongPhong) {
                $usersQuery->where('MaPB', $user->MaPB);
            } elseif ($template && $template->MaPB) {
                $usersQuery->where('MaPB', $template->MaPB);
            }
            $allUsers = $usersQuery->pluck('name', 'id');
            $submittedIds = $reports->pluck('user_id')->toArray();
            foreach ($allUsers as $uid => $uname) {
                if (!in_array($uid, $submittedIds)) {
                    $notSubmitted[] = ['id' => $uid, 'name' => $uname];
                }
            }
        }

        return response()->json([
            'success' => true,
            'reports' => $reports,
            'not_submitted' => $notSubmitted,
            'is_admin' => $isAdmin || $isTruongPhong,
        ]);
    }

    /**
     * API: Lấy báo cáo của user hiện tại theo ngày + mẫu (để pre-fill form)
     */
    public function getMyReport(Request $request)
    {
        $user = Auth::user();
        $date = $request->date ?? now()->toDateString();
        $templateId = $request->template_id;

        $report = DB::table('daily_reports')
            ->where('template_id', $templateId)
            ->where('user_id', $user->id)
            ->where('report_date', $date)
            ->first();

        if (!$report) {
            return response()->json(['success' => true, 'report' => null]);
        }

        $report->values = DB::table('daily_report_values')
            ->where('report_id', $report->id)
            ->pluck('value', 'field_id');

        return response()->json(['success' => true, 'report' => $report]);
    }

    /**
     * API: Nhận xét báo cáo (Admin)
     */
    public function reviewReport(Request $request, $id)
    {
        $user = Auth::user();
        if (!$this->canManage()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
        }
        // Trưởng Phòng chỉ đánh giá BC NV cùng PB
        if ($this->isTruongPhong()) {
            $report = DB::table('daily_reports')->find($id);
            $reportUser = $report ? DB::table('users')->find($report->user_id) : null;
            if (!$reportUser || $reportUser->MaPB != $user->MaPB) {
                return response()->json(['success' => false, 'message' => 'Không có quyền đánh giá'], 403);
            }
        }

        DB::table('daily_reports')->where('id', $id)->update([
            'status' => 'reviewed',
            'reviewer_id' => $user->id,
            'reviewer_note' => $request->note,
            'reviewed_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Đã nhận xét']);
    }

    /**
     * API: Thống kê nộp báo cáo
     */
    public function statsApi(Request $request)
    {
        $user = Auth::user();
        if (!$this->canManage()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
        }

        $from = $request->from ?? now()->startOfMonth()->toDateString();
        $to = $request->to ?? now()->toDateString();
        $templateId = $request->template_id;

        $query = DB::table('daily_reports as dr')
            ->join('users as u', 'u.id', '=', 'dr.user_id')
            ->whereBetween('dr.report_date', [$from, $to]);

        if ($templateId) {
            $query->where('dr.template_id', $templateId);
        }
        // Trưởng Phòng: chỉ thống kê NV cùng PB
        if ($this->isTruongPhong()) {
            $deptUserIds = DB::table('users')->where('MaPB', $user->MaPB)->where('TinhTrang', 1)->pluck('id');
            $query->whereIn('dr.user_id', $deptUserIds);
        }

        $stats = $query->select(
            'dr.user_id',
            'u.name as user_name',
            DB::raw('COUNT(*) as total_reports'),
            DB::raw("SUM(CASE WHEN dr.status = 'submitted' THEN 1 ELSE 0 END) as submitted"),
            DB::raw("SUM(CASE WHEN dr.status = 'reviewed' THEN 1 ELSE 0 END) as reviewed"),
        )
            ->groupBy('dr.user_id', 'u.name')
            ->orderBy('u.name')
            ->get();

        // Calculate working days in range
        $start = \Carbon\Carbon::parse($from);
        $end = \Carbon\Carbon::parse($to);
        $workingDays = 0;
        while ($start->lte($end)) {
            if (!$start->isWeekend()) $workingDays++;
            $start->addDay();
        }

        return response()->json([
            'success' => true,
            'stats' => $stats,
            'working_days' => $workingDays,
            'from' => $from,
            'to' => $to,
        ]);
    }

    /**
     * API: Lấy nhân viên active theo phòng ban
     */
    public function getUsersByDepartment(Request $request)
    {
        $user = Auth::user();
        if (!$this->canManage()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
        }

        $query = DB::table('users')->where('TinhTrang', 1)->orderBy('name');

        // Trưởng Phòng chỉ xem NV PB mình
        if ($this->isTruongPhong()) {
            $query->where('MaPB', $user->MaPB);
        } elseif ($request->MaPB) {
            $query->where('MaPB', $request->MaPB);
        }

        $users = $query->get(['id', 'name', 'MaPB']);

        return response()->json(['success' => true, 'users' => $users]);
    }

    /**
     * API: Upload hình đính kèm báo cáo
     */
    public function uploadReportImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // Max 5MB
        ]);

        $file = $request->file('image');
        $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $path = 'reports/images/' . date('Y/m');

        $file->storeAs('public/' . $path, $fileName);

        $url = asset('storage/' . $path . '/' . $fileName);

        return response()->json(['success' => true, 'url' => $url]);
    }
}
