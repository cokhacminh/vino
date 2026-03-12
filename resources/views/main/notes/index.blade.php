@extends('main.layouts.app')
@section('title', 'Ghi Chú & Nhắc Nhở')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<style>
/* ============================================= */
/*  NOTES & REMINDERS                             */
/* ============================================= */

.notes-page { padding: 10px; background: #fff; border-radius: 10px; box-shadow: 0 4px 15px rgba(0,0,0,0.2); }

/* Header */
.notes-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px; margin-bottom: 20px;
}
.notes-header h1 {
    font-size: 1.5rem; font-weight: 700; color: #000;
    display: flex; align-items: center; gap: 10px; margin: 0;
}
.notes-header h1 svg { width: 26px; height: 26px; color: #f59e0b; }
.notes-header-actions { display: flex; gap: 10px; flex-wrap: wrap; }

/* View toggle */
.notes-view-toggle {
    display: flex; background: rgba(0,0,0,0.04); border-radius: 10px;
    overflow: hidden; border: 1px solid rgba(0,0,0,0.08);
}
.notes-view-toggle button {
    padding: 8px 16px; border: none; background: none; color: #64748b;
    cursor: pointer; font-size: 0.85rem; transition: all .2s;
    display: flex; align-items: center; gap: 6px;
}
.notes-view-toggle button.active {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff; font-weight: 600;
}
.notes-view-toggle button:hover:not(.active) { background: rgba(0,0,0,0.06); }

.btn-create-note {
    padding: 10px 20px; border: none; border-radius: 10px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    transition: all .3s; box-shadow: 0 4px 15px rgba(245,158,11,0.3);
}
.btn-create-note:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(245,158,11,0.4); }

