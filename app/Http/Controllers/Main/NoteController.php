<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Note;
use App\Models\Reminder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NoteController extends Controller
{
    // ============================================
    // INDEX — Trang chính
    // ============================================

    public function index(Request $request)
    {
        $user = Auth::user();

        $query = Note::where('user_id', $user->id)
            ->with('activeReminder');

        if ($request->filled('search')) {
            $s = $request->search;
            $query->where(function ($q) use ($s) {
                $q->where('tieu_de', 'like', "%{$s}%")
                  ->orWhere('noi_dung', 'like', "%{$s}%");
            });
        }
        if ($request->filled('mau_sac')) {
            $query->where('mau_sac', $request->mau_sac);
        }
        if ($request->filled('ghim')) {
            $query->where('ghim', true);
        }
        if ($request->filled('co_nhac')) {
            $query->whereHas('reminders', function ($q) {
                $q->where('trang_thai', '!=', 'hoan_thanh');
            });
        }

        $notes = $query->orderByDesc('ghim')
            ->orderByDesc('updated_at')
            ->get();

        // Reminders for calendar view
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $reminders = Reminder::where('user_id', $user->id)
            ->whereYear('thoi_gian', $year)
            ->whereMonth('thoi_gian', $month)
            ->with('note')
            ->orderBy('thoi_gian')
            ->get();

        // Reminder count hôm nay
        $todayReminders = Reminder::where('user_id', $user->id)
            ->whereDate('thoi_gian', today())
            ->where('trang_thai', 'chua_nhac')
            ->count();

        return view('main.notes.index', compact('notes', 'reminders', 'month', 'year', 'todayReminders'));
    }

    // ============================================
    // STORE — Tạo ghi chú mới
    // ============================================

    public function store(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'noi_dung' => 'nullable|string',
            'mau_sac' => 'nullable|string|max:7',
            'ghim' => 'nullable|boolean',
            'reminder_time' => 'nullable|date',
            'reminder_repeat' => 'nullable|in:khong,hang_ngay,hang_tuan,hang_thang',
        ]);

        $note = Note::create([
            'user_id' => Auth::id(),
            'tieu_de' => $request->tieu_de,
            'noi_dung' => $request->noi_dung,
            'mau_sac' => $request->mau_sac ?? '#fbbf24',
            'ghim' => $request->ghim ?? false,
        ]);

        // Create reminder if provided
        if ($request->filled('reminder_time')) {
            Reminder::create([
                'user_id' => Auth::id(),
                'note_id' => $note->id,
                'tieu_de' => $request->tieu_de,
                'thoi_gian' => $request->reminder_time,
                'lap_lai' => $request->reminder_repeat ?? 'khong',
            ]);
        }

        return response()->json(['success' => true, 'note' => $note->load('activeReminder')]);
    }

    // ============================================
    // UPDATE — Sửa ghi chú
    // ============================================

    public function update(Request $request, $id)
    {
        $note = Note::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'tieu_de' => 'sometimes|string|max:255',
            'noi_dung' => 'nullable|string',
            'mau_sac' => 'nullable|string|max:7',
            'ghim' => 'nullable|boolean',
        ]);

        $note->update($request->only(['tieu_de', 'noi_dung', 'mau_sac', 'ghim']));

        return response()->json(['success' => true, 'note' => $note->fresh()->load('activeReminder')]);
    }

    // ============================================
    // TOGGLE PIN — Ghim / bỏ ghim
    // ============================================

    public function togglePin($id)
    {
        $note = Note::where('user_id', Auth::id())->findOrFail($id);
        $note->update(['ghim' => !$note->ghim]);

        return response()->json(['success' => true, 'ghim' => $note->ghim]);
    }

    // ============================================
    // DESTROY — Xóa ghi chú
    // ============================================

    public function destroy($id)
    {
        $note = Note::where('user_id', Auth::id())->findOrFail($id);
        $note->delete(); // cascade deletes reminders
        return response()->json(['success' => true]);
    }

    // ============================================
    // REMINDERS CRUD
    // ============================================

    public function storeReminder(Request $request)
    {
        $request->validate([
            'tieu_de' => 'required|string|max:255',
            'thoi_gian' => 'required|date',
            'lap_lai' => 'nullable|in:khong,hang_ngay,hang_tuan,hang_thang',
            'note_id' => 'nullable|exists:notes,id',
        ]);

        $reminder = Reminder::create([
            'user_id' => Auth::id(),
            'note_id' => $request->note_id,
            'tieu_de' => $request->tieu_de,
            'thoi_gian' => $request->thoi_gian,
            'lap_lai' => $request->lap_lai ?? 'khong',
        ]);

        return response()->json(['success' => true, 'reminder' => $reminder]);
    }

    public function updateReminder(Request $request, $id)
    {
        $reminder = Reminder::where('user_id', Auth::id())->findOrFail($id);

        $reminder->update($request->only(['tieu_de', 'thoi_gian', 'lap_lai', 'trang_thai']));

        return response()->json(['success' => true, 'reminder' => $reminder]);
    }

    public function destroyReminder($id)
    {
        Reminder::where('user_id', Auth::id())->findOrFail($id)->delete();
        return response()->json(['success' => true]);
    }

    public function completeReminder($id)
    {
        $reminder = Reminder::where('user_id', Auth::id())->findOrFail($id);

        if ($reminder->lap_lai !== 'khong') {
            // Create next occurrence
            $nextTime = Carbon::parse($reminder->thoi_gian);
            switch ($reminder->lap_lai) {
                case 'hang_ngay': $nextTime->addDay(); break;
                case 'hang_tuan': $nextTime->addWeek(); break;
                case 'hang_thang': $nextTime->addMonth(); break;
            }
            Reminder::create([
                'user_id' => $reminder->user_id,
                'note_id' => $reminder->note_id,
                'tieu_de' => $reminder->tieu_de,
                'thoi_gian' => $nextTime,
                'lap_lai' => $reminder->lap_lai,
            ]);
        }

        $reminder->update(['trang_thai' => 'hoan_thanh']);

        return response()->json(['success' => true]);
    }

    // ============================================
    // CHECK REMINDERS — API for browser polling
    // ============================================

    public function checkReminders()
    {
        $now = Carbon::now();
        $reminders = Reminder::where('user_id', Auth::id())
            ->where('trang_thai', 'chua_nhac')
            ->where('thoi_gian', '<=', $now)
            ->with('note')
            ->get();

        // Mark as notified
        foreach ($reminders as $r) {
            $r->update(['trang_thai' => 'da_nhac']);
        }

        return response()->json($reminders);
    }

    // ============================================
    // CALENDAR DATA — JSON cho calendar view
    // ============================================

    public function calendarData(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);

        $reminders = Reminder::where('user_id', Auth::id())
            ->whereYear('thoi_gian', $year)
            ->whereMonth('thoi_gian', $month)
            ->with('note')
            ->orderBy('thoi_gian')
            ->get()
            ->groupBy(function ($r) {
                return Carbon::parse($r->thoi_gian)->format('Y-m-d');
            });

        return response()->json($reminders);
    }
}
