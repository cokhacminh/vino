<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Models\TaskUser;
use App\Models\TaskComment;
use App\Models\TaskLabel;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TaskController extends Controller
{
    // ============================================
    // PERMISSION HELPERS
    // ============================================

    private function isAdmin()
    {
        $user = Auth::user();
        return $user && ($user->hasRole('Admin') || $user->can('Admin'));
    }

    private function isTruongPB()
    {
        $user = Auth::user();
        return $user && !$this->isAdmin() && $user->can('quan_ly_task');
    }

    private function isNhanSu()
    {
        $user = Auth::user();
        return $user && !$this->isAdmin() && $user->can('Nhân Sự');
    }

    /**
     * Lấy danh sách user_ids mà current user có quyền quản lý
     */
    private function getManagedUserIds()
    {
        $user = Auth::user();
        if ($this->isAdmin()) {
            return User::where('TinhTrang', 1)->pluck('id')->toArray();
        }
        if ($this->isNhanSu()) {
            // Nhân Sự: quản lý tất cả trừ Admin
            return User::where('TinhTrang', 1)
                ->whereDoesntHave('permissions', function($q) {
                    $q->where('name', 'Admin');
                })
                ->pluck('id')->toArray();
        }
        if ($this->isTruongPB()) {
            return User::where('MaPB', $user->MaPB)->where('TinhTrang', 1)->pluck('id')->toArray();
        }
        return [$user->id];
    }

    // ============================================
    // INDEX — Trang chính (Kanban + List)
    // ============================================

    public function index(Request $request)
    {
        $user = Auth::user();
        $isAdmin = $this->isAdmin();
        $isTruongPB = $this->isTruongPB();
        $isNhanSu = $this->isNhanSu();

        // Query tasks based on role
        $query = Task::with(['creator', 'taskUsers.user', 'labels', 'subtasks'])
            ->whereNull('parent_id'); // Only main tasks, not subtasks

        if ($isAdmin || $isNhanSu) {
            // Admin và Nhân Sự thấy tất cả
        } elseif ($isTruongPB) {
            // Trưởng PB: tasks thuộc phòng ban mình + tasks mình tạo + tasks mình được giao
            $pbUserIds = User::where('MaPB', $user->MaPB)->pluck('id')->toArray();
            $query->where(function ($q) use ($user, $pbUserIds) {
                $q->where('MaPB', $user->MaPB)
                  ->orWhere('created_by', $user->id)
                  ->orWhereHas('taskUsers', function ($sub) use ($pbUserIds) {
                      $sub->whereIn('user_id', $pbUserIds);
                  });
            });
        } else {
            // Nhân viên: chỉ thấy task mình tạo + task mình được giao
            $query->where(function ($q) use ($user) {
                $q->where('created_by', $user->id)
                  ->orWhereHas('taskUsers', function ($sub) use ($user) {
                      $sub->where('user_id', $user->id);
                  });
            });
        }

        // Filters
        if ($request->filled('trang_thai')) {
            $query->where('trang_thai', $request->trang_thai);
        }
        if ($request->filled('do_uu_tien')) {
            $query->where('do_uu_tien', $request->do_uu_tien);
        }
        if ($request->filled('user_id')) {
            $query->whereHas('taskUsers', function ($q) use ($request) {
                $q->where('user_id', $request->user_id);
            });
        }
        if ($request->filled('search')) {
            $query->where('tieu_de', 'like', '%' . $request->search . '%');
        }

        $tasks = $query->orderBy('thu_tu')->orderByDesc('created_at')->get();

        // Group by status for Kanban
        $kanban = [
            'chua_bat_dau' => $tasks->where('trang_thai', 'chua_bat_dau')->values(),
            'dang_lam'     => $tasks->where('trang_thai', 'dang_lam')->values(),
            'cho_duyet'    => $tasks->where('trang_thai', 'cho_duyet')->values(),
            'hoan_thanh'   => $tasks->where('trang_thai', 'hoan_thanh')->values(),
        ];

        // Users for assignment dropdown
        if ($isAdmin) {
            $assignableUsers = User::where('TinhTrang', 1)->orderBy('name')->get();
        } elseif ($isNhanSu) {
            // Nhân Sự: phân công tất cả trừ Admin
            $assignableUsers = User::where('TinhTrang', 1)
                ->whereDoesntHave('permissions', function($q) {
                    $q->where('name', 'Admin');
                })
                ->orderBy('name')->get();
        } elseif ($isTruongPB) {
            $assignableUsers = User::where('TinhTrang', 1)
                ->where('MaPB', $user->MaPB)
                ->orderBy('name')->get();
        } else {
            $assignableUsers = collect([]);
        }

        $labels = TaskLabel::orderBy('ten')->get();
        $phongBans = DB::table('phongban')->where('TrangThai', 1)->get();

        return view('main.tasks.index', compact(
            'kanban', 'tasks', 'assignableUsers', 'labels', 'phongBans',
            'isAdmin', 'isTruongPB', 'isNhanSu'
        ));
    }

    // ============================================
    // STORE — Tạo task mới
    // ============================================

    public function store(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'mo_ta' => 'nullable|string',
            'loai' => 'required|in:ca_nhan,nhom,phong_ban',
            'do_uu_tien' => 'required|in:thap,trung_binh,cao,khan_cap',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date',
            'assigned_users' => 'nullable|array',
            'assigned_users.*' => 'exists:users,id',
            'label_ids' => 'nullable|array',
            'parent_id' => 'nullable|exists:tasks,id',
        ]);

        $user = Auth::user();

        $task = Task::create([
            'tieu_de' => $request->tieu_de,
            'mo_ta' => $request->mo_ta,
            'loai' => $request->loai,
            'do_uu_tien' => $request->do_uu_tien,
            'trang_thai' => 'chua_bat_dau',
            'parent_id' => $request->parent_id,
            'created_by' => $user->id,
            'MaPB' => $request->loai === 'phong_ban' ? $user->MaPB : null,
            'ngay_bat_dau' => $request->ngay_bat_dau,
            'ngay_ket_thuc' => $request->ngay_ket_thuc,
        ]);

        // Attach users
        if ($request->filled('assigned_users')) {
            foreach ($request->assigned_users as $index => $userId) {
                TaskUser::create([
                    'task_id' => $task->id,
                    'user_id' => $userId,
                    'vai_tro' => $index === 0 ? 'phu_trach' : 'tham_gia',
                ]);
            }
        } else {
            // Không giao ai => giao cho bản thân
            TaskUser::create([
                'task_id' => $task->id,
                'user_id' => $user->id,
                'vai_tro' => 'phu_trach',
            ]);
        }

        // Attach labels
        if ($request->filled('label_ids')) {
            $task->labels()->sync($request->label_ids);
        }

        return response()->json(['success' => true, 'task' => $task->load('taskUsers.user', 'labels')]);
    }

    // ============================================
    // UPDATE — Cập nhật task
    // ============================================

    public function update(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Permission check
        if (!$this->canEditTask($task)) {
            return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
        }

        $request->validate([
            'tieu_de' => 'sometimes|string|max:255',
            'mo_ta' => 'nullable|string',
            'do_uu_tien' => 'sometimes|in:thap,trung_binh,cao,khan_cap',
            'trang_thai' => 'sometimes|in:chua_bat_dau,dang_lam,cho_duyet,hoan_thanh,huy',
            'ngay_bat_dau' => 'nullable|date',
            'ngay_ket_thuc' => 'nullable|date',
            'assigned_users' => 'nullable|array',
            'label_ids' => 'nullable|array',
        ]);

        $task->update($request->only([
            'tieu_de', 'mo_ta', 'do_uu_tien', 'trang_thai',
            'ngay_bat_dau', 'ngay_ket_thuc',
        ]));

        // Update assigned users if provided
        if ($request->has('assigned_users')) {
            TaskUser::where('task_id', $task->id)->delete();
            foreach ($request->assigned_users as $index => $userId) {
                TaskUser::create([
                    'task_id' => $task->id,
                    'user_id' => $userId,
                    'vai_tro' => $index === 0 ? 'phu_trach' : 'tham_gia',
                ]);
            }
        }

        // Update labels if provided
        if ($request->has('label_ids')) {
            $task->labels()->sync($request->label_ids ?? []);
        }

        return response()->json(['success' => true, 'task' => $task->fresh()->load('taskUsers.user', 'labels', 'subtasks')]);
    }

    // ============================================
    // UPDATE STATUS — Kéo thả Kanban
    // ============================================

    public function updateStatus(Request $request, $id)
    {
        $task = Task::findOrFail($id);

        // Chờ duyệt → Hoàn thành: chỉ Admin / Trưởng PB
        if ($request->trang_thai === 'hoan_thanh' && $task->trang_thai === 'cho_duyet') {
            if (!$this->isAdmin() && !$this->isTruongPB() && !$this->isNhanSu()) {
                return response()->json(['success' => false, 'message' => 'Chỉ quản lý được duyệt task'], 403);
            }
        }

        $task->update([
            'trang_thai' => $request->trang_thai,
            'thu_tu' => $request->thu_tu ?? $task->thu_tu,
        ]);

        return response()->json(['success' => true]);
    }

    // ============================================
    // UPDATE ORDER — Sắp xếp thứ tự
    // ============================================

    public function updateOrder(Request $request)
    {
        $items = $request->items; // [{id: 1, thu_tu: 0, trang_thai: 'dang_lam'}, ...]
        if (!$items) return response()->json(['success' => false]);

        foreach ($items as $item) {
            Task::where('id', $item['id'])->update([
                'thu_tu' => $item['thu_tu'],
                'trang_thai' => $item['trang_thai'],
            ]);
        }

        return response()->json(['success' => true]);
    }

    // ============================================
    // DESTROY — Xóa task
    // ============================================

    public function destroy($id)
    {
        $task = Task::findOrFail($id);

        if (!$this->canEditTask($task)) {
            return response()->json(['success' => false, 'message' => 'Không có quyền xóa'], 403);
        }

        // Xóa ảnh đính kèm trên S3
        $this->deleteTaskImages($task);

        // Xóa comments images
        foreach ($task->comments as $comment) {
            $this->deleteCommentImages($comment);
        }

        $task->delete(); // cascade deletes subtasks, task_users, comments, labels

        return response()->json(['success' => true]);
    }

    // ============================================
    // DETAIL — Chi tiết task (JSON)
    // ============================================

    public function detail($id)
    {
        $task = Task::with([
            'creator', 'taskUsers.user', 'labels',
            'subtasks.taskUsers.user', 'subtasks.labels',
            'comments.user', 'phongBan',
        ])->findOrFail($id);

        return response()->json($task);
    }

    // ============================================
    // UPDATE PROGRESS — Cập nhật tiến độ
    // ============================================

    public function updateProgress(Request $request, $id)
    {
        $user = Auth::user();
        $taskUser = TaskUser::where('task_id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$taskUser) {
            // Admin/Trưởng PB có thể cập nhật tiến độ cho bất kỳ ai
            if ($this->isAdmin() || $this->isTruongPB()) {
                $taskUser = TaskUser::where('task_id', $id)
                    ->where('user_id', $request->user_id)
                    ->first();
            }
            if (!$taskUser) {
                return response()->json(['success' => false, 'message' => 'Không tìm thấy phân công'], 404);
            }
        }

        $taskUser->update([
            'tien_do' => min(100, max(0, (int)$request->tien_do)),
            'ghi_chu' => $request->ghi_chu ?? $taskUser->ghi_chu,
        ]);

        return response()->json(['success' => true, 'taskUser' => $taskUser]);
    }

    // ============================================
    // COMMENTS
    // ============================================

    public function storeComment(Request $request, $taskId)
    {
        $request->validate([
            'noi_dung' => 'required|string',
        ]);

        $comment = TaskComment::create([
            'task_id' => $taskId,
            'user_id' => Auth::id(),
            'noi_dung' => $request->noi_dung,
            'hinh_anh' => $request->hinh_anh,
        ]);

        return response()->json([
            'success' => true,
            'comment' => $comment->load('user'),
        ]);
    }

    public function destroyComment($id)
    {
        $comment = TaskComment::findOrFail($id);

        // Chỉ người tạo comment hoặc Admin mới được xóa
        if ($comment->user_id !== Auth::id() && !$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
        }

        $this->deleteCommentImages($comment);
        $comment->delete();

        return response()->json(['success' => true]);
    }

    // ============================================
    // LABELS CRUD
    // ============================================

    public function storeLabel(Request $request)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Chỉ Admin được quản lý nhãn'], 403);
        }

        $request->validate([
            'ten' => 'required|string|max:50',
            'mau_sac' => 'required|string|max:7',
        ]);

        $label = TaskLabel::create($request->only('ten', 'mau_sac'));

        return response()->json(['success' => true, 'label' => $label]);
    }

    public function destroyLabel($id)
    {
        if (!$this->isAdmin()) {
            return response()->json(['success' => false, 'message' => 'Chỉ Admin được quản lý nhãn'], 403);
        }

        TaskLabel::findOrFail($id)->delete();

        return response()->json(['success' => true]);
    }

    // ============================================
    // IMAGE UPLOAD
    // ============================================

    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120', // 5MB
        ]);

        $file = $request->file('image');
        $filename = 'Task/' . time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        Storage::disk('s3')->put($filename, file_get_contents($file), 'public');

        $url = Storage::disk('s3')->url($filename);

        return response()->json(['success' => true, 'url' => $url]);
    }

    // ============================================
    // HELPERS
    // ============================================

    private function canEditTask(Task $task)
    {
        if ($this->isAdmin()) return true;
        if ($this->isNhanSu()) return true;

        $user = Auth::user();

        // Trưởng PB: quản lý task trong PB mình
        if ($this->isTruongPB()) {
            if ($task->MaPB == $user->MaPB) return true;
            if ($task->created_by == $user->id) return true;
            // Check if any assigned user is in their department
            $pbUserIds = User::where('MaPB', $user->MaPB)->pluck('id')->toArray();
            if ($task->taskUsers()->whereIn('user_id', $pbUserIds)->exists()) return true;
        }

        // Nhân viên: chỉ task mình tạo
        return $task->created_by == $user->id;
    }

    private function deleteTaskImages(Task $task)
    {
        if (!$task->hinh_anh) return;
        $urls = json_decode($task->hinh_anh, true) ?? [];
        $this->deleteS3Images($urls);
    }

    private function deleteCommentImages($comment)
    {
        if (!$comment->hinh_anh) return;
        $urls = json_decode($comment->hinh_anh, true) ?? [];
        $this->deleteS3Images($urls);
    }

    private function deleteS3Images(array $urls)
    {
        foreach ($urls as $url) {
            try {
                $parsed = parse_url($url);
                if (isset($parsed['path'])) {
                    $key = ltrim($parsed['path'], '/');
                    // Remove bucket name from path if present
                    $bucket = config('filesystems.disks.s3.bucket');
                    if ($bucket && strpos($key, $bucket . '/') === 0) {
                        $key = substr($key, strlen($bucket) + 1);
                    }
                    Storage::disk('s3')->delete($key);
                }
            } catch (\Exception $e) {
                // Skip failed deletions
            }
        }
    }
}