.btn-create-reminder {
    padding: 10px 20px; border: none; border-radius: 10px;
    background: linear-gradient(135deg, #6d28d9, #7c3aed);
    color: #fff; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    transition: all .3s; box-shadow: 0 4px 15px rgba(109,40,217,0.3);
}
.btn-create-reminder:hover { transform: translateY(-2px); }

/* Filters */
.notes-filters {
    display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
    margin-bottom: 20px; padding: 10px 14px;
    background: #f8fafc; border-radius: 12px; border: 1px solid #e2e8f0;
}
.notes-filters input[type="text"] {
    padding: 8px 12px; border-radius: 8px; border: 1px solid #cbd5e1;
    background: #fff; color: #1e293b; font-size: 0.85rem; outline: none; min-width: 180px;
}
.notes-filters input::placeholder { color: #94a3b8; }

.color-filter-group { display: flex; gap: 6px; align-items: center; }
.color-dot {
    width: 22px; height: 22px; border-radius: 50%; cursor: pointer;
    border: 2px solid transparent; transition: all .2s;
}
.color-dot:hover, .color-dot.active { border-color: #1e293b; transform: scale(1.15); }

.filter-btn {
    padding: 6px 14px; border-radius: 8px; border: 1px solid #e2e8f0;
    background: #fff; color: #64748b; cursor: pointer; font-size: 0.8rem;
    transition: all .2s;
}
.filter-btn.active { background: #f59e0b; color: #fff; border-color: #f59e0b; }

/* ============================================= */
/*  NOTES GRID                                    */
/* ============================================= */

.notes-grid {
    display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
    gap: 16px;
}
@media (max-width: 600px) { .notes-grid { grid-template-columns: 1fr; } }

.note-card {
    border-radius: 14px; padding: 16px; cursor: pointer;
    transition: all .25s ease; position: relative;
    border: 1px solid rgba(0,0,0,0.06);
    box-shadow: 0 2px 8px rgba(0,0,0,0.04);
    min-height: 120px; display: flex; flex-direction: column;
}
.note-card:hover {
    box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    transform: translateY(-3px);
}

.note-card-pin {
    position: absolute; top: 10px; right: 10px;
    font-size: 1rem; cursor: pointer; opacity: 0.4; transition: all .2s;
    background: none; border: none; padding: 4px;
}
.note-card-pin.pinned { opacity: 1; }
.note-card-pin:hover { opacity: 1; transform: rotate(20deg); }

.note-card-title {
    font-size: 0.95rem; font-weight: 700; color: rgba(0,0,0,0.85);
    margin-bottom: 8px; line-height: 1.3;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.note-card-content {
    font-size: 0.84rem; color: rgba(0,0,0,0.6); line-height: 1.5; flex: 1;
    display: -webkit-box; -webkit-line-clamp: 4; -webkit-box-orient: vertical; overflow: hidden;
}
.note-card-footer {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 10px; font-size: 0.72rem; color: rgba(0,0,0,0.4);
}
.note-card-footer .reminder-tag {
    display: flex; align-items: center; gap: 4px; color: #7c3aed;
    font-weight: 600; font-size: 0.72rem;
}

/* Pinned section */
.notes-section-title {
    font-size: 0.78rem; font-weight: 700; color: #94a3b8;
    text-transform: uppercase; letter-spacing: 1px;
    margin: 0 0 12px; padding-bottom: 6px;
    border-bottom: 1px solid #e2e8f0;
}

/* Color palette */
.note-colors {
    --note-yellow: #fef3c7;
    --note-blue: #dbeafe;
    --note-pink: #fce7f3;
    --note-purple: #ede9fe;
    --note-orange: #ffedd5;
    --note-gray: #f1f5f9;
}

/* ============================================= */
/*  CALENDAR VIEW                                 */
/* ============================================= */

.notes-calendar-view { display: none; }
.notes-calendar-view.active { display: block; }
.notes-grid-view.hidden { display: none; }

.calendar-wrapper {
    background: #fff; border-radius: 14px; border: 1px solid #e2e8f0;
    overflow: hidden;
}
.calendar-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 16px 20px; background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.calendar-header h3 { margin: 0; font-size: 1.1rem; font-weight: 700; color: #1e293b; }
.calendar-nav { display: flex; gap: 8px; }
.calendar-nav button {
    width: 36px; height: 36px; border: 1px solid #e2e8f0; border-radius: 8px;
    background: #fff; cursor: pointer; font-size: 1rem; color: #64748b;
    display: flex; align-items: center; justify-content: center;
    transition: all .2s;
}
.calendar-nav button:hover { background: #f1f5f9; color: #1e293b; }

.calendar-grid {
    display: grid; grid-template-columns: repeat(7, 1fr);
}
.calendar-day-header {
    padding: 10px; text-align: center; font-size: 0.75rem;
    font-weight: 700; color: #94a3b8; background: #f8fafc;
    border-bottom: 1px solid #e2e8f0;
}
.calendar-day {
    padding: 8px; min-height: 80px; border-right: 1px solid #f1f5f9;
    border-bottom: 1px solid #f1f5f9; cursor: pointer;
    transition: background .15s; position: relative;
}
.calendar-day:hover { background: #fffbeb; }
.calendar-day.other-month { opacity: 0.3; }
.calendar-day.today { background: #fffbeb; }
.calendar-day .day-number {
    font-size: 0.85rem; font-weight: 600; color: #1e293b; margin-bottom: 4px;
}
.calendar-day.today .day-number {
    background: #f59e0b; color: #fff; width: 26px; height: 26px;
    border-radius: 50%; display: flex; align-items: center; justify-content: center;
}
.calendar-day .day-dots { display: flex; gap: 3px; flex-wrap: wrap; }
.calendar-dot {
    width: 7px; height: 7px; border-radius: 50%;
}
.calendar-reminder-item {
    font-size: 0.68rem; padding: 2px 5px; border-radius: 4px;
    margin-top: 2px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    color: #fff; font-weight: 600;
}

/* Day detail panel */
.day-detail-panel {
    margin-top: 16px; padding: 16px; background: #f8fafc;
    border-radius: 12px; border: 1px solid #e2e8f0;
    display: none;
}
.day-detail-panel.active { display: block; }
.day-detail-panel h4 {
    margin: 0 0 12px; font-size: 0.95rem; font-weight: 700; color: #1e293b;
}
.day-reminder-list { list-style: none; padding: 0; margin: 0; }
.day-reminder-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 12px; margin-bottom: 6px;
    background: #fff; border-radius: 10px; border: 1px solid #e2e8f0;
}
.day-reminder-item .reminder-info { flex: 1; }
.day-reminder-item .reminder-title { font-size: 0.88rem; font-weight: 600; color: #1e293b; }
.day-reminder-item .reminder-time { font-size: 0.75rem; color: #64748b; }
.day-reminder-item .reminder-actions { display: flex; gap: 6px; }
.day-reminder-item button {
    padding: 4px 10px; border-radius: 6px; border: none;
    font-size: 0.75rem; font-weight: 600; cursor: pointer; transition: all .2s;
}
.btn-complete-reminder { background: rgba(52,211,153,0.15); color: #059669; }
.btn-complete-reminder:hover { background: rgba(52,211,153,0.3); }
.btn-delete-reminder { background: rgba(239,68,68,0.1); color: #ef4444; }
.btn-delete-reminder:hover { background: rgba(239,68,68,0.2); }

/* ============================================= */
/*  MODALS                                        */
/* ============================================= */

.note-modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.5);
    z-index: 9999; display: none; align-items: center; justify-content: center;
    backdrop-filter: blur(3px);
}
.note-modal-overlay.active { display: flex; }

.note-modal {
    background: #fff; border-radius: 16px; width: 95%; max-width: 560px;
    max-height: 90vh; overflow-y: auto;
    box-shadow: 0 20px 60px rgba(0,0,0,0.15);
}
.note-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 18px 22px; border-bottom: 1px solid #e2e8f0;
}
.note-modal-header h2 {
    font-size: 1.05rem; font-weight: 700; color: #1e293b; margin: 0;
}
.note-modal-close {
    width: 30px; height: 30px; border-radius: 8px; border: none;
    background: #f1f5f9; color: #64748b; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 1rem; transition: all .2s;
}
.note-modal-close:hover { background: #fee2e2; color: #ef4444; }

.note-modal-body { padding: 22px; }

.note-form-group { margin-bottom: 14px; }
.note-form-group label {
    display: block; font-size: 0.82rem; font-weight: 600;
    color: #64748b; margin-bottom: 5px;
}
.note-form-group input, .note-form-group select, .note-form-group textarea {
    width: 100%; padding: 10px 12px; border-radius: 10px;
    border: 1px solid #e2e8f0; background: #fff; color: #1e293b;
    font-size: 0.88rem; outline: none; transition: border-color .2s;
    box-sizing: border-box;
}
.note-form-group input:focus, .note-form-group select:focus, .note-form-group textarea:focus {
    border-color: #f59e0b;
}
.note-form-group textarea { resize: vertical; min-height: 100px; }
.note-form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 500px) { .note-form-row { grid-template-columns: 1fr; } }

/* Color picker in modal */
.color-picker-row {
    display: flex; gap: 8px; align-items: center; margin-top: 4px;
}
.color-pick {
    width: 30px; height: 30px; border-radius: 50%; cursor: pointer;
    border: 3px solid transparent; transition: all .2s;
}
.color-pick:hover, .color-pick.active { border-color: #1e293b; transform: scale(1.1); }

.btn-note-submit {
    width: 100%; padding: 12px; border: none; border-radius: 10px;
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: #fff; font-weight: 600; font-size: 0.95rem;
    cursor: pointer; transition: all .3s; margin-top: 8px;
}
.btn-note-submit:hover { box-shadow: 0 6px 20px rgba(245,158,11,0.4); }

.btn-reminder-submit {
    width: 100%; padding: 12px; border: none; border-radius: 10px;
    background: linear-gradient(135deg, #6d28d9, #7c3aed);
    color: #fff; font-weight: 600; font-size: 0.95rem;
    cursor: pointer; transition: all .3s; margin-top: 8px;
}

/* Empty state */
.notes-empty {
    text-align: center; padding: 60px 20px; color: #94a3b8;
}
.notes-empty svg { width: 60px; height: 60px; opacity: 0.3; margin-bottom: 12px; }
.notes-empty p { font-size: 0.95rem; margin: 0; }

/* Notification permission banner */
.notif-banner {
    background: linear-gradient(135deg, #ede9fe, #dbeafe);
    padding: 12px 20px; border-radius: 12px; margin-bottom: 16px;
    display: flex; align-items: center; justify-content: space-between;
    border: 1px solid #c7d2fe;
}
.notif-banner p { margin: 0; font-size: 0.85rem; color: #4338ca; font-weight: 500; }
.notif-banner button {
    padding: 8px 16px; border: none; border-radius: 8px;
    background: #6d28d9; color: #fff; font-weight: 600; cursor: pointer;
    font-size: 0.82rem;
}
</style>
@endpush

@section('content')
<div class="notes-page">
    <!-- Notification permission banner -->
    <div class="notif-banner" id="notifBanner" style="display:none;">
        <p>🔔 Bật thông báo trình duyệt để nhận nhắc nhở đúng giờ</p>
        <button onclick="requestNotifPermission()">Bật Thông Báo</button>
    </div>

    <!-- Header -->
    <div class="notes-header">
        <h1>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
            </svg>
            Ghi Chú & Nhắc Nhở
        </h1>
        <div class="notes-header-actions">
            <div class="notes-view-toggle">
                <button id="btnGridView" class="active" onclick="toggleNoteView('grid')">
                    <i class="fas fa-th-large"></i> Ghi Chú
                </button>
                <button id="btnCalView" onclick="toggleNoteView('calendar')">
                    <i class="fas fa-calendar-alt"></i> Lịch
                </button>
            </div>
            <button class="btn-create-reminder" onclick="openReminderModal()">
                <i class="fas fa-bell"></i> Nhắc Nhở
            </button>
            <button class="btn-create-note" onclick="openNoteModal()">
                <i class="fas fa-plus"></i> Ghi Chú
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="notes-filters">
        <input type="text" id="noteSearch" placeholder="🔍 Tìm kiếm ghi chú..." onkeyup="filterNotes()">
        <div class="color-filter-group">
            <div class="color-dot" style="background:#fef3c7;border:1px solid #fbbf24;" data-color="#fef3c7" onclick="filterByColor(this)"></div>
            <div class="color-dot" style="background:#dbeafe;border:1px solid #60a5fa;" data-color="#dbeafe" onclick="filterByColor(this)"></div>
            <div class="color-dot" style="background:#fce7f3;border:1px solid #f472b6;" data-color="#fce7f3" onclick="filterByColor(this)"></div>
            <div class="color-dot" style="background:#ede9fe;border:1px solid #a78bfa;" data-color="#ede9fe" onclick="filterByColor(this)"></div>
            <div class="color-dot" style="background:#ffedd5;border:1px solid #fb923c;" data-color="#ffedd5" onclick="filterByColor(this)"></div>
            <div class="color-dot" style="background:#f1f5f9;border:1px solid #94a3b8;" data-color="#f1f5f9" onclick="filterByColor(this)"></div>
        </div>
        <button class="filter-btn" id="filterPin" onclick="toggleFilterPin()">📌 Đã Ghim</button>
        <button class="filter-btn" id="filterReminder" onclick="toggleFilterReminder()">⏰ Có Nhắc</button>
    </div>

    <!-- Grid View -->
    <div class="notes-grid-view" id="gridView">
        @php
            $pinned = $notes->where('ghim', true);
            $unpinned = $notes->where('ghim', false);
        @endphp

        @if($pinned->count() > 0)
            <div class="notes-section-title">📌 ĐÃ GHIM</div>
            <div class="notes-grid" style="margin-bottom:24px;">
                @foreach($pinned as $note)
                    @include('main.notes._card', ['note' => $note])
                @endforeach
            </div>
            @if($unpinned->count() > 0)
                <div class="notes-section-title">📝 KHÁC</div>
            @endif
        @endif

        @if($unpinned->count() > 0)
            <div class="notes-grid">
                @foreach($unpinned as $note)
                    @include('main.notes._card', ['note' => $note])
                @endforeach
            </div>
        @endif

        @if($notes->isEmpty())
            <div class="notes-empty">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                <p>Chưa có ghi chú nào. Bấm <strong>"+ Ghi Chú"</strong> để bắt đầu!</p>
            </div>
        @endif
    </div>

    <!-- Calendar View -->
    <div class="notes-calendar-view" id="calendarView">
        <div class="calendar-wrapper">
            <div class="calendar-header">
                <button class="calendar-nav" style="border:none;background:none;">
                    <button onclick="changeMonth(-1)">‹</button>
                </button>
                <h3 id="calendarTitle">Tháng {{ $month }}, {{ $year }}</h3>
                <button class="calendar-nav" style="border:none;background:none;">
                    <button onclick="changeMonth(1)">›</button>
                </button>
            </div>
            <div class="calendar-grid" id="calendarGrid">
                <!-- Rendered by JS -->
            </div>
        </div>
        <div class="day-detail-panel" id="dayDetailPanel">
            <h4 id="dayDetailTitle">Nhắc nhở ngày ...</h4>
            <ul class="day-reminder-list" id="dayReminderList"></ul>
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- NOTE MODAL                                     -->
<!-- ============================================= -->
<div class="note-modal-overlay" id="noteModal">
    <div class="note-modal">
        <div class="note-modal-header">
            <h2 id="noteModalTitle">Tạo Ghi Chú Mới</h2>
            <button class="note-modal-close" onclick="closeNoteModal()">&times;</button>
        </div>
        <div class="note-modal-body">
            <form id="noteForm" onsubmit="submitNote(event)">
                <input type="hidden" id="noteId" value="">

                <div class="note-form-group">
                    <label>Tiêu đề *</label>
                    <input type="text" id="noteTitle" required placeholder="Tiêu đề ghi chú...">
                </div>
                <div class="note-form-group">
                    <label>Nội dung</label>
                    <textarea id="noteContent" placeholder="Viết ghi chú..."></textarea>
                </div>
                <div class="note-form-group">
                    <label>Màu sắc</label>
                    <div class="color-picker-row">
                        <div class="color-pick active" style="background:#fef3c7" data-color="#fef3c7" onclick="pickColor(this)"></div>
                        <div class="color-pick" style="background:#dbeafe" data-color="#dbeafe" onclick="pickColor(this)"></div>
                        <div class="color-pick" style="background:#fce7f3" data-color="#fce7f3" onclick="pickColor(this)"></div>
                        <div class="color-pick" style="background:#ede9fe" data-color="#ede9fe" onclick="pickColor(this)"></div>
                        <div class="color-pick" style="background:#ffedd5" data-color="#ffedd5" onclick="pickColor(this)"></div>
                        <div class="color-pick" style="background:#f1f5f9" data-color="#f1f5f9" onclick="pickColor(this)"></div>
                    </div>
                </div>
                <div class="note-form-group">
                    <label>⏰ Đặt Nhắc Nhở (tùy chọn)</label>
                    <div class="note-form-row">
                        <input type="text" id="noteReminderTime" placeholder="dd/mm/yyyy HH:mm">
                        <select id="noteReminderRepeat">
                            <option value="khong">Không lặp</option>
                            <option value="hang_ngay">Hằng ngày</option>
                            <option value="hang_tuan">Hằng tuần</option>
                            <option value="hang_thang">Hằng tháng</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-note-submit" id="btnNoteSubmit">Tạo Ghi Chú</button>
            </form>
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- REMINDER MODAL                                 -->
<!-- ============================================= -->
<div class="note-modal-overlay" id="reminderModal">
    <div class="note-modal">
        <div class="note-modal-header">
            <h2>Tạo Nhắc Nhở</h2>
            <button class="note-modal-close" onclick="closeReminderModal()">&times;</button>
        </div>
        <div class="note-modal-body">
            <form id="reminderForm" onsubmit="submitReminder(event)">
                <div class="note-form-group">
                    <label>Tiêu đề *</label>
                    <input type="text" id="reminderTitle" required placeholder="Nhắc nhở gì...">
                </div>
                <div class="note-form-row">
                    <div class="note-form-group">
                        <label>Thời gian *</label>
                        <input type="text" id="reminderTime" required placeholder="dd/mm/yyyy HH:mm">
                    </div>
                    <div class="note-form-group">
                        <label>Lặp lại</label>
                        <select id="reminderRepeat">
                            <option value="khong">Không lặp</option>
                            <option value="hang_ngay">Hằng ngày</option>
                            <option value="hang_tuan">Hằng tuần</option>
                            <option value="hang_thang">Hằng tháng</option>
                        </select>
                    </div>
                </div>
                <button type="submit" class="btn-reminder-submit">🔔 Tạo Nhắc Nhở</button>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
let currentMonth = {{ $month }};
let currentYear = {{ $year }};
let selectedColor = '#fef3c7';
let calendarData = {};

// ============================================
// DATE FORMAT HELPERS (dd/mm/yyyy HH:mm)
// ============================================
function toDisplayDate(serverDate) {
    // "2026-03-08 15:30:00" or "2026-03-08T15:30" => "08/03/2026 15:30"
    if (!serverDate) return '';
    const s = serverDate.replace('T', ' ');
    const parts = s.split(' ');
    const dateParts = parts[0].split('-');
    const time = parts[1] ? parts[1].substring(0, 5) : '00:00';
    return `${dateParts[2]}/${dateParts[1]}/${dateParts[0]} ${time}`;
}

function toServerDate(displayDate) {
    // "08/03/2026 15:30" => "2026-03-08 15:30:00"
    if (!displayDate || !displayDate.trim()) return null;
    const parts = displayDate.trim().split(' ');
    const dateParts = parts[0].split('/');
    const time = parts[1] || '00:00';
    if (dateParts.length !== 3) return null;
    return `${dateParts[2]}-${dateParts[1].padStart(2,'0')}-${dateParts[0].padStart(2,'0')} ${time}:00`;
}

// Init Flatpickr
const fpConfig = {
    enableTime: true,
    time_24hr: true,
    dateFormat: 'd/m/Y H:i',
    locale: 'vn',
    allowInput: true,
    disableMobile: true,
};
const fpNoteReminder = flatpickr('#noteReminderTime', fpConfig);
const fpReminderTime = flatpickr('#reminderTime', fpConfig);

// ============================================
// VIEW TOGGLE
// ============================================
function toggleNoteView(mode) {
    document.getElementById('btnGridView').classList.toggle('active', mode === 'grid');
    document.getElementById('btnCalView').classList.toggle('active', mode === 'calendar');
    document.getElementById('gridView').classList.toggle('hidden', mode !== 'grid');
    document.getElementById('calendarView').classList.toggle('active', mode === 'calendar');
    if (mode === 'calendar') loadCalendar();
}

// ============================================
// FILTERS (client-side)
// ============================================
let filterColorActive = null;
let filterPinActive = false;
let filterReminderActive = false;

function filterNotes() {
    const search = document.getElementById('noteSearch').value.toLowerCase();
    document.querySelectorAll('.note-card').forEach(card => {
        let show = true;
        if (search && !card.dataset.title.toLowerCase().includes(search) && !card.dataset.content.toLowerCase().includes(search)) show = false;
        if (filterColorActive && card.dataset.color !== filterColorActive) show = false;
        if (filterPinActive && card.dataset.pin !== '1') show = false;
        if (filterReminderActive && card.dataset.hasreminder !== '1') show = false;
        card.style.display = show ? '' : 'none';
    });
}

function filterByColor(el) {
    if (el.classList.contains('active')) {
        el.classList.remove('active');
        filterColorActive = null;
    } else {
        document.querySelectorAll('.color-filter-group .color-dot').forEach(d => d.classList.remove('active'));
        el.classList.add('active');
        filterColorActive = el.dataset.color;
    }
    filterNotes();
}

function toggleFilterPin() {
    filterPinActive = !filterPinActive;
    document.getElementById('filterPin').classList.toggle('active', filterPinActive);
    filterNotes();
}

function toggleFilterReminder() {
    filterReminderActive = !filterReminderActive;
    document.getElementById('filterReminder').classList.toggle('active', filterReminderActive);
    filterNotes();
}

// ============================================
// COLOR PICKER
// ============================================
function pickColor(el) {
    document.querySelectorAll('.color-pick').forEach(c => c.classList.remove('active'));
    el.classList.add('active');
    selectedColor = el.dataset.color;
}

// ============================================
// NOTE MODAL
// ============================================
function openNoteModal(note = null) {
    if (note) {
        document.getElementById('noteId').value = note.id;
        document.getElementById('noteTitle').value = note.tieu_de;
        document.getElementById('noteContent').value = note.noi_dung || '';
        selectedColor = note.mau_sac;
        document.querySelectorAll('.color-pick').forEach(c => {
            c.classList.toggle('active', c.dataset.color === note.mau_sac);
        });
        if (note.active_reminder) {
            document.getElementById('noteReminderTime').value = toDisplayDate(note.active_reminder.thoi_gian);
            document.getElementById('noteReminderRepeat').value = note.active_reminder.lap_lai;
        }
        document.getElementById('noteModalTitle').textContent = 'Sửa Ghi Chú';
        document.getElementById('btnNoteSubmit').textContent = 'Lưu Thay Đổi';
    } else {
        document.getElementById('noteForm').reset();
        document.getElementById('noteId').value = '';
        document.getElementById('noteModalTitle').textContent = 'Tạo Ghi Chú Mới';
        document.getElementById('btnNoteSubmit').textContent = 'Tạo Ghi Chú';
        selectedColor = '#fef3c7';
        document.querySelectorAll('.color-pick').forEach(c => c.classList.toggle('active', c.dataset.color === '#fef3c7'));
    }
    document.getElementById('noteModal').classList.add('active');
}

function closeNoteModal() { document.getElementById('noteModal').classList.remove('active'); }

function submitNote(e) {
    e.preventDefault();
    const noteId = document.getElementById('noteId').value;
    const isEdit = !!noteId;

    const data = {
        tieu_de: document.getElementById('noteTitle').value,
        noi_dung: document.getElementById('noteContent').value,
        mau_sac: selectedColor,
        reminder_time: toServerDate(document.getElementById('noteReminderTime').value),
        reminder_repeat: document.getElementById('noteReminderRepeat').value,
    };

    fetch(isEdit ? `/notes/${noteId}` : '/notes', {
        method: isEdit ? 'PUT' : 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) { closeNoteModal(); location.reload(); }
        else alert(res.message || 'Lỗi');
    })
    .catch(() => alert('Lỗi kết nối'));
}

// ============================================
// NOTE ACTIONS
// ============================================
function togglePin(id, e) {
    e.stopPropagation();
    fetch(`/notes/${id}/pin`, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => location.reload());
}

function editNote(id, e) {
    e.stopPropagation();
    // Get note data from the card
    const card = document.querySelector(`.note-card[data-id="${id}"]`);
    openNoteModal({
        id: id,
        tieu_de: card.dataset.title,
        noi_dung: card.dataset.content,
        mau_sac: card.dataset.color,
        active_reminder: card.dataset.remindertime ? {
            thoi_gian: card.dataset.remindertime,
            lap_lai: card.dataset.reminderrepeat || 'khong'
        } : null
    });
}

function deleteNote(id, e) {
    e.stopPropagation();
    if (!confirm('Xóa ghi chú này?')) return;
    fetch(`/notes/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => location.reload());
}

// ============================================
// REMINDER MODAL
// ============================================
function openReminderModal() { document.getElementById('reminderModal').classList.add('active'); }
function closeReminderModal() { document.getElementById('reminderModal').classList.remove('active'); }

function submitReminder(e) {
    e.preventDefault();
    fetch('/notes/reminder', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({
            tieu_de: document.getElementById('reminderTitle').value,
            thoi_gian: toServerDate(document.getElementById('reminderTime').value),
            lap_lai: document.getElementById('reminderRepeat').value,
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) { closeReminderModal(); location.reload(); }
        else alert(res.message || 'Lỗi');
    });
}

// ============================================
// CALENDAR
// ============================================
const monthNames = ['Tháng 1','Tháng 2','Tháng 3','Tháng 4','Tháng 5','Tháng 6','Tháng 7','Tháng 8','Tháng 9','Tháng 10','Tháng 11','Tháng 12'];

function changeMonth(delta) {
    currentMonth += delta;
    if (currentMonth > 12) { currentMonth = 1; currentYear++; }
    if (currentMonth < 1) { currentMonth = 12; currentYear--; }
    loadCalendar();
}

function loadCalendar() {
    document.getElementById('calendarTitle').textContent = `${monthNames[currentMonth - 1]}, ${currentYear}`;

    fetch(`/notes/calendar-data?month=${currentMonth}&year=${currentYear}`, {
        headers: { 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(data => {
        calendarData = data;
        renderCalendar();
    });
}

function renderCalendar() {
    const grid = document.getElementById('calendarGrid');
    const days = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
    let html = days.map(d => `<div class="calendar-day-header">${d}</div>`).join('');

    const firstDay = new Date(currentYear, currentMonth - 1, 1);
    const lastDay = new Date(currentYear, currentMonth, 0);
    let startDow = firstDay.getDay(); // 0=Sun
    startDow = startDow === 0 ? 6 : startDow - 1; // Convert to Mon=0

    const today = new Date();
    const todayStr = `${today.getFullYear()}-${String(today.getMonth()+1).padStart(2,'0')}-${String(today.getDate()).padStart(2,'0')}`;

    // Previous month days
    const prevLast = new Date(currentYear, currentMonth - 1, 0).getDate();
    for (let i = startDow - 1; i >= 0; i--) {
        html += `<div class="calendar-day other-month"><div class="day-number">${prevLast - i}</div></div>`;
    }

    // Current month days
    for (let d = 1; d <= lastDay.getDate(); d++) {
        const dateStr = `${currentYear}-${String(currentMonth).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
        const isToday = dateStr === todayStr;
        const reminders = calendarData[dateStr] || [];

        let dotsHtml = '';
        reminders.forEach(r => {
            const color = r.note ? (r.note.mau_sac || '#f59e0b') : '#6d28d9';
            const bgColor = r.trang_thai === 'hoan_thanh' ? '#94a3b8' : color;
            dotsHtml += `<div class="calendar-reminder-item" style="background:${bgColor}">${r.tieu_de}</div>`;
        });

        html += `<div class="calendar-day${isToday ? ' today' : ''}" onclick="showDayDetail('${dateStr}')">
            <div class="day-number">${d}</div>
            ${dotsHtml}
        </div>`;
    }

    // Next month days
    const totalCells = startDow + lastDay.getDate();
    const remaining = totalCells % 7 === 0 ? 0 : 7 - (totalCells % 7);
    for (let i = 1; i <= remaining; i++) {
        html += `<div class="calendar-day other-month"><div class="day-number">${i}</div></div>`;
    }

    grid.innerHTML = html;
}

function showDayDetail(dateStr) {
    const panel = document.getElementById('dayDetailPanel');
    const reminders = calendarData[dateStr] || [];
    const d = new Date(dateStr);
    document.getElementById('dayDetailTitle').textContent = `📅 Nhắc nhở ngày ${d.toLocaleDateString('vi-VN')}`;

    if (reminders.length === 0) {
        document.getElementById('dayReminderList').innerHTML = '<li style="color:#94a3b8;text-align:center;padding:20px;">Không có nhắc nhở</li>';
    } else {
        document.getElementById('dayReminderList').innerHTML = reminders.map(r => {
            const time = new Date(r.thoi_gian).toLocaleTimeString('vi-VN', {hour:'2-digit', minute:'2-digit'});
            const isDone = r.trang_thai === 'hoan_thanh';
            return `<li class="day-reminder-item" style="${isDone ? 'opacity:0.5;' : ''}">
                <div class="reminder-info">
                    <div class="reminder-title">${isDone ? '✅ ' : '🔔 '}${r.tieu_de}</div>
                    <div class="reminder-time">${time}${r.lap_lai !== 'khong' ? ' • 🔁 ' + r.lap_lai : ''}</div>
                </div>
                <div class="reminder-actions">
                    ${!isDone ? `<button class="btn-complete-reminder" onclick="completeReminder(${r.id})">✓ Xong</button>` : ''}
                    <button class="btn-delete-reminder" onclick="deleteReminder(${r.id})">✕</button>
                </div>
            </li>`;
        }).join('');
    }

    panel.classList.add('active');
}

function completeReminder(id) {
    fetch(`/notes/reminder/${id}/complete`, {
        method: 'PUT',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => loadCalendar());
}

function deleteReminder(id) {
    if (!confirm('Xóa nhắc nhở?')) return;
    fetch(`/notes/reminder/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => { loadCalendar(); document.getElementById('dayDetailPanel').classList.remove('active'); });
}

// ============================================
// BROWSER NOTIFICATION
// ============================================
function requestNotifPermission() {
    if ('Notification' in window) {
        Notification.requestPermission().then(perm => {
            if (perm === 'granted') {
                document.getElementById('notifBanner').style.display = 'none';
            }
        });
    }
}

function checkBrowserNotifPermission() {
    if ('Notification' in window) {
        if (Notification.permission === 'default') {
            document.getElementById('notifBanner').style.display = 'flex';
        } else {
            document.getElementById('notifBanner').style.display = 'none';
        }
    }
}

// Polling for reminders
function pollReminders() {
    fetch('/notes/reminders/check', {
        headers: { 'X-CSRF-TOKEN': CSRF }
    })
    .then(r => r.json())
    .then(reminders => {
        reminders.forEach(r => {
            showBrowserNotification(r.tieu_de, r.note ? r.note.tieu_de : '');
        });
    })
    .catch(() => {});
}

function showBrowserNotification(title, body) {
    // Play sound
    try {
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdH2JkZeWk4+GfnZxb3J4gIeNkpSUkIuFfnhzbW1vc3mAhoyQkpKQjIaDfXhzbW1vdHqAhoyPkZGPjIaCfXhzbm5wdXuBh4uPkJCOi4WBfHZxbm5wdXuBh4uOj4+NioWBfHZxbm9xdnyChomMjo6MiYSAfnlzbm9xdnyChomMjY2LiIOAfnlzbm9xdn2Dh4qLjIuKh4J+eXRvb3F2fYOHiouLioiEgH15dG9vcXZ9g4eKi4uKiISAfXl0b3BydnyDh4qLi4qIhIB9eXRvb3F2');
        audio.volume = 0.5;
        audio.play().catch(() => {});
    } catch(e) {}

    // Browser notification
    if ('Notification' in window && Notification.permission === 'granted') {
        const n = new Notification('🔔 ' + title, {
            body: body || 'Nhắc nhở từ hệ thống',
            icon: '/favicon.ico',
            tag: 'reminder-' + Date.now(),
        });
        n.onclick = function() {
            window.focus();
            n.close();
        };
    }
}

// Init
checkBrowserNotifPermission();
pollReminders();
setInterval(pollReminders, 60000); // Poll every 60s

// Close modals
document.getElementById('noteModal').addEventListener('click', function(e) { if (e.target === this) closeNoteModal(); });
document.getElementById('reminderModal').addEventListener('click', function(e) { if (e.target === this) closeReminderModal(); });
</script>
@endpush
