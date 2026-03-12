@extends('main.layouts.app')
@section('title', 'Quản Lý Công Việc')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/themes/material_blue.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
/* ============================================= */
/*  TASK MANAGEMENT - KANBAN BOARD               */
/* ============================================= */

/* Select2 dark theme override */
.select2-container--default .select2-selection--multiple {
    background: rgba(255,255,255,0.05) !important;
    border: 1px solid rgba(255,255,255,0.1) !important;
    border-radius: 10px !important;
    padding: 6px !important;
    min-height: 42px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice {
    background: #6d28d9 !important;
    border: none !important; color: #fff !important;
    border-radius: 20px !important; padding: 4px 12px !important;
    font-size: 0.78rem !important; margin: 3px 4px !important;
    line-height: 1.4 !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove,
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
    display: none !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered li.select2-search--inline {
    margin: 3px 4px !important;
}
.select2-container--default .select2-selection--multiple .select2-selection__rendered li.select2-search--inline .select2-search__field {
    color: #cbd5e1 !important; font-size: 0.85rem !important;
}
.select2-dropdown {
    background: #1e1b4b !important;
    border: 1px solid rgba(167,139,250,0.25) !important;
    border-radius: 10px !important;
    box-shadow: 0 8px 30px rgba(0,0,0,0.4) !important;
    overflow: hidden !important;
}
.select2-container--default .select2-search--dropdown {
    padding: 8px !important;
}
.select2-container--default .select2-search--dropdown .select2-search__field {
    background: rgba(255,255,255,0.08) !important; color: #e2e8f0 !important;
    border: 1px solid rgba(255,255,255,0.12) !important; border-radius: 8px !important;
    padding: 8px 12px !important; outline: none !important;
}
.select2-results__options { padding: 4px !important; }
.select2-container--default .select2-results__option {
    color: #e2e8f0 !important; padding: 9px 14px !important;
    border-radius: 6px !important; margin: 1px 0 !important;
    font-size: 0.85rem !important;
    background: transparent !important;
    transition: none !important;
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: rgba(109,40,217,0.35) !important; color: #fff !important;
}
.select2-container--default .select2-results__option[aria-selected=true] {
    background: rgba(109,40,217,0.15) !important;
    color: #a78bfa !important;
}
.select2-container--default .select2-results__option--highlighted[aria-selected=true] {
    background: rgba(109,40,217,0.35) !important;
    color: #a78bfa !important;
}
.select2-container--default .select2-results__option[aria-selected=true]::after {
    content: ' ✓'; color: #a78bfa; font-weight: 700;
}
.select2-container--open .select2-dropdown { z-index: 10001 !important; }

.task-page { padding: 10px; background:white;border-radius: 10px;box-shadow: 0 4px 15px rgba(0,0,0,0.2); }

/* Header */
.task-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 12px; margin-bottom: 20px;
}
.task-header h1 {
    font-size: 1.6rem; font-weight: 700; color: #000000ff;
    display: flex; align-items: center; gap: 10px; margin: 0;
}
.task-header h1 svg { width: 28px; height: 28px; color: #a78bfa; }
.task-header-actions { display: flex; gap: 10px; flex-wrap: wrap; }

/* View toggle */
.view-toggle {
    display: flex; background: rgba(255,255,255,0.05); border-radius: 10px;
    overflow: hidden; border: 1px solid rgba(255,255,255,0.08);
}
.view-toggle button {
    padding: 8px 16px; border: none; background: none; color: #94a3b8;
    cursor: pointer; font-size: 0.85rem; transition: all .2s;
    display: flex; align-items: center; gap: 6px;
}
.view-toggle button.active {
    background: linear-gradient(135deg, #6d28d9, #7c3aed);
    color: #fff; font-weight: 600;
}
.view-toggle button:hover:not(.active) { background: rgba(255,255,255,0.05); color: #e2e8f0; }

/* Buttons */
.btn-create-task {
    padding: 10px 20px; border: none; border-radius: 10px;
    background: linear-gradient(135deg, #6d28d9, #7c3aed);
    color: #fff; font-weight: 600; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    transition: all .3s; box-shadow: 0 4px 15px rgba(109,40,217,0.3);
}
.btn-create-task:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(109,40,217,0.4); }

/* Filter Bar */
.task-filters {
    display: flex; gap: 10px; align-items: center; flex-wrap: wrap;
    margin-bottom: 20px; padding: 12px 16px;
    background: rgba(255,255,255,0.03); border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.06);
}
.task-filters select, .task-filters input[type="text"] {
    padding: 8px 12px; border-radius: 8px; border: 1px solid #aeaeae;
    background: rgba(255,255,255,0.05); color: #000000ff; font-size: 0.85rem;
    outline: none; min-width: 140px;
}
.task-filters select option { background: #1e1b4b; color: #e2e8f0; }
.task-filters input::placeholder { color: #64748b; }

/* ============================================= */
/*  KANBAN BOARD                                  */
/* ============================================= */

.kanban-board {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px;
    min-height: 60vh;
}
@media (max-width: 1200px) { .kanban-board { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 768px) { .kanban-board { grid-template-columns: 1fr; } }

.kanban-column {
    background: rgba(255,255,255,0.03); border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.06);
    display: flex; flex-direction: column; min-height: 300px;
}
.kanban-column-header {
    padding: 14px 16px; border-bottom: 1px solid rgba(255,255,255,0.06);
    display: flex; align-items: center; justify-content: space-between;
    font-weight: 700; font-size: 0.9rem;
}
.kanban-column-header .col-count {
    background: rgba(255,255,255,0.1); padding: 2px 10px; border-radius: 20px;
    font-size: 0.75rem; font-weight: 600; color: #94a3b8;
}
.kanban-column[data-status="chua_bat_dau"] .kanban-column-header { color: #94a3b8; border-bottom-color: rgba(148,163,184,0.2); }
.kanban-column[data-status="dang_lam"] .kanban-column-header { color: #60a5fa; border-bottom-color: rgba(96,165,250,0.2); }
.kanban-column[data-status="cho_duyet"] .kanban-column-header { color: #fbbf24; border-bottom-color: rgba(251,191,36,0.2); }
.kanban-column[data-status="hoan_thanh"] .kanban-column-header { color: #34d399; border-bottom-color: rgba(52,211,153,0.2); }

.kanban-column-body {
    padding: 10px; flex: 1; overflow-y: auto;
    min-height: 100px;
}
.kanban-column-body.drag-over {
    background: rgba(109,40,217,0.08); border: 2px dashed rgba(109,40,217,0.3);
    border-radius: 10px;
}

/* ============================================= */
/*  TASK CARD                                     */
/* ============================================= */

.task-card {
    background: rgba(255,255,255,0.05); border-radius: 12px;
    border: 1px solid rgba(255,255,255,0.08);
    padding: 14px; margin-bottom: 10px; cursor: grab;
    transition: all .2s ease; position: relative;
}
.task-card:hover {
    border-color: rgba(109,40,217,0.3);
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    transform: translateY(-2px);
}
.task-card.dragging { opacity: 0.4; transform: rotate(2deg); }

.task-card-labels {
    display: flex; gap: 4px; flex-wrap: wrap; margin-bottom: 8px;
}
.task-label-badge {
    padding: 2px 8px; border-radius: 6px; font-size: 0.7rem;
    font-weight: 600; color: #fff;
}

.task-card-title {
    font-size: 0.9rem; font-weight: 600; color: #e2e8f0;
    margin-bottom: 8px; line-height: 1.3;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}

.task-card-meta {
    display: flex; align-items: center; justify-content: space-between;
    font-size: 0.75rem; color: #64748b; gap: 8px;
}
.task-card-meta-left { display: flex; align-items: center; gap: 8px; }
.task-card-meta-right { display: flex; align-items: center; gap: 6px; }

.card-priority {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
}
.card-priority.khan_cap { background: #ef4444; box-shadow: 0 0 6px rgba(239,68,68,0.5); }
.card-priority.cao { background: #f59e0b; }
.card-priority.trung_binh { background: #60a5fa; }
.card-priority.thap { background: #6b7280; }

.card-deadline { display: flex; align-items: center; gap: 4px; }
.card-deadline.overdue { color: #ef4444; font-weight: 600; }
.card-deadline.soon { color: #f59e0b; }

.card-avatars { display: flex; }
.card-avatar {
    width: 24px; height: 24px; border-radius: 50%;
    border: 2px solid #1e1b4b; margin-left: -6px; object-fit: cover;
}
.card-avatar:first-child { margin-left: 0; }

.card-stats { display: flex; align-items: center; gap: 8px; font-size: 0.72rem; color: #64748b; }
.card-stats span { display: flex; align-items: center; gap: 3px; }

/* Progress bar mini */
.card-progress {
    height: 3px; background: rgba(255,255,255,0.08); border-radius: 4px;
    margin-top: 10px; overflow: hidden;
}
.card-progress-fill {
    height: 100%; border-radius: 4px; transition: width .3s;
    background: linear-gradient(90deg, #6d28d9, #a78bfa);
}

/* ============================================= */
/*  LIST VIEW                                     */
/* ============================================= */

.task-list-view { display: none; }
.task-list-view.active { display: block; }
.kanban-board.hidden { display: none; }

.task-list-table {
    width: 100%; border-collapse: separate; border-spacing: 0;
    background: rgba(255,255,255,0.03); border-radius: 14px;
    border: 1px solid rgba(255,255,255,0.06); overflow: hidden;
}
.task-list-table thead th {
    padding: 12px 16px; font-size: 0.8rem; font-weight: 600; color: #94a3b8;
    text-align: left; border-bottom: 1px solid rgba(255,255,255,0.06);
    background: rgba(255,255,255,0.02);
}
.task-list-table tbody tr {
    cursor: pointer; transition: background .2s;
}
.task-list-table tbody tr:hover { background: rgba(255,255,255,0.04); }
.task-list-table tbody td {
    padding: 12px 16px; font-size: 0.85rem; color: #cbd5e1;
    border-bottom: 1px solid rgba(255,255,255,0.04);
}

.status-badge {
    padding: 4px 10px; border-radius: 8px; font-size: 0.72rem;
    font-weight: 600; display: inline-block;
}
.status-badge.chua_bat_dau { background: rgba(148,163,184,0.15); color: #94a3b8; }
.status-badge.dang_lam { background: rgba(96,165,250,0.15); color: #60a5fa; }
.status-badge.cho_duyet { background: rgba(251,191,36,0.15); color: #fbbf24; }
.status-badge.hoan_thanh { background: rgba(52,211,153,0.15); color: #34d399; }
.status-badge.huy { background: rgba(239,68,68,0.15); color: #ef4444; }

.priority-badge {
    padding: 4px 10px; border-radius: 8px; font-size: 0.72rem; font-weight: 600;
}
.priority-badge.khan_cap { background: rgba(239,68,68,0.15); color: #ef4444; }
.priority-badge.cao { background: rgba(245,158,11,0.15); color: #f59e0b; }
.priority-badge.trung_binh { background: rgba(96,165,250,0.15); color: #60a5fa; }
.priority-badge.thap { background: rgba(107,114,128,0.15); color: #6b7280; }

/* ============================================= */
/*  MODAL                                         */
/* ============================================= */

.task-modal-overlay {
    position: fixed; inset: 0; background: rgba(0,0,0,0.6);
    z-index: 9999; display: none; align-items: center; justify-content: center;
    backdrop-filter: blur(4px);
}
.task-modal-overlay.active { display: flex; }

.task-modal {
    background: linear-gradient(160deg, #1e1b4b 0%, #0f172a 100%);
    border-radius: 16px; width: 95%; max-width: 720px;
    max-height: 90vh; overflow-y: auto;
    border: 1px solid rgba(255,255,255,0.08);
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}
.task-modal-header {
    display: flex; align-items: center; justify-content: space-between;
    padding: 20px 24px; border-bottom: 1px solid rgba(255,255,255,0.06);
}
.task-modal-header h2 {
    font-size: 1.1rem; font-weight: 700; color: #e2e8f0; margin: 0;
}
.task-modal-close {
    width: 32px; height: 32px; border-radius: 8px; border: none;
    background: rgba(255,255,255,0.05); color: #94a3b8; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; transition: all .2s;
}
.task-modal-close:hover { background: rgba(239,68,68,0.2); color: #ef4444; }

.task-modal-body { padding: 24px; }

.form-group {
    margin-bottom: 16px;
}
.form-group label {
    display: block; font-size: 0.82rem; font-weight: 600;
    color: #94a3b8; margin-bottom: 6px;
}
.form-group input, .form-group select, .form-group textarea {
    width: 100%; padding: 10px 14px; border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.05); color: #e2e8f0;
    font-size: 0.88rem; outline: none; transition: border-color .2s;
    box-sizing: border-box;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    border-color: rgba(109,40,217,0.5);
}
.form-group select option { background: #1e1b4b; color: #e2e8f0; }
.form-group textarea { resize: vertical; min-height: 80px; }
.form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 600px) { .form-row { grid-template-columns: 1fr; } }

.form-group .checkbox-group {
    display: flex; flex-wrap: wrap; gap: 8px; max-height: 150px; overflow-y: auto;
    padding: 8px; background: rgba(255,255,255,0.02); border-radius: 8px;
}
.checkbox-group label {
    display: flex; align-items: center; gap: 6px;
    padding: 6px 10px; border-radius: 8px; cursor: pointer;
    background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06);
    font-size: 0.8rem; color: #cbd5e1; margin-bottom: 0;
}
.checkbox-group label:hover { background: rgba(109,40,217,0.1); }
.checkbox-group input[type="checkbox"] { accent-color: #7c3aed; }

.btn-submit {
    width: 100%; padding: 12px; border: none; border-radius: 10px;
    background: linear-gradient(135deg, #6d28d9, #7c3aed);
    color: #fff; font-weight: 600; font-size: 0.95rem;
    cursor: pointer; transition: all .3s; margin-top: 8px;
}
.btn-submit:hover { box-shadow: 0 6px 20px rgba(109,40,217,0.4); }

/* Detail Modal */
.task-detail-section {
    margin-bottom: 20px; padding-bottom: 16px;
    border-bottom: 1px solid rgba(255,255,255,0.06);
}
.task-detail-section:last-child { border-bottom: none; }
.task-detail-section h3 {
    font-size: 0.88rem; font-weight: 700; color: #a78bfa;
    margin: 0 0 12px;
    display: flex; align-items: center; gap: 8px;
}

.task-assignee-list { display: flex; flex-direction: column; gap: 8px; }
.task-assignee-row {
    display: flex; align-items: center; gap: 12px;
    padding: 8px 12px; background: rgba(255,255,255,0.03);
    border-radius: 10px;
}
.task-assignee-row img {
    width: 32px; height: 32px; border-radius: 50%; object-fit: cover;
}
.task-assignee-info { flex: 1; }
.task-assignee-info .name { font-size: 0.85rem; font-weight: 600; color: #e2e8f0; }
.task-assignee-info .role { font-size: 0.72rem; color: #64748b; }
.task-assignee-progress {
    display: flex; align-items: center; gap: 8px; min-width: 120px;
}
.task-assignee-progress input[type="range"] {
    flex: 1; accent-color: #7c3aed; height: 4px;
}
.task-assignee-progress span { font-size: 0.75rem; color: #94a3b8; min-width: 32px; text-align: right; }

/* Subtask list */
.subtask-list { list-style: none; padding: 0; margin: 0; }
.subtask-item {
    display: flex; align-items: center; gap: 10px;
    padding: 8px 0; border-bottom: 1px solid rgba(255,255,255,0.04);
    font-size: 0.85rem; color: #cbd5e1;
}
.subtask-item input[type="checkbox"] { accent-color: #7c3aed; }
.subtask-item.done { text-decoration: line-through; color: #64748b; }

/* Comments */
.comment-list { display: flex; flex-direction: column; gap: 12px; }
.comment-item {
    display: flex; gap: 10px; padding: 10px;
    background: rgba(255,255,255,0.03); border-radius: 10px;
}
.comment-item img {
    width: 32px; height: 32px; border-radius: 50%; object-fit: cover; flex-shrink: 0;
}
.comment-body { flex: 1; }
.comment-body .comment-header {
    display: flex; align-items: center; gap: 8px; margin-bottom: 4px;
}
.comment-body .comment-author { font-size: 0.82rem; font-weight: 600; color: #e2e8f0; }
.comment-body .comment-time { font-size: 0.72rem; color: #64748b; }
.comment-body .comment-text { font-size: 0.85rem; color: #cbd5e1; line-height: 1.5; }
.comment-body .comment-delete {
    font-size: 0.72rem; color: #ef4444; cursor: pointer; margin-left: 8px;
    background: none; border: none;
}

.comment-form {
    display: flex; gap: 8px; margin-top: 12px;
}
.comment-form input {
    flex: 1; padding: 10px 14px; border-radius: 10px;
    border: 1px solid rgba(255,255,255,0.1);
    background: rgba(255,255,255,0.05); color: #e2e8f0; font-size: 0.85rem;
    outline: none;
}
.comment-form button {
    padding: 10px 18px; border: none; border-radius: 10px;
    background: linear-gradient(135deg, #6d28d9, #7c3aed);
    color: #fff; font-weight: 600; cursor: pointer;
}

/* Task actions in detail modal */
.task-detail-actions {
    display: flex; gap: 8px; flex-wrap: wrap; margin-top: 16px;
}
.task-detail-actions button {
    padding: 8px 16px; border-radius: 8px; border: none;
    font-size: 0.82rem; font-weight: 600; cursor: pointer;
    transition: all .2s;
}
.btn-edit-task { background: rgba(96,165,250,0.15); color: #60a5fa; }
.btn-edit-task:hover { background: rgba(96,165,250,0.25); }
.btn-delete-task { background: rgba(239,68,68,0.15); color: #ef4444; }
.btn-delete-task:hover { background: rgba(239,68,68,0.25); }
.btn-approve-task { background: rgba(52,211,153,0.15); color: #34d399; }
.btn-approve-task:hover { background: rgba(52,211,153,0.25); }

/* Label manager */
.label-manager {
    display: flex; gap: 8px; flex-wrap: wrap; margin-bottom: 10px;
}
.label-tag {
    display: flex; align-items: center; gap: 4px; padding: 4px 10px;
    border-radius: 6px; font-size: 0.75rem; font-weight: 600; color: #fff;
}
.label-tag button {
    background: none; border: none; color: rgba(255,255,255,0.7); cursor: pointer;
    font-size: 0.8rem; margin-left: 2px;
}

/* Empty state */
.kanban-empty {
    text-align: center; padding: 30px; color: #475569;
    font-size: 0.85rem;
}
.kanban-empty svg { width: 40px; height: 40px; margin-bottom: 8px; opacity: 0.3; }
</style>
@endpush

@section('content')
<div class="task-page">
    <!-- Header -->
    <div class="task-header">
        <h1>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
            </svg>
            Quản Lý Công Việc
        </h1>
        <div class="task-header-actions">
            <div class="view-toggle">
                <button id="btnKanban" class="active" onclick="toggleView('kanban')">
                    <i class="fas fa-columns"></i> Kanban
                </button>
                <button id="btnList" onclick="toggleView('list')">
                    <i class="fas fa-list"></i> Danh Sách
                </button>
            </div>
            <button class="btn-create-task" onclick="openCreateModal()">
                <i class="fas fa-plus"></i> Tạo Công Việc
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="task-filters">
        <input type="text" id="filterSearch" placeholder="🔍 Tìm kiếm..." onkeyup="applyFilters()">
        <select id="filterPriority" onchange="applyFilters()">
            <option value="">Tất cả ưu tiên</option>
            <option value="khan_cap">🔴 Khẩn cấp</option>
            <option value="cao">🟠 Cao</option>
            <option value="trung_binh">🔵 Trung bình</option>
            <option value="thap">⚪ Thấp</option>
        </select>
        <select id="filterUser" onchange="applyFilters()">
            <option value="">Tất cả người</option>
            @foreach($assignableUsers as $u)
                <option value="{{ $u->id }}">{{ $u->name }}</option>
            @endforeach
        </select>
        @if($isAdmin)
        <select id="filterLabel" onchange="applyFilters()">
            <option value="">Tất cả nhãn</option>
            @foreach($labels as $label)
                <option value="{{ $label->id }}">{{ $label->ten }}</option>
            @endforeach
        </select>
        @endif
    </div>

    <!-- Kanban Board -->
    <div class="kanban-board" id="kanbanBoard">
        @php
            $columns = [
                'chua_bat_dau' => '📋 Chưa Bắt Đầu',
                'dang_lam' => '🔄 Đang Làm',
                'cho_duyet' => '⏳ Chờ Duyệt',
                'hoan_thanh' => '✅ Hoàn Thành',
            ];
        @endphp
        @foreach($columns as $status => $label)
        <div class="kanban-column" data-status="{{ $status }}"
             ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)"
             ondrop="handleDrop(event)">
            <div class="kanban-column-header">
                <span>{{ $label }}</span>
                <span class="col-count">{{ $kanban[$status]->count() }}</span>
            </div>
            <div class="kanban-column-body">
                @forelse($kanban[$status] as $task)
                    @include('main.tasks._card', ['task' => $task])
                @empty
                    <div class="kanban-empty">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                        <div>Chưa có task nào</div>
                    </div>
                @endforelse
            </div>
        </div>
        @endforeach
    </div>

    <!-- List View -->
    <div class="task-list-view" id="listView">
        <table class="task-list-table">
            <thead>
                <tr>
                    <th>Tiêu Đề</th>
                    <th>Ưu Tiên</th>
                    <th>Trạng Thái</th>
                    <th>Phân Công</th>
                    <th>Deadline</th>
                    <th>Tiến Độ</th>
                </tr>
            </thead>
            <tbody>
                @foreach($tasks as $task)
                <tr onclick="openDetailModal({{ $task->id }})">
                    <td>
                        @foreach($task->labels as $label)
                            <span class="task-label-badge" style="background:{{ $label->mau_sac }}">{{ $label->ten }}</span>
                        @endforeach
                        {{ $task->tieu_de }}
                    </td>
                    <td>
                        @php
                            $pMap = ['khan_cap'=>'🔴 Khẩn cấp','cao'=>'🟠 Cao','trung_binh'=>'🔵 TB','thap'=>'⚪ Thấp'];
                        @endphp
                        <span class="priority-badge {{ $task->do_uu_tien }}">{{ $pMap[$task->do_uu_tien] ?? $task->do_uu_tien }}</span>
                    </td>
                    <td>
                        @php
                            $sMap = ['chua_bat_dau'=>'Chưa bắt đầu','dang_lam'=>'Đang làm','cho_duyet'=>'Chờ duyệt','hoan_thanh'=>'Hoàn thành','huy'=>'Hủy'];
                        @endphp
                        <span class="status-badge {{ $task->trang_thai }}">{{ $sMap[$task->trang_thai] ?? $task->trang_thai }}</span>
                    </td>
                    <td>
                        @foreach($task->taskUsers->take(3) as $tu)
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($tu->user->name ?? '?') }}&size=24&background=6d28d9&color=fff&bold=true" class="card-avatar" title="{{ $tu->user->name ?? '' }}">
                        @endforeach
                        @if($task->taskUsers->count() > 3)
                            <span style="color:#64748b;font-size:0.75rem;">+{{ $task->taskUsers->count() - 3 }}</span>
                        @endif
                    </td>
                    <td>
                        @if($task->ngay_ket_thuc)
                            @php
                                $deadline = \Carbon\Carbon::parse($task->ngay_ket_thuc);
                                $now = \Carbon\Carbon::now();
                                $dcls = $deadline->isPast() ? 'overdue' : ($deadline->diffInDays($now) <= 2 ? 'soon' : '');
                            @endphp
                            <span class="card-deadline {{ $dcls }}">📅 {{ $deadline->format('d/m') }}</span>
                        @else
                            <span style="color:#475569">—</span>
                        @endif
                    </td>
                    <td>
                        <div style="display:flex;align-items:center;gap:8px;">
                            <div class="card-progress" style="flex:1;height:6px;">
                                <div class="card-progress-fill" style="width:{{ $task->tien_do_trung_binh }}%"></div>
                            </div>
                            <span style="font-size:0.75rem;color:#94a3b8;">{{ $task->tien_do_trung_binh }}%</span>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ============================================= -->
<!-- CREATE/EDIT MODAL                              -->
<!-- ============================================= -->
<div class="task-modal-overlay" id="createModal">
    <div class="task-modal">
        <div class="task-modal-header">
            <h2 id="createModalTitle">Tạo Công Việc Mới</h2>
            <button class="task-modal-close" onclick="closeCreateModal()">&times;</button>
        </div>
        <div class="task-modal-body">
            <form id="taskForm" onsubmit="submitTask(event)">
                <input type="hidden" id="taskId" value="">
                <input type="hidden" id="taskParentId" value="">

                <div class="form-group">
                    <label>Tiêu đề *</label>
                    <input type="text" id="taskTitle" required placeholder="Nhập tiêu đề công việc...">
                </div>

                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea id="taskDesc" placeholder="Mô tả chi tiết..."></textarea>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Loại</label>
                        <select id="taskType">
                            <option value="ca_nhan">Cá nhân</option>
                            <option value="nhom">Nhóm</option>
                            <option value="phong_ban">Phòng ban</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Ưu tiên</label>
                        <select id="taskPriority">
                            <option value="thap">⚪ Thấp</option>
                            <option value="trung_binh" selected>🔵 Trung bình</option>
                            <option value="cao">🟠 Cao</option>
                            <option value="khan_cap">🔴 Khẩn cấp</option>
                        </select>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày bắt đầu</label>
                        <input type="text" id="taskStartDate" placeholder="dd/mm/yyyy">
                    </div>
                    <div class="form-group">
                        <label>Deadline</label>
                        <input type="text" id="taskEndDate" placeholder="dd/mm/yyyy">
                    </div>
                </div>

                @if($isAdmin || $isTruongPB || $isNhanSu)
                <div class="form-group">
                    <label>Phân công cho</label>
                    <select id="assignUserSelect" multiple="multiple" style="width:100%">
                        @foreach($assignableUsers as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                @endif

                @if($labels->count() > 0)
                <div class="form-group">
                    <label>Nhãn</label>
                    <div class="checkbox-group">
                        @foreach($labels as $label)
                        <label>
                            <input type="checkbox" name="label_ids[]" value="{{ $label->id }}">
                            <span class="task-label-badge" style="background:{{ $label->mau_sac }}">{{ $label->ten }}</span>
                        </label>
                        @endforeach
                    </div>
                </div>
                @endif

                <button type="submit" class="btn-submit" id="btnSubmitTask">
                    <i class="fas fa-plus"></i> Tạo Công Việc
                </button>
            </form>
        </div>
    </div>
</div>

<!-- ============================================= -->
<!-- DETAIL MODAL                                   -->
<!-- ============================================= -->
<div class="task-modal-overlay" id="detailModal">
    <div class="task-modal" style="max-width: 800px;">
        <div class="task-modal-header">
            <h2 id="detailTitle">Chi Tiết Công Việc</h2>
            <button class="task-modal-close" onclick="closeDetailModal()">&times;</button>
        </div>
        <div class="task-modal-body" id="detailBody">
            <!-- Loaded via JS -->
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
const CSRF = document.querySelector('meta[name="csrf-token"]').content;
const isAdmin = @json($isAdmin);
const isNhanSu = @json($isNhanSu ?? false);
const isTruongPB = @json($isTruongPB);

// Date format helpers
function dateToDisplay(serverDate) {
    if (!serverDate) return '';
    const d = serverDate.split('T')[0].split(' ')[0];
    const p = d.split('-');
    return `${p[2]}/${p[1]}/${p[0]}`;
}
function dateToServer(displayDate) {
    if (!displayDate || !displayDate.trim()) return null;
    const p = displayDate.trim().split('/');
    if (p.length !== 3) return null;
    return `${p[2]}-${p[1].padStart(2,'0')}-${p[0].padStart(2,'0')}`;
}

// Init Flatpickr
const fpDateConfig = { dateFormat: 'd/m/Y', locale: 'vn', allowInput: true, disableMobile: true };
const fpStartDate = flatpickr('#taskStartDate', fpDateConfig);
const fpEndDate = flatpickr('#taskEndDate', fpDateConfig);

// Init Select2
$('#assignUserSelect').select2({
    placeholder: 'Chọn người phân công...',
    allowClear: true,
    width: '100%',
    dropdownParent: $('#createModal')
});

// ============================================
// VIEW TOGGLE
// ============================================
function toggleView(mode) {
    document.getElementById('btnKanban').classList.toggle('active', mode === 'kanban');
    document.getElementById('btnList').classList.toggle('active', mode === 'list');
    document.getElementById('kanbanBoard').classList.toggle('hidden', mode !== 'kanban');
    document.getElementById('listView').classList.toggle('active', mode === 'list');
}

// ============================================
// FILTERS (client-side)
// ============================================
function applyFilters() {
    const search = document.getElementById('filterSearch').value.toLowerCase();
    const priority = document.getElementById('filterPriority').value;
    const userId = document.getElementById('filterUser').value;
    const labelEl = document.getElementById('filterLabel');
    const labelId = labelEl ? labelEl.value : '';

    document.querySelectorAll('.task-card').forEach(card => {
        let show = true;
        if (search && !card.dataset.title.toLowerCase().includes(search)) show = false;
        if (priority && card.dataset.priority !== priority) show = false;
        if (userId && !card.dataset.users.includes(',' + userId + ',')) show = false;
        if (labelId && !card.dataset.labels.includes(',' + labelId + ',')) show = false;
        card.style.display = show ? '' : 'none';
    });

    // List view
    document.querySelectorAll('.task-list-table tbody tr').forEach(row => {
        let show = true;
        if (search && !row.textContent.toLowerCase().includes(search)) show = false;
        row.style.display = show ? '' : 'none';
    });

    // Update counts
    document.querySelectorAll('.kanban-column').forEach(col => {
        const visible = col.querySelectorAll('.task-card:not([style*="display: none"])').length;
        col.querySelector('.col-count').textContent = visible;
    });
}

// ============================================
// DRAG & DROP
// ============================================
function handleDragStart(e) {
    e.dataTransfer.setData('text/plain', e.target.dataset.id);
    e.target.classList.add('dragging');
}
function handleDragEnd(e) {
    e.target.classList.remove('dragging');
    document.querySelectorAll('.kanban-column-body').forEach(b => b.classList.remove('drag-over'));
}
function handleDragOver(e) {
    e.preventDefault();
    const body = e.target.closest('.kanban-column')?.querySelector('.kanban-column-body');
    if (body) body.classList.add('drag-over');
}
function handleDragLeave(e) {
    const body = e.target.closest('.kanban-column')?.querySelector('.kanban-column-body');
    if (body) body.classList.remove('drag-over');
}
function handleDrop(e) {
    e.preventDefault();
    const col = e.target.closest('.kanban-column');
    if (!col) return;
    col.querySelector('.kanban-column-body').classList.remove('drag-over');

    const taskId = e.dataTransfer.getData('text/plain');
    const newStatus = col.dataset.status;
    const card = document.querySelector(`.task-card[data-id="${taskId}"]`);
    if (!card) return;

    // Chờ duyệt → Hoàn thành: chỉ admin/trưởng PB
    if (newStatus === 'hoan_thanh' && card.closest('.kanban-column').dataset.status === 'cho_duyet') {
        if (!isAdmin && !isTruongPB && !isNhanSu) {
            alert('Chỉ quản lý được duyệt task!');
            return;
        }
    }

    // Move card to new column
    const emptyMsg = col.querySelector('.kanban-empty');
    if (emptyMsg) emptyMsg.remove();
    col.querySelector('.kanban-column-body').appendChild(card);

    // Remove empty from old column if old column has the card
    updateEmptyState();
    updateColumnCounts();

    // API call
    fetch(`/tasks/${taskId}/status`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ trang_thai: newStatus })
    });
}

function updateEmptyState() {
    document.querySelectorAll('.kanban-column').forEach(col => {
        const body = col.querySelector('.kanban-column-body');
        const cards = body.querySelectorAll('.task-card');
        const empty = body.querySelector('.kanban-empty');
        if (cards.length === 0 && !empty) {
            body.innerHTML = '<div class="kanban-empty"><svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg><div>Chưa có task nào</div></div>';
        } else if (cards.length > 0 && empty) {
            empty.remove();
        }
    });
}

function updateColumnCounts() {
    document.querySelectorAll('.kanban-column').forEach(col => {
        const count = col.querySelectorAll('.task-card').length;
        col.querySelector('.col-count').textContent = count;
    });
}

// ============================================
// CREATE MODAL
// ============================================
function openCreateModal(parentId = null) {
    document.getElementById('taskId').value = '';
    document.getElementById('taskParentId').value = parentId || '';
    document.getElementById('taskForm').reset();
    document.getElementById('createModalTitle').textContent = parentId ? 'Thêm Sub-task' : 'Tạo Công Việc Mới';
    document.getElementById('btnSubmitTask').innerHTML = '<i class="fas fa-plus"></i> ' + (parentId ? 'Thêm Sub-task' : 'Tạo Công Việc');
    document.getElementById('createModal').classList.add('active');
}

function openEditModal(task) {
    document.getElementById('taskId').value = task.id;
    document.getElementById('taskParentId').value = task.parent_id || '';
    document.getElementById('taskTitle').value = task.tieu_de;
    document.getElementById('taskDesc').value = task.mo_ta || '';
    document.getElementById('taskType').value = task.loai;
    document.getElementById('taskPriority').value = task.do_uu_tien;
    document.getElementById('taskStartDate').value = task.ngay_bat_dau ? dateToDisplay(task.ngay_bat_dau) : '';
    document.getElementById('taskEndDate').value = task.ngay_ket_thuc ? dateToDisplay(task.ngay_ket_thuc) : '';

    // Set assigned users in Select2
    const userIds = (task.task_users || []).map(tu => String(tu.user_id));
    $('#assignUserSelect').val(userIds).trigger('change');

    document.getElementById('createModalTitle').textContent = 'Sửa Công Việc';
    document.getElementById('btnSubmitTask').innerHTML = '<i class="fas fa-save"></i> Lưu Thay Đổi';
    document.getElementById('createModal').classList.add('active');
}

function closeCreateModal() {
    document.getElementById('createModal').classList.remove('active');
}

function submitTask(e) {
    e.preventDefault();
    const taskId = document.getElementById('taskId').value;
    const isEdit = !!taskId;

    const assignedUsers = $('#assignUserSelect').val() || [];

    const labelIds = [];
    document.querySelectorAll('input[name="label_ids[]"]:checked').forEach(cb => labelIds.push(cb.value));

    const data = {
        tieu_de: document.getElementById('taskTitle').value,
        mo_ta: document.getElementById('taskDesc').value,
        loai: document.getElementById('taskType').value,
        do_uu_tien: document.getElementById('taskPriority').value,
        ngay_bat_dau: dateToServer(document.getElementById('taskStartDate').value),
        ngay_ket_thuc: dateToServer(document.getElementById('taskEndDate').value),
        parent_id: document.getElementById('taskParentId').value || null,
        assigned_users: assignedUsers,
        label_ids: labelIds,
    };

    const url = isEdit ? `/tasks/${taskId}` : '/tasks';
    const method = isEdit ? 'PUT' : 'POST';

    fetch(url, {
        method,
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            closeCreateModal();
            location.reload();
        } else {
            alert(res.message || 'Có lỗi xảy ra');
        }
    })
    .catch(() => alert('Lỗi kết nối'));
}

// ============================================
// DETAIL MODAL
// ============================================
function openDetailModal(id) {
    fetch(`/tasks/${id}/detail`)
        .then(r => r.json())
        .then(task => renderDetail(task))
        .catch(() => alert('Không thể tải thông tin'));
}

function closeDetailModal() {
    document.getElementById('detailModal').classList.remove('active');
}

function renderDetail(task) {
    const priorityMap = {khan_cap:'🔴 Khẩn cấp',cao:'🟠 Cao',trung_binh:'🔵 Trung bình',thap:'⚪ Thấp'};
    const statusMap = {chua_bat_dau:'Chưa bắt đầu',dang_lam:'Đang làm',cho_duyet:'Chờ duyệt',hoan_thanh:'Hoàn thành',huy:'Hủy'};

    let labelsHtml = task.labels?.map(l => `<span class="task-label-badge" style="background:${l.mau_sac}">${l.ten}</span>`).join(' ') || '';

    let assigneesHtml = '';
    if (task.task_users?.length) {
        assigneesHtml = task.task_users.map(tu => {
            const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(tu.user?.name || '?')}&size=32&background=6d28d9&color=fff&bold=true`;
            const roleLabel = tu.vai_tro === 'phu_trach' ? '⭐ Phụ trách' : 'Tham gia';
            return `<div class="task-assignee-row">
                <img src="${avatar}">
                <div class="task-assignee-info">
                    <div class="name">${tu.user?.name || ''}</div>
                    <div class="role">${roleLabel}</div>
                </div>
                <div class="task-assignee-progress">
                    <input type="range" min="0" max="100" value="${tu.tien_do}" onchange="updateProgress(${task.id}, ${tu.user_id}, this.value, this)">
                    <span>${tu.tien_do}%</span>
                </div>
            </div>`;
        }).join('');
    }

    let subtasksHtml = '';
    if (task.subtasks?.length) {
        subtasksHtml = `<div class="task-detail-section">
            <h3>📎 Sub-tasks (${task.subtasks.filter(s => s.trang_thai === 'hoan_thanh').length}/${task.subtasks.length})</h3>
            <ul class="subtask-list">
                ${task.subtasks.map(s => `<li class="subtask-item ${s.trang_thai === 'hoan_thanh' ? 'done' : ''}">
                    <input type="checkbox" ${s.trang_thai === 'hoan_thanh' ? 'checked' : ''} onchange="toggleSubtask(${s.id}, this.checked)">
                    <span>${s.tieu_de}</span>
                </li>`).join('')}
            </ul>
            <button style="margin-top:8px;padding:6px 12px;border:none;border-radius:8px;background:rgba(109,40,217,0.15);color:#a78bfa;cursor:pointer;font-size:0.8rem;" onclick="closeDetailModal();openCreateModal(${task.id})">
                + Thêm Sub-task
            </button>
        </div>`;
    } else {
        subtasksHtml = `<div class="task-detail-section">
            <h3>📎 Sub-tasks</h3>
            <div style="color:#475569;font-size:0.85rem;">Chưa có sub-task</div>
            <button style="margin-top:8px;padding:6px 12px;border:none;border-radius:8px;background:rgba(109,40,217,0.15);color:#a78bfa;cursor:pointer;font-size:0.8rem;" onclick="closeDetailModal();openCreateModal(${task.id})">
                + Thêm Sub-task
            </button>
        </div>`;
    }

    let commentsHtml = '';
    if (task.comments?.length) {
        commentsHtml = task.comments.map(c => {
            const avatar = `https://ui-avatars.com/api/?name=${encodeURIComponent(c.user?.name || '?')}&size=32&background=6d28d9&color=fff&bold=true`;
            const time = new Date(c.created_at).toLocaleString('vi-VN');
            let deleteBtn = '';
            if (c.user_id == {{ Auth::id() }} || isAdmin) {
                deleteBtn = `<button class="comment-delete" onclick="deleteComment(${c.id})">🗑</button>`;
            }
            return `<div class="comment-item">
                <img src="${avatar}">
                <div class="comment-body">
                    <div class="comment-header">
                        <span class="comment-author">${c.user?.name || ''}</span>
                        <span class="comment-time">${time}</span>
                        ${deleteBtn}
                    </div>
                    <div class="comment-text">${c.noi_dung}</div>
                </div>
            </div>`;
        }).join('');
    }

    const canEdit = isAdmin || task.created_by == {{ Auth::id() }} || isTruongPB || isNhanSu;
    const canApprove = (isAdmin || isTruongPB || isNhanSu) && task.trang_thai === 'cho_duyet';

    let actionsHtml = '<div class="task-detail-actions">';
    if (canEdit) actionsHtml += `<button class="btn-edit-task" onclick="closeDetailModal();fetchAndEdit(${task.id})"><i class="fas fa-edit"></i> Sửa</button>`;
    if (canApprove) actionsHtml += `<button class="btn-approve-task" onclick="approveTask(${task.id})"><i class="fas fa-check"></i> Duyệt</button>`;
    if (canEdit) actionsHtml += `<button class="btn-delete-task" onclick="deleteTask(${task.id})"><i class="fas fa-trash"></i> Xóa</button>`;
    actionsHtml += '</div>';

    const deadline = task.ngay_ket_thuc ? new Date(task.ngay_ket_thuc).toLocaleDateString('vi-VN') : 'Không';
    const startDate = task.ngay_bat_dau ? new Date(task.ngay_bat_dau).toLocaleDateString('vi-VN') : 'Không';

    document.getElementById('detailTitle').textContent = task.tieu_de;
    document.getElementById('detailBody').innerHTML = `
        <div class="task-detail-section">
            <div style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:12px;">
                ${labelsHtml}
                <span class="priority-badge ${task.do_uu_tien}">${priorityMap[task.do_uu_tien]}</span>
                <span class="status-badge ${task.trang_thai}">${statusMap[task.trang_thai]}</span>
            </div>
            ${task.mo_ta ? `<p style="color:#cbd5e1;font-size:0.9rem;line-height:1.6;margin:0;">${task.mo_ta}</p>` : ''}
            <div style="display:flex;gap:20px;margin-top:12px;font-size:0.82rem;color:#64748b;">
                <span>📅 Bắt đầu: ${startDate}</span>
                <span>🏁 Deadline: ${deadline}</span>
                <span>👤 Tạo bởi: ${task.creator?.name || ''}</span>
            </div>
        </div>

        <div class="task-detail-section">
            <h3>👥 Phân Công</h3>
            <div class="task-assignee-list">${assigneesHtml || '<div style="color:#475569;font-size:0.85rem;">Chưa phân công</div>'}</div>
        </div>

        ${subtasksHtml}

        <div class="task-detail-section">
            <h3>💬 Bình Luận (${task.comments?.length || 0})</h3>
            <div class="comment-list">${commentsHtml || '<div style="color:#475569;font-size:0.85rem;">Chưa có bình luận</div>'}</div>
            <div class="comment-form">
                <input type="text" id="commentInput" placeholder="Viết bình luận...">
                <button onclick="postComment(${task.id})">Gửi</button>
            </div>
        </div>

        ${actionsHtml}
    `;

    document.getElementById('detailModal').classList.add('active');
}

// ============================================
// API HELPERS
// ============================================
function updateProgress(taskId, userId, value, el) {
    el.nextElementSibling.textContent = value + '%';
    fetch(`/tasks/${taskId}/progress`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ tien_do: value, user_id: userId })
    });
}

function toggleSubtask(id, done) {
    fetch(`/tasks/${id}/status`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ trang_thai: done ? 'hoan_thanh' : 'chua_bat_dau' })
    });
}

function postComment(taskId) {
    const input = document.getElementById('commentInput');
    if (!input.value.trim()) return;
    fetch(`/tasks/${taskId}/comment`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ noi_dung: input.value })
    })
    .then(r => r.json())
    .then(res => {
        if (res.success) {
            input.value = '';
            openDetailModal(taskId); // Refresh
        }
    });
}

function deleteComment(id) {
    if (!confirm('Xóa bình luận này?')) return;
    fetch(`/tasks/comment/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => location.reload());
}

function deleteTask(id) {
    if (!confirm('Xóa công việc này? Tất cả sub-task cũng sẽ bị xóa.')) return;
    fetch(`/tasks/${id}`, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': CSRF }
    }).then(() => { closeDetailModal(); location.reload(); });
}

function approveTask(id) {
    fetch(`/tasks/${id}/status`, {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF },
        body: JSON.stringify({ trang_thai: 'hoan_thanh' })
    }).then(() => { closeDetailModal(); location.reload(); });
}

function fetchAndEdit(id) {
    fetch(`/tasks/${id}/detail`)
        .then(r => r.json())
        .then(task => openEditModal(task));
}

// Close modals on overlay click
document.getElementById('createModal').addEventListener('click', function(e) {
    if (e.target === this) closeCreateModal();
});
document.getElementById('detailModal').addEventListener('click', function(e) {
    if (e.target === this) closeDetailModal();
});
</script>
@endpush
