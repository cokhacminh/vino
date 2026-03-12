<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ThreadController extends Controller
{
    protected $s3Folder = 'Thread';
    /**
     * Danh sách threads
     */
    public function index($type = 'gop_y')
    {
        $threads = DB::table('threads')
            ->join('users', 'users.id', '=', 'threads.user_id')
            ->where('threads.type', $type)
            ->select(
                'threads.*',
                'users.name as user_name',
                'users.avatar as user_avatar'
            )
            ->selectRaw('(SELECT COUNT(*) FROM thread_comments WHERE thread_comments.thread_id = threads.id) as comment_count')
            ->orderByDesc('threads.created_at')
            ->get();

        $view = $type === 'hoi_dap' ? 'main.hoidap.index' : 'main.system.gop_y';
        return view($view, compact('threads'));
    }

    /**
     * Tạo thread mới
     */
    public function store(Request $request, $type = 'gop_y')
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'images.*' => 'image|max:5120',
        ], [
            'title.required' => 'Vui lòng nhập tiêu đề',
            'body.required' => 'Vui lòng nhập nội dung',
        ]);

        $user = Auth::user();
        $imageUrls = $this->uploadImages($request);

        DB::table('threads')->insert([
            'user_id' => $user->id,
            'type' => $type,
            'title' => $request->title,
            'body' => $request->body,
            'images' => !empty($imageUrls) ? json_encode($imageUrls) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('success', 'Tạo bài viết thành công!');
    }

    public function indexHoiDap() { return $this->index('hoi_dap'); }
    public function storeHoiDap(Request $request) { $this->s3Folder = 'Q&A'; return $this->store($request, 'hoi_dap'); }

    /**
     * Xem chi tiết thread + comments
     */
    public function show($id)
    {
        $thread = DB::table('threads')
            ->join('users', 'users.id', '=', 'threads.user_id')
            ->where('threads.id', $id)
            ->select('threads.*', 'users.name as user_name', 'users.avatar as user_avatar')
            ->first();

        if (!$thread) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài viết'], 404);
        }

        $comments = DB::table('thread_comments')
            ->join('users', 'users.id', '=', 'thread_comments.user_id')
            ->where('thread_comments.thread_id', $id)
            ->select(
                'thread_comments.*',
                'users.name as user_name',
                'users.avatar as user_avatar'
            )
            ->orderBy('thread_comments.created_at')
            ->get();

        // Parse images JSON
        foreach ($comments as $c) {
            $c->images = $c->images ? json_decode($c->images, true) : [];
        }
        $thread->images = $thread->images ? json_decode($thread->images, true) : [];

        return response()->json([
            'success' => true,
            'thread' => $thread,
            'comments' => $comments,
        ]);
    }

    /**
     * Khóa/Mở khóa thread
     */
    public function lock($id)
    {
        $user = Auth::user();
        $thread = DB::table('threads')->where('id', $id)->first();

        if (!$thread) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);
        }

        $isAdmin = $user->can('Admin');
        if ($thread->user_id != $user->id && !$isAdmin) {
            return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
        }

        DB::table('threads')->where('id', $id)->update([
            'is_locked' => !$thread->is_locked,
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'is_locked' => !$thread->is_locked,
            'message' => $thread->is_locked ? 'Đã mở khóa bài viết' : 'Đã khóa bài viết',
        ]);
    }

    /**
     * Xóa thread
     */
    public function destroy($id)
    {
        $user = Auth::user();
        $thread = DB::table('threads')->where('id', $id)->first();

        if (!$thread) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy'], 404);
        }

        $isAdmin = $user->can('Admin');
        if ($thread->user_id != $user->id && !$isAdmin) {
            return response()->json(['success' => false, 'message' => 'Không có quyền'], 403);
        }

        // Xóa ảnh trên S3
        $this->deleteThreadImages($thread);
        $comments = DB::table('thread_comments')->where('thread_id', $id)->get();
        foreach ($comments as $c) {
            $this->deleteCommentImages($c);
        }

        DB::table('threads')->where('id', $id)->delete(); // cascade deletes comments

        return response()->json(['success' => true, 'message' => 'Đã xóa bài viết']);
    }

    /**
     * Thêm comment
     */
    public function storeComment(Request $request, $threadId)
    {
        $request->validate([
            'body' => 'required|string',
            'images.*' => 'image|max:5120',
        ], [
            'body.required' => 'Vui lòng nhập nội dung comment',
        ]);

        $thread = DB::table('threads')->where('id', $threadId)->first();
        if (!$thread) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy bài viết'], 404);
        }
        if ($thread->is_locked) {
            return response()->json(['success' => false, 'message' => 'Bài viết đã bị khóa, không thể comment'], 403);
        }

        $user = Auth::user();
        $imageUrls = $this->uploadImages($request);

        DB::table('thread_comments')->insert([
            'thread_id' => $threadId,
            'parent_id' => $request->input('parent_id'),
            'user_id' => $user->id,
            'body' => $request->body,
            'images' => !empty($imageUrls) ? json_encode($imageUrls) : null,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Đã thêm comment']);
    }

    /**
     * Sửa comment
     */
    public function updateComment(Request $request, $id)
    {
        $request->validate(['body' => 'required|string']);

        $user = Auth::user();
        $comment = DB::table('thread_comments')->where('id', $id)->first();

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy comment'], 404);
        }

        if ($comment->user_id != $user->id) {
            return response()->json(['success' => false, 'message' => 'Không có quyền sửa'], 403);
        }

        DB::table('thread_comments')->where('id', $id)->update([
            'body' => $request->body,
            'updated_at' => now(),
        ]);

        return response()->json(['success' => true, 'message' => 'Đã sửa comment']);
    }

    /**
     * Xóa comment
     */
    public function destroyComment($id)
    {
        $user = Auth::user();
        $comment = DB::table('thread_comments')->where('id', $id)->first();

        if (!$comment) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy comment'], 404);
        }

        $isAdmin = $user->can('Admin');
        if ($comment->user_id != $user->id && !$isAdmin) {
            return response()->json(['success' => false, 'message' => 'Không có quyền xóa'], 403);
        }

        $this->deleteCommentImages($comment);

        // Xóa tất cả reply con (và ảnh S3 của chúng)
        $childComments = DB::table('thread_comments')->where('parent_id', $id)->get();
        foreach ($childComments as $child) {
            $this->deleteCommentImages($child);
        }
        DB::table('thread_comments')->where('parent_id', $id)->delete();

        DB::table('thread_comments')->where('id', $id)->delete();

        return response()->json(['success' => true, 'message' => 'Đã xóa comment']);
    }

    /**
     * Upload ảnh lên S3 (Thread/)
     */
    public function uploadImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|max:5120',
        ]);

        try {
            $file = $request->file('image');
            $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
            $path = $this->s3Folder . '/' . date('Y-m');

            Storage::disk('s3')->putFileAs($path, $file, $fileName, 'public');
            $url = Storage::disk('s3')->url($path . '/' . $fileName);

            return response()->json(['success' => true, 'url' => $url]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Lỗi upload: ' . $e->getMessage()], 500);
        }
    }

    // === Helpers ===

    private function uploadImages(Request $request): array
    {
        $urls = [];
        if ($request->hasFile('images')) {
            $disk = Storage::disk('s3');
            $path = $this->s3Folder . '/' . date('Y-m');
            foreach ($request->file('images') as $file) {
                $fileName = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $disk->putFileAs($path, $file, $fileName, 'public');
                $urls[] = $disk->url($path . '/' . $fileName);
            }
        }
        return $urls;
    }

    private function deleteThreadImages($thread)
    {
        if ($thread->images) {
            $images = json_decode($thread->images, true);
            $this->deleteS3Images($images ?: []);
        }
    }

    private function deleteCommentImages($comment)
    {
        if ($comment->images) {
            $images = json_decode($comment->images, true);
            $this->deleteS3Images($images ?: []);
        }
    }

    private function deleteS3Images(array $urls)
    {
        $disk = Storage::disk('s3');
        foreach ($urls as $url) {
            // Extract path from URL
            $parsed = parse_url($url);
            if (isset($parsed['path'])) {
                $s3Path = ltrim($parsed['path'], '/');
                // Remove bucket name if present
                $bucket = config('filesystems.disks.s3.bucket');
                if ($bucket && str_starts_with($s3Path, $bucket . '/')) {
                    $s3Path = substr($s3Path, strlen($bucket) + 1);
                }
                try {
                    $disk->delete($s3Path);
                } catch (\Exception $e) {
                    // Silent fail
                }
            }
        }
    }
}
