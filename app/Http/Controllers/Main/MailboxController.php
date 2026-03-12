<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class MailboxController extends Controller
{
    /**
     * Hộp Thư Đến + Đã Gửi (cùng 1 view, phân biệt qua tab)
     */
    public function index()
    {
        $user = Auth::user();

        // Hộp thư đến
        $inbox = DB::table('message_recipients')
            ->join('messages', 'messages.id', '=', 'message_recipients.message_id')
            ->join('users', 'users.id', '=', 'messages.sender_id')
            ->where('message_recipients.recipient_id', $user->id)
            ->where('message_recipients.deleted_by_recipient', false)
            ->select(
                'messages.id',
                'messages.subject',
                'messages.body',
                'messages.created_at',
                'messages.sender_id',
                'users.name as sender_name',
                'users.avatar as sender_avatar',
                'message_recipients.is_read',
                'message_recipients.read_at'
            )
            ->orderByDesc('messages.created_at')
            ->get();

        // Thư đã gửi
        $sent = DB::table('messages')
            ->where('messages.sender_id', $user->id)
            ->where('messages.deleted_by_sender', false)
            ->select(
                'messages.id',
                'messages.subject',
                'messages.body',
                'messages.created_at'
            )
            ->orderByDesc('messages.created_at')
            ->get();

        // Lấy danh sách người nhận cho mỗi thư đã gửi
        $sentIds = $sent->pluck('id')->toArray();
        $sentRecipients = DB::table('message_recipients')
            ->join('users', 'users.id', '=', 'message_recipients.recipient_id')
            ->whereIn('message_recipients.message_id', $sentIds)
            ->select('message_recipients.message_id', 'users.name')
            ->get()
            ->groupBy('message_id');

        foreach ($sent as $msg) {
            $msg->recipients = $sentRecipients->get($msg->id, collect())->pluck('name')->implode(', ');
        }

        // Đếm thư chưa đọc
        $unreadCount = DB::table('message_recipients')
            ->where('recipient_id', $user->id)
            ->where('is_read', false)
            ->where('deleted_by_recipient', false)
            ->count();

        // Danh sách users để chọn người nhận (trừ chính mình)
        $users = User::where('id', '!=', $user->id)
            ->where('TinhTrang', 'Active')
            ->select('id', 'name')
            ->orderBy('name')
            ->get();

        return view('main.mailbox.index', compact('inbox', 'sent', 'unreadCount', 'users'));
    }

    /**
     * Xem chi tiết thư
     */
    public function show($id)
    {
        $user = Auth::user();

        $message = DB::table('messages')
            ->join('users', 'users.id', '=', 'messages.sender_id')
            ->where('messages.id', $id)
            ->select(
                'messages.*',
                'users.name as sender_name',
                'users.avatar as sender_avatar'
            )
            ->first();

        if (!$message) {
            return response()->json(['success' => false, 'message' => 'Không tìm thấy thư'], 404);
        }

        // Kiểm tra quyền xem (là người gửi hoặc người nhận)
        $isRecipient = DB::table('message_recipients')
            ->where('message_id', $id)
            ->where('recipient_id', $user->id)
            ->exists();

        $isSender = $message->sender_id == $user->id;

        if (!$isRecipient && !$isSender) {
            return response()->json(['success' => false, 'message' => 'Bạn không có quyền xem thư này'], 403);
        }

        // Đánh dấu đã đọc nếu là người nhận
        if ($isRecipient) {
            DB::table('message_recipients')
                ->where('message_id', $id)
                ->where('recipient_id', $user->id)
                ->where('is_read', false)
                ->update([
                    'is_read' => true,
                    'read_at' => now(),
                ]);
        }

        // Lấy danh sách người nhận
        $recipients = DB::table('message_recipients')
            ->join('users', 'users.id', '=', 'message_recipients.recipient_id')
            ->where('message_recipients.message_id', $id)
            ->select('users.name', 'message_recipients.is_read')
            ->get();

        return response()->json([
            'success' => true,
            'message' => $message,
            'recipients' => $recipients,
        ]);
    }

    /**
     * Gửi thư mới
     */
    public function send(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'body' => 'required|string',
            'recipients' => 'required|array|min:1',
            'recipients.*' => 'exists:users,id',
        ], [
            'subject.required' => 'Vui lòng nhập tiêu đề',
            'body.required' => 'Vui lòng nhập nội dung thư',
            'recipients.required' => 'Vui lòng chọn ít nhất 1 người nhận',
            'recipients.min' => 'Vui lòng chọn ít nhất 1 người nhận',
        ]);

        $user = Auth::user();

        try {
            DB::beginTransaction();

            $messageId = DB::table('messages')->insertGetId([
                'sender_id' => $user->id,
                'subject' => $request->subject,
                'body' => $request->body,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $recipientData = [];
            foreach ($request->recipients as $recipientId) {
                $recipientData[] = [
                    'message_id' => $messageId,
                    'recipient_id' => $recipientId,
                    'is_read' => false,
                ];
            }

            DB::table('message_recipients')->insert($recipientData);

            DB::commit();

            return redirect()->route('mailbox.index')->with('success', 'Đã gửi thư thành công!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('mailbox.index')->with('error', 'Gửi thư thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Xóa thư (soft delete)
     */
    public function destroy($id)
    {
        $user = Auth::user();

        // Nếu là người nhận → soft delete cho recipient
        $updated = DB::table('message_recipients')
            ->where('message_id', $id)
            ->where('recipient_id', $user->id)
            ->update(['deleted_by_recipient' => true]);

        // Nếu là người gửi → soft delete cho sender
        if (!$updated) {
            DB::table('messages')
                ->where('id', $id)
                ->where('sender_id', $user->id)
                ->update(['deleted_by_sender' => true]);
        }

        return response()->json(['success' => true, 'message' => 'Đã xóa thư']);
    }
}
