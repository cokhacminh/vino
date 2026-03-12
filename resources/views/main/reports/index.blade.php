@extends('main.layouts.app')

@section('title', 'Báo Cáo Ngày')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
.rp-datepicker { cursor: pointer; }
.flatpickr-calendar { z-index: 9999 !important; }
.date-input-wrap { position: relative; display: inline-block; }
.date-input-wrap i { position: absolute; right: 12px; top: 50%; transform: translateY(-50%); color: #0d9488; pointer-events: none; font-size: 0.85rem; }
.date-input-wrap .rp-input { padding-right: 34px; }

/* ====== PAGE CONTAINER ====== */
.report-page { padding: 20px; ; border-radius: 16px; min-height: 80vh; }
.report-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 24px; flex-wrap: wrap; gap: 12px; padding: 16px 20px; background: linear-gradient(135deg, #0d9488, #0891b2, #0ea5e9); border-radius: 14px; box-shadow: 0 4px 20px rgba(13,148,136,0.2); }
.report-title { font-size: 1.5rem; font-weight: 800; color: #fff; display: flex; align-items: center; gap: 10px; }
.report-title i { font-size: 1.3rem; color: rgba(255,255,255,0.85); }

/* ====== TABS ====== */
.report-tabs { display: flex; gap: 4px; background: rgba(255,255,255,0.5); border-radius: 10px; padding: 4px; flex-wrap: wrap; }
.report-tab { padding: 8px 18px; border-radius: 8px; border: none; background: transparent; color: rgba(255,255,255,0.75); font-weight: 600; font-size: 0.82rem; cursor: pointer; transition: all 0.3s; white-space: nowrap; }
.report-tab:hover { color: #fff; background: rgba(255,255,255,0.2); }
.report-tab.active { background: #fff; color: #0d9488; box-shadow: 0 2px 8px rgba(0,0,0,0.1); font-weight: 700; }
.tab-content { display: none; }
.tab-content.active { display: block; }

/* ====== CARDS ====== */
.rp-card { background: #fff; border: 1px solid #e2e8f0; border-left: 4px solid #0d9488; border-radius: 14px; padding: 24px; margin-bottom: 16px; box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
.rp-card h3 { color: #1e293b; font-size: 1rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.rp-card h3 i { color: #0d9488; font-size: 0.9rem; }

/* ====== FORM ====== */
.rp-toolbar { display: flex; gap: 12px; align-items: center; margin-bottom: 16px; flex-wrap: wrap; }
.rp-select, .rp-input { background: #fff; border: 1.5px solid #e2e8f0; border-radius: 10px; color: #1e293b; padding: 10px 14px; font-size: 0.85rem; outline: none; transition: border-color 0.2s, box-shadow 0.2s; }
.rp-select:focus, .rp-input:focus { border-color: #0d9488; box-shadow: 0 0 0 3px rgba(13,148,136,0.1); }
.rp-select { min-width: 200px; }
.rp-input-date { width: 160px; }

.rp-form-group { margin-bottom: 16px; }
.rp-label { display: block; color: #475569; font-size: 0.82rem; font-weight: 600; margin-bottom: 6px; }
.rp-label .required { color: #ef4444; margin-left: 4px; }
.rp-text-input, .rp-textarea, .rp-number-input { width: 98%; background: #f8fafc; border: 1.5px solid #e2e8f0; border-radius: 10px; color: #1e293b; padding: 10px 14px; font-size: 0.85rem; outline: none; transition: border-color 0.2s, box-shadow 0.2s; font-family: inherit; }
.rp-text-input:focus, .rp-textarea:focus, .rp-number-input:focus { border-color: #0d9488; box-shadow: 0 0 0 3px rgba(13,148,136,0.1); }
.rp-textarea { resize: vertical; min-height: 80px; }
.rp-number-input { width: 90%; }
.rp-checkbox-wrap { display: flex; align-items: center; gap: 8px; color: #475569; font-size: 0.85rem; padding-top: 28px; }
.rp-checkbox-wrap input[type="checkbox"] { width: 18px; height: 18px; accent-color: #0d9488; }
.rp-select-field { width: 100%; background: #fff; border: 1.5px solid #e2e8f0; border-radius: 10px; color: #1e293b; padding: 10px 14px; font-size: 0.85rem; outline: none; }
.rp-select-field:focus { border-color: #0d9488; box-shadow: 0 0 0 3px rgba(13,148,136,0.1); }

/* ====== REPORT FORM GRID ====== */
.report-form-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(260px, 1fr)); gap: 12px 20px; }
.report-form-grid .rp-form-group { margin-bottom: 0; }
.report-form-grid .rp-form-group.full-width { grid-column: 1 / -1; }

/* ====== BUTTONS ====== */
.rp-btn { padding: 10px 22px; border-radius: 10px; border: none; font-weight: 700; font-size: 0.82rem; cursor: pointer; transition: all 0.3s; display: inline-flex; align-items: center; gap: 6px; }

/* ====== SUBMIT TEMPLATE CARDS ====== */
.submit-tpl-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 12px; }
.submit-tpl-card { background: #fff; border: 1.5px solid #e2e8f0; border-radius: 12px; padding: 16px; cursor: pointer; transition: all 0.25s; display: flex; align-items: center; gap: 14px; }
.submit-tpl-card:hover { border-color: #0d9488; transform: translateY(-1px); box-shadow: 0 4px 16px rgba(13,148,136,0.12); }
.submit-tpl-card.status-submitted { border-left: 4px solid #22c55e; }
.submit-tpl-card.status-draft { border-left: 4px solid #f59e0b; }
.submit-tpl-card.status-none { border-left: 4px solid #e2e8f0; }
.submit-tpl-icon { width: 42px; height: 42px; border-radius: 10px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }
.submit-tpl-icon.ic-submitted { background: #dcfce7; color: #16a34a; }
.submit-tpl-icon.ic-draft { background: #fef3c7; color: #d97706; }
.submit-tpl-icon.ic-none { background: #f1f5f9; color: #94a3b8; }
.submit-tpl-info { flex: 1; min-width: 0; }
.submit-tpl-name { font-weight: 700; color: #1e293b; font-size: 0.88rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
.submit-tpl-status { font-size: 0.72rem; margin-top: 3px; font-weight: 600; }
.submit-tpl-status.st-submitted { color: #16a34a; }
.submit-tpl-status.st-draft { color: #d97706; }
.submit-tpl-status.st-none { color: #94a3b8; }
.submit-back-btn { background: none; border: 1.5px solid #e2e8f0; border-radius: 8px; padding: 6px 14px; font-size: 0.78rem; font-weight: 600; color: #64748b; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; margin-bottom: 14px; transition: all 0.2s; }
.submit-back-btn:hover { border-color: #0d9488; color: #0d9488; }
.rp-btn-primary { background: linear-gradient(135deg, #0d9488, #0ea5e9); color: white; }
.rp-btn-primary:hover { box-shadow: 0 4px 16px rgba(13,148,136,0.35); transform: translateY(-1px); }
.rp-btn-secondary { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
.rp-btn-secondary:hover { background: #e2e8f0; color: #475569; }
.rp-btn-success { background: linear-gradient(135deg, #10b981, #059669); color: white; }
.rp-btn-success:hover { box-shadow: 0 4px 12px rgba(16,185,129,0.35); }
.rp-btn-danger { background: #fef2f2; color: #ef4444; border: 1px solid #fecaca; }
.rp-btn-danger:hover { background: #fee2e2; }
.rp-btn-sm { padding: 6px 14px; font-size: 0.75rem; }

/* ====== REPORT VIEW (History) ====== */
.report-card { background: #fff; border: 1px solid #e2e8f0; border-radius: 14px; padding: 16px; margin-bottom: 10px; transition: all 0.2s; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
.report-card:hover { border-color: rgba(13,148,136,0.2); box-shadow: 0 4px 16px rgba(0,0,0,0.06); }
.report-card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; flex-wrap: wrap; gap: 8px; }
.report-card-user { color: #0d9488; font-weight: 700; font-size: 0.9rem; }
.report-card-date { color: #94a3b8; font-size: 0.75rem; }
.report-card-status { padding: 3px 10px; border-radius: 8px; font-size: 0.7rem; font-weight: 700; text-transform: uppercase; }
.status-submitted { background: #dbeafe; color: #2563eb; }
.status-reviewed { background: #dcfce7; color: #16a34a; }
.status-draft { background: #fef3c7; color: #d97706; }
.report-field { display: flex; gap: 8px; padding: 7px 0; border-bottom: 1px solid #f8fafc; }
.report-field:last-child { border-bottom: none; }
.report-field-label { color: #94a3b8; font-size: 0.78rem; min-width: 180px; flex-shrink: 0; }
.report-field-value { color: #1e293b; font-size: 0.82rem; font-weight: 600; }
.reviewer-note { margin-top: 12px; padding: 10px 14px; background: #ecfdf5; border-radius: 10px; border-left: 3px solid #10b981; }
.reviewer-note-label { color: #059669; font-size: 0.72rem; font-weight: 700; margin-bottom: 4px; }
.reviewer-note-text { color: #475569; font-size: 0.82rem; }

/* ====== NOT SUBMITTED LIST ====== */
.not-submitted { margin-top: 16px; padding: 14px; background: #fef2f2; border: 1px solid #fecaca; border-radius: 12px; }
.not-submitted h4 { color: #dc2626; font-size: 0.82rem; margin-bottom: 8px; }
.not-submitted-list { display: flex; flex-wrap: wrap; gap: 6px; }
.not-submitted-item { background: #fee2e2; color: #b91c1c; padding: 4px 10px; border-radius: 6px; font-size: 0.75rem; font-weight: 600; }

/* ====== TEMPLATE BUILDER ====== */
.tpl-list { display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 12px; }
.tpl-card { background: linear-gradient(135deg, #fff, #f0fdfa); border: 1px solid #ccfbf1; border-radius: 14px; padding: 16px; cursor: pointer; transition: all 0.3s; box-shadow: 0 2px 8px rgba(0,0,0,0.04); }
.tpl-card:hover { border-color: #14b8a6; transform: translateY(-2px); box-shadow: 0 6px 20px rgba(13,148,136,0.15); }
.tpl-name { color: #1e293b; font-weight: 700; font-size: 0.9rem; margin-bottom: 6px; }
.tpl-meta { color: #64748b; font-size: 0.72rem; display: flex; gap: 12px; margin-top: 8px; }
.tpl-badge { padding: 2px 8px; border-radius: 6px; font-size: 0.65rem; font-weight: 700; }
.tpl-badge-active { background: #dcfce7; color: #16a34a; }
.tpl-badge-inactive { background: #fee2e2; color: #dc2626; }
.tpl-field-count { color: #0d9488; font-weight: 600; }
.tpl-actions { display: flex; gap: 6px; margin-top: 10px; }
.tpl-btn-toggle, .tpl-btn-delete { padding: 4px 12px; border-radius: 6px; border: none; font-size: 0.72rem; font-weight: 700; cursor: pointer; transition: all 0.2s; }
.tpl-btn-toggle { background: #dbeafe; color: #2563eb; }
.tpl-btn-toggle:hover { background: #bfdbfe; }
.tpl-btn-delete { background: #fee2e2; color: #dc2626; }
.tpl-btn-delete:hover { background: #fecaca; }

/* ====== MODAL ====== */
.rp-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.4); backdrop-filter: blur(6px); z-index: 9000; display: none; align-items: center; justify-content: center; }
.rp-modal-overlay.show { display: flex; }
.rp-modal { background: #fff; border: 1px solid #e2e8f0; border-radius: 18px; padding: 28px; width: 95%; max-width: 600px; max-height: 85vh; overflow-y: auto; animation: rpModalIn 0.25s ease; box-shadow: 0 20px 60px rgba(0,0,0,0.15); }
@keyframes rpModalIn { from { opacity: 0; transform: scale(0.95) translateY(10px); } to { opacity: 1; transform: scale(1) translateY(0); } }
.rp-modal h3 { color: #1e293b; font-size: 1rem; margin-bottom: 16px; display: flex; align-items: center; gap: 8px; }
.rp-modal-actions { display: flex; gap: 8px; justify-content: flex-end; margin-top: 20px; }

/* ====== BUILDER FIELDS ====== */
.builder-field { display: flex; gap: 8px; align-items: center; padding: 10px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; margin-bottom: 8px; transition: opacity 0.2s, border-color 0.2s, transform 0.15s; }
.builder-field .drag-handle { color: #94a3b8; cursor: grab; font-size: 0.8rem; padding: 4px 2px; user-select: none; }
.builder-field .drag-handle:active { cursor: grabbing; }
.builder-field.dragging { opacity: 0.4; transform: scale(0.97); }
.builder-field.drag-over { border-color: #0d9488; border-style: dashed; background: rgba(13,148,136,0.04); }
.builder-field-inputs { flex: 1; display: grid; grid-template-columns: 1fr 120px 60px; gap: 6px; align-items: center; }
.builder-input { background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; color: #1e293b; padding: 6px 10px; font-size: 0.78rem; outline: none; }
.builder-input:focus { border-color: #0d9488; }
.builder-remove { background: none; border: none; color: #ef4444; cursor: pointer; font-size: 0.8rem; padding: 4px; }

/* ====== SCHEDULE CONFIG ====== */
.schedule-group { margin-top: 10px; padding: 12px; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; }
.schedule-group label { color: #64748b; font-size: 0.75rem; font-weight: 600; margin-bottom: 6px; display: block; }
.schedule-dates-list { display: flex; flex-wrap: wrap; gap: 6px; margin-top: 6px; }
.schedule-date-tag { background: #dbeafe; color: #2563eb; padding: 3px 10px; border-radius: 6px; font-size: 0.72rem; font-weight: 600; display: inline-flex; align-items: center; gap: 4px; }
.schedule-date-tag .remove-tag { cursor: pointer; color: #ef4444; font-size: 0.7rem; }
.schedule-badge { font-size: 0.62rem; padding: 2px 6px; border-radius: 4px; font-weight: 700; }
.schedule-badge-daily { background: #dcfce7; color: #16a34a; }
.schedule-badge-custom { background: #dbeafe; color: #2563eb; }
.schedule-badge-monthly { background: #fef3c7; color: #d97706; }
.schedule-info { margin-bottom: 14px; padding: 10px 14px; background: linear-gradient(135deg, #ecfdf5, #f0f9ff); border-radius: 10px; border-left: 3px solid #0d9488; color: #0f766e; font-size: 0.78rem; }

/* ====== STATS ====== */
.stats-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 10px; margin-bottom: 20px; }
.stat-card { background: linear-gradient(135deg, #f0fdfa, #ecfeff); border: 1px solid #99f6e4; border-radius: 12px; padding: 16px; text-align: center; box-shadow: 0 2px 8px rgba(13,148,136,0.08); }
.stat-value { font-size: 1.5rem; font-weight: 800; color: #0d9488; }
.stat-label { font-size: 0.72rem; color: #64748b; margin-top: 4px; font-weight: 500; }
.stats-table { width: 100%; border-collapse: collapse; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.06); }
.stats-table th { background: linear-gradient(135deg, #0d9488, #0ea5e9); color: white; padding: 10px 14px; font-size: 0.75rem; text-align: left; font-weight: 700; }
.stats-table td { padding: 10px 14px; border-bottom: 1px solid #f1f5f9; color: #1e293b; font-size: 0.82rem; }
.stats-table tr:hover { background: rgba(13,148,136,0.04); }
.completion-bar { height: 6px; border-radius: 3px; background: #f1f5f9; overflow: hidden; min-width: 80px; }
.completion-fill { height: 100%; border-radius: 3px; transition: width 0.4s ease; }

/* ====== LOADING ====== */
.rp-loading { text-align: center; padding: 40px; color: #94a3b8; }
.rp-loading i { font-size: 1.5rem; animation: spin 1s linear infinite; margin-bottom: 10px; display: block; color: #0d9488; }
@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } }

/* ====== EMPTY STATE ====== */
.rp-empty { text-align: center; padding: 40px 20px; color: #94a3b8; }
.rp-empty i { font-size: 2.5rem; margin-bottom: 12px; display: block; color: #cbd5e1; }
.rp-empty p { font-size: 0.85rem; }

/* ====== RESPONSIVE ====== */
@media (max-width: 768px) {
    .report-page { padding: 10px; }
    .rp-toolbar { flex-direction: column; align-items: stretch; }
    .rp-select, .rp-input-date { width: 100%; }
    .builder-field-inputs { grid-template-columns: 1fr; }
    .report-field { flex-direction: column; gap: 2px; }
    .report-field-label { min-width: auto; }
    .stats-grid { grid-template-columns: repeat(2, 1fr); }
    .report-form-grid { grid-template-columns: 1fr; }
}

/* ====== SELECT2 OVERRIDES ====== */
.select2-container--default .select2-selection--multiple { border: 1.5px solid #e2e8f0; border-radius: 10px; padding: 4px 8px; min-height: 40px; background: #f8fafc; }
.select2-container--default .select2-selection--multiple:focus-within { border-color: #0d9488; box-shadow: 0 0 0 3px rgba(13,148,136,0.1); }
.select2-container--default .select2-selection--multiple .select2-selection__choice { background: linear-gradient(135deg, #0d9488, #0ea5e9); color: #fff; border: none; border-radius: 6px; padding: 3px 8px; font-size: 0.78rem; font-weight: 600; }
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove { color: rgba(255,255,255,0.7); margin-right: 4px; }
.select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover { color: #fff; }
.select2-dropdown { border: 1.5px solid #e2e8f0; border-radius: 10px; box-shadow: 0 6px 20px rgba(0,0,0,0.1); z-index: 10000; }
.select2-results__option--highlighted { background: #0d9488 !important; }
</style>
@endpush


@section('content')
<div class="report-page">
    <div class="report-header">
        <div class="report-title">
            <i class="fas fa-chart-line"></i> Báo Cáo Ngày
        </div>
        <div class="report-tabs" id="reportTabs">
            <button class="report-tab active" data-tab="submit" onclick="switchTab('submit')"><i class="fas fa-edit"></i> Nộp Báo Cáo</button>
            <button class="report-tab" data-tab="history" onclick="switchTab('history')"><i class="fas fa-history"></i> Lịch Sử</button>
            <button class="report-tab admin-only" data-tab="templates" onclick="switchTab('templates')" style="display:none;"><i class="fas fa-cogs"></i> Quản Lý Mẫu</button>
            <button class="report-tab admin-only" data-tab="stats" onclick="switchTab('stats')" style="display:none;"><i class="fas fa-chart-bar"></i> Thống Kê</button>
        </div>
    </div>

    <!-- TAB 1: NỘP BÁO CÁO -->
    <div class="tab-content active" id="tab-submit">
        <div class="rp-card">
            <div class="rp-toolbar">
                <select class="rp-select" id="submitTemplate" onchange="loadSubmitForm()" style="display:none;">
                    <option value="">-- Chọn mẫu báo cáo --</option>
                </select>
                <div class="date-input-wrap"><input type="text" class="rp-input rp-input-date rp-datepicker" id="submitDate" placeholder="dd/mm/yyyy" readonly><i class="fas fa-calendar-alt"></i></div>
            </div>
            <div id="submitTemplateList"></div>
            <div id="submitFormArea" style="display:none;"></div>
        </div>
    </div>

    <!-- TAB 2: LỊCH SỬ -->
    <div class="tab-content" id="tab-history">
        <div class="rp-card">
            <div class="rp-toolbar">
                <select class="rp-select" id="historyTemplate" onchange="loadHistory()">
                    <option value="">-- Tất cả mẫu --</option>
                </select>
                <div class="date-input-wrap"><input type="text" class="rp-input rp-input-date rp-datepicker" id="historyDate" placeholder="dd/mm/yyyy" readonly><i class="fas fa-calendar-alt"></i></div>
                <select class="rp-select admin-only" id="historyUser" onchange="loadHistory()" style="display:none;">
                    <option value="">-- Tất cả nhân viên --</option>
                </select>
            </div>
            <div id="historyArea">
                <div class="rp-loading"><i class="fas fa-spinner"></i> Đang tải...</div>
            </div>
        </div>
    </div>

    <!-- TAB 3: QUẢN LÝ MẪU -->
    <div class="tab-content" id="tab-templates">
        <div class="rp-card">
            <h3><i class="fas fa-puzzle-piece"></i> Danh Sách Mẫu Báo Cáo <button class="rp-btn rp-btn-primary rp-btn-sm" onclick="openTemplateModal()" style="margin-left:auto;"><i class="fas fa-plus"></i> Tạo Mẫu</button></h3>
            <div id="templateListArea">
                <div class="rp-loading"><i class="fas fa-spinner"></i> Đang tải...</div>
            </div>
        </div>
    </div>

    <!-- TAB 4: THỐNG KÊ -->
    <div class="tab-content" id="tab-stats">
        <div class="rp-card">
            <h3><i class="fas fa-chart-bar"></i> Thống Kê Nộp Báo Cáo</h3>
            <div class="rp-toolbar">
                <select class="rp-select" id="statsTemplate" onchange="loadStats()">
                    <option value="">-- Tất cả mẫu --</option>
                </select>
                <div class="date-input-wrap"><input type="text" class="rp-input rp-input-date rp-datepicker" id="statsFrom" placeholder="dd/mm/yyyy" readonly><i class="fas fa-calendar-alt"></i></div>
                <span style="color:#64748b;">→</span>
                <div class="date-input-wrap"><input type="text" class="rp-input rp-input-date rp-datepicker" id="statsTo" placeholder="dd/mm/yyyy" readonly><i class="fas fa-calendar-alt"></i></div>
            </div>
            <div id="statsArea">
                <div class="rp-loading"><i class="fas fa-spinner"></i> Đang tải...</div>
            </div>
        </div>
    </div>
</div>

<!-- TEMPLATE MODAL -->
<div class="rp-modal-overlay" id="templateModal">
    <div class="rp-modal">
        <h3><i class="fas fa-puzzle-piece" style="color:#a78bfa;"></i> <span id="tplModalTitle">Tạo Mẫu Báo Cáo</span></h3>

        <div class="rp-form-group">
            <label class="rp-label">Tên mẫu <span class="required">*</span></label>
            <input type="text" class="rp-text-input" id="tplName" placeholder="VD: Báo Cáo Ngày - Kinh Doanh">
        </div>

        <div class="rp-form-group">
            <label class="rp-label">Phòng ban</label>
            <select class="rp-select" id="tplPhongBan" style="width:100%;" onchange="loadUsersForDepartment()">
                <option value="">Tất cả phòng ban</option>
            </select>
        </div>

        <div class="rp-form-group">
            <label class="rp-label">Nhân viên áp dụng</label>
            <select class="rp-select" id="tplUsers" multiple="multiple" style="width:100%;">
            </select>
            <small style="color:#94a3b8; font-size:0.72rem;">Chọn phòng ban để tự động load nhân viên</small>
        </div>

        <div class="rp-form-group">
            <label class="rp-label">Lịch báo cáo <span class="required">*</span></label>
            <select class="rp-select" id="tplScheduleType" style="width:100%;" onchange="toggleScheduleConfig()">
                <option value="daily">📅 Hằng ngày</option>
                <option value="custom">📌 Ngày tùy chọn</option>
                <option value="monthly">🔁 Ngày cố định trong tháng</option>
            </select>
            <div id="scheduleConfigArea"></div>
        </div>

        <div class="rp-form-group">
            <label class="rp-label">Các trường báo cáo</label>
            <div id="tplFields"></div>
            <button class="rp-btn rp-btn-secondary rp-btn-sm" onclick="addTemplateField()" style="margin-top:8px;"><i class="fas fa-plus"></i> Thêm trường</button>
        </div>

        <input type="hidden" id="tplEditId" value="">
        <div class="rp-modal-actions">
            <button class="rp-btn rp-btn-secondary" onclick="closeTemplateModal()">Hủy</button>
            <button class="rp-btn rp-btn-primary" onclick="saveTemplate()"><i class="fas fa-save"></i> Lưu</button>
        </div>
    </div>
</div>

<!-- REVIEW MODAL -->
<div class="rp-modal-overlay" id="reviewModal">
    <div class="rp-modal" style="max-width:400px;">
        <h3><i class="fas fa-comment-dots" style="color:#22c55e;"></i> Nhận Xét Báo Cáo</h3>
        <div class="rp-form-group">
            <textarea class="rp-textarea" id="reviewNote" placeholder="Nhận xét..."></textarea>
        </div>
        <input type="hidden" id="reviewReportId" value="">
        <div class="rp-modal-actions">
            <button class="rp-btn rp-btn-secondary" onclick="closeReviewModal()">Hủy</button>
            <button class="rp-btn rp-btn-success" onclick="submitReview()"><i class="fas fa-check"></i> Xác nhận</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/vn.js"></script>
<script>
const csrfToken = '{{ csrf_token() }}';
let allTemplates = [];
let isAdmin = false;

// Date helpers: dd/mm/yyyy <-> yyyy-mm-dd
function toApiDate(displayDate) {
    if (!displayDate) return '';
    const parts = displayDate.split('/');
    if (parts.length !== 3) return displayDate;
    return parts[2] + '-' + parts[1] + '-' + parts[0];
}
function toDisplayDate(apiDate) {
    if (!apiDate) return '';
    const parts = apiDate.split('-');
    if (parts.length !== 3) return apiDate;
    return parts[2] + '/' + parts[1] + '/' + parts[0];
}
function todayDisplay() {
    const d = new Date();
    return String(d.getDate()).padStart(2,'0') + '/' + String(d.getMonth()+1).padStart(2,'0') + '/' + d.getFullYear();
}
function monthStartDisplay() {
    const d = new Date();
    return '01/' + String(d.getMonth()+1).padStart(2,'0') + '/' + d.getFullYear();
}

function initFlatpickr(selector, onChange) {
    return flatpickr(selector, {
        dateFormat: 'd/m/Y',
        locale: 'vn',
        clickOpens: true,
        allowInput: false,
        disableMobile: true,
        onChange: onChange || function() {}
    });
}

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM loaded, flatpickr available:', typeof flatpickr);
    
    if (typeof flatpickr === 'undefined') {
        console.error('Flatpickr NOT loaded! Falling back to native date inputs.');
        document.querySelectorAll('.rp-datepicker').forEach(el => {
            el.type = 'date';
            el.removeAttribute('readonly');
        });
        return;
    }

    initFlatpickr('#submitDate', () => {
        if (isAdmin) { loadSubmitForm(); } else { renderSubmitTemplateCards(); }
    });
    initFlatpickr('#historyDate', () => loadHistory());
    initFlatpickr('#statsFrom', () => loadStats());
    initFlatpickr('#statsTo', () => loadStats());
    console.log('Flatpickr initialized on all date inputs');

    document.getElementById('submitDate').value = todayDisplay();
    document.getElementById('historyDate').value = todayDisplay();
    document.getElementById('statsTo').value = todayDisplay();
    document.getElementById('statsFrom').value = monthStartDisplay();
    loadTemplates();
});

async function loadTemplates() {
    try {
        const resp = await fetch("{{ route('reports.templates') }}");
        const data = await resp.json();
        if (!data.success) return;

        allTemplates = data.templates;
        isAdmin = data.is_admin || data.is_truong_phong;

        // Show admin tabs cho Admin + Trưởng Phòng
        if (isAdmin) {
            document.querySelectorAll('.admin-only').forEach(el => el.style.display = '');
        }

        // Populate selects (history, stats)
        const selects = ['historyTemplate', 'statsTemplate'];
        selects.forEach(id => {
            const sel = document.getElementById(id);
            const val = sel.value;
            sel.innerHTML = '<option value="">-- Tất cả mẫu --</option>';
            allTemplates.forEach(t => {
                sel.innerHTML += `<option value="${t.id}">${t.name} (${t.phongban_name})</option>`;
            });
            sel.value = val;
        });

        // Submit tab: admin dùng select, nhân viên dùng danh sách card
        if (isAdmin) {
            const submitSel = document.getElementById('submitTemplate');
            submitSel.style.display = '';
            submitSel.innerHTML = '<option value="">-- Chọn mẫu báo cáo --</option>';
            allTemplates.forEach(t => {
                submitSel.innerHTML += `<option value="${t.id}">${t.name} (${t.phongban_name})</option>`;
            });
            document.getElementById('submitTemplateList').style.display = 'none';
            document.getElementById('submitFormArea').style.display = '';
            document.getElementById('submitFormArea').innerHTML = '<div class="rp-empty"><i class="fas fa-clipboard-list"></i><p>Vui lòng chọn mẫu báo cáo để bắt đầu</p></div>';
        } else {
            document.getElementById('submitTemplate').style.display = 'none';
            document.getElementById('submitTemplateList').style.display = '';
            document.getElementById('submitFormArea').style.display = 'none';
            renderSubmitTemplateCards();
        }

        // Load users for history filter (admin only)
        if (isAdmin) {
            try {
                const usersResp = await fetch("{{ route('reports.allTemplates') }}");
                const usersData = await usersResp.json();
                if (usersData.phongbans) {
                    const tplPB = document.getElementById('tplPhongBan');
                    tplPB.innerHTML = '<option value="">Tất cả phòng ban</option>';
                    usersData.phongbans.forEach(p => {
                        tplPB.innerHTML += `<option value="${p.MaPB}">${p.TenPB}</option>`;
                    });
                }
            } catch(e) {}
        }

        // Auto-select first template if only one
        if (allTemplates.length === 1) {
            document.getElementById('submitTemplate').value = allTemplates[0].id;
            loadSubmitForm();
        }
    } catch(e) {
        console.error(e);
    }
}

// ============================
// TAB SWITCHING
// ============================
function switchTab(tab) {
    document.querySelectorAll('.report-tab').forEach(t => t.classList.toggle('active', t.dataset.tab === tab));
    document.querySelectorAll('.tab-content').forEach(t => t.classList.toggle('active', t.id === 'tab-' + tab));

    if (tab === 'submit' && !isAdmin) renderSubmitTemplateCards();
    if (tab === 'history') loadHistory();
    if (tab === 'templates') loadTemplateList();
    if (tab === 'stats') loadStats();
}

// Render template cards for employees
async function renderSubmitTemplateCards() {
    const area = document.getElementById('submitTemplateList');
    const date = toApiDate(document.getElementById('submitDate').value);

    if (allTemplates.length === 0) {
        area.innerHTML = '<div class="rp-empty"><i class="fas fa-clipboard-list"></i><p>Chưa có mẫu báo cáo nào được gán cho bạn</p></div>';
        return;
    }

    area.innerHTML = '<div class="rp-loading"><i class="fas fa-spinner"></i> Đang tải...</div>';

    // Lấy status cho từng template
    const statuses = {};
    await Promise.all(allTemplates.map(async t => {
        try {
            const resp = await fetch(`{{ route('reports.myReport') }}?template_id=${t.id}&date=${encodeURIComponent(date)}`);
            const data = await resp.json();
            statuses[t.id] = data.report ? data.report.status : null;
        } catch(e) {
            statuses[t.id] = null;
        }
    }));

    let html = '<div class="submit-tpl-list">';
    allTemplates.forEach(t => {
        const status = statuses[t.id];
        const statusClass = status === 'submitted' || status === 'reviewed' ? 'submitted' : (status === 'draft' ? 'draft' : 'none');
        const iconClass = status === 'submitted' || status === 'reviewed' ? 'ic-submitted' : (status === 'draft' ? 'ic-draft' : 'ic-none');
        const icon = status === 'submitted' || status === 'reviewed' ? 'fa-check-circle' : (status === 'draft' ? 'fa-pen' : 'fa-file-alt');
        const statusText = status === 'submitted' ? '✅ Đã nộp' : (status === 'reviewed' ? '🔍 Đã đánh giá' : (status === 'draft' ? '📝 Bản nháp' : '⏳ Chưa nộp'));
        const stClass = status === 'submitted' || status === 'reviewed' ? 'st-submitted' : (status === 'draft' ? 'st-draft' : 'st-none');

        html += `<div class="submit-tpl-card status-${statusClass}" onclick="loadSubmitForm(${t.id})">`;
        html += `<div class="submit-tpl-icon ${iconClass}"><i class="fas ${icon}"></i></div>`;
        html += `<div class="submit-tpl-info">`;
        html += `<div class="submit-tpl-name">${escapeHtml(t.name)}</div>`;
        html += `<div class="submit-tpl-status ${stClass}">${statusText}</div>`;
        html += `</div>`;
        html += `<i class="fas fa-chevron-right" style="color:#cbd5e1;"></i>`;
        html += `</div>`;
    });
    html += '</div>';
    area.innerHTML = html;
}

function backToTemplateList() {
    document.getElementById('submitFormArea').style.display = 'none';
    document.getElementById('submitTemplateList').style.display = '';
    renderSubmitTemplateCards();
}

// ============================
// TAB 1: NỘP BÁO CÁO
// ============================
async function loadSubmitForm(templateIdParam) {
    const templateId = templateIdParam || document.getElementById('submitTemplate').value;
    const date = toApiDate(document.getElementById('submitDate').value);
    const area = document.getElementById('submitFormArea');

    if (!templateId) {
        area.style.display = 'none';
        document.getElementById('submitTemplateList').style.display = '';
        return;
    }

    // Ẩn danh sách card, hiện form area
    document.getElementById('submitTemplateList').style.display = 'none';
    area.style.display = '';
    // Set template ID vào select để submitReport dùng
    document.getElementById('submitTemplate').value = templateId;

    const template = allTemplates.find(t => t.id == templateId);
    if (!template) return;

    area.innerHTML = '<div class="rp-loading"><i class="fas fa-spinner"></i> Đang tải...</div>';

    // Check if already submitted
    let existingValues = {};
    let existingStatus = null;
    try {
        const resp = await fetch(`{{ route('reports.myReport') }}?template_id=${templateId}&date=${encodeURIComponent(date)}`);
        const data = await resp.json();
        if (data.report) {
            existingValues = data.report.values || {};
            existingStatus = data.report.status;
        }
    } catch(e) {}

    let html = '';
    if (!isAdmin) {
        html += `<button type="button" class="submit-back-btn" onclick="backToTemplateList()"><i class="fas fa-arrow-left"></i> Quay lại danh sách</button>`;
    }
    html += '<form id="reportForm" onsubmit="return submitReport(event)">';

    if (existingStatus) {
        const statusLabels = { draft: '📝 Bản nháp', submitted: '✅ Đã nộp', reviewed: '🔍 Đã đánh giá' };
        html += `<div style="margin-bottom:14px; padding:10px 14px; background:#f0f9ff; border-radius:10px; border-left:3px solid #0ea5e9; color:#0369a1; font-size:0.82rem;"><b>${statusLabels[existingStatus] || existingStatus}</b> — Bạn có thể chỉnh sửa và nộp lại</div>`;
    }

    // Schedule info
    if (template.type === 'custom' && template.schedule_config && template.schedule_config.dates) {
        html += `<div class="schedule-info"><i class="fas fa-calendar-alt"></i> <b>Ngày báo cáo:</b> ${template.schedule_config.dates.join(', ')}</div>`;
    } else if (template.type === 'monthly' && template.schedule_config && template.schedule_config.day_of_month) {
        html += `<div class="schedule-info"><i class="fas fa-calendar-day"></i> <b>Báo cáo vào ngày ${template.schedule_config.day_of_month} hằng tháng</b></div>`;
    }

    html += '<div class="report-form-grid">';
    template.fields.forEach(f => {
        const val = existingValues[f.id] || '';
        const isFullWidth = f.field_type === 'textarea';
        html += `<div class="rp-form-group${isFullWidth ? ' full-width' : ''}">`;
        html += `<label class="rp-label">${escapeHtml(f.label)}${f.is_required ? '<span class="required">*</span>' : ''}</label>`;

        if (f.field_type === 'text') {
            html += `<input type="text" class="rp-text-input" name="field_${f.id}" value="${escapeHtml(val)}" ${f.is_required ? 'required' : ''}>`;
        } else if (f.field_type === 'textarea') {
            html += `<textarea class="rp-textarea" name="field_${f.id}" ${f.is_required ? 'required' : ''}>${escapeHtml(val)}</textarea>`;
        } else if (f.field_type === 'number') {
            html += `<input type="number" class="rp-number-input" name="field_${f.id}" value="${val}" ${f.is_required ? 'required' : ''}>`;
        } else if (f.field_type === 'select') {
            html += `<select class="rp-select-field" name="field_${f.id}" ${f.is_required ? 'required' : ''}><option value="">-- Chọn --</option>`;
            (f.options || []).forEach(opt => {
                html += `<option value="${escapeHtml(opt)}" ${val === opt ? 'selected' : ''}>${escapeHtml(opt)}</option>`;
            });
            html += `</select>`;
        } else if (f.field_type === 'checkbox') {
            html += `<div class="rp-checkbox-wrap"><input type="checkbox" name="field_${f.id}" value="1" ${val == '1' ? 'checked' : ''}> Có</div>`;
        } else if (f.field_type === 'image') {
            html += `<div class="image-upload-area" id="imgArea_${f.id}">`;
            if (val) {
                html += `<div class="image-preview"><img src="${val}" style="max-width:200px;max-height:150px;border-radius:8px;border:1px solid #e2e8f0;"><button type="button" class="rp-btn rp-btn-secondary rp-btn-sm" onclick="document.getElementById('imgInput_${f.id}').click()" style="margin-top:6px;"><i class="fas fa-edit"></i> Đổi hình</button></div>`;
                html += `<input type="hidden" name="field_${f.id}" value="${escapeHtml(val)}">`;
            } else {
                html += `<div class="image-preview" style="text-align:center;padding:20px;background:#f8fafc;border:2px dashed #e2e8f0;border-radius:10px;cursor:pointer;" onclick="document.getElementById('imgInput_${f.id}').click()"><i class="fas fa-cloud-upload-alt" style="font-size:2rem;color:#94a3b8;"></i><p style="color:#94a3b8;font-size:0.78rem;margin-top:6px;">Click để chọn hình</p></div>`;
                html += `<input type="hidden" name="field_${f.id}" value="">`;
            }
            html += `<input type="file" id="imgInput_${f.id}" accept="image/*" style="display:none;" onchange="uploadReportImage(this, ${f.id})">`;
            html += `</div>`;
        }

        html += `</div>`;
    });
    html += '</div>';

    html += `<div style="display:flex; gap:8px; margin-top:20px;">`;
    html += `<button type="submit" class="rp-btn rp-btn-primary" name="submitAction" value="submitted"><i class="fas fa-paper-plane"></i> Nộp Báo Cáo</button>`;
    html += `<button type="submit" class="rp-btn rp-btn-secondary" name="submitAction" value="draft"><i class="fas fa-save"></i> Lưu Nháp</button>`;
    html += `</div>`;
    html += `</form>`;

    area.innerHTML = html;

    // Handle button click to set status
    area.querySelectorAll('button[name="submitAction"]').forEach(btn => {
        btn.addEventListener('click', function(e) {
            document.getElementById('reportForm').dataset.status = this.value;
        });
    });
}

// Upload hình đính kèm báo cáo
async function uploadReportImage(input, fieldId) {
    const file = input.files[0];
    if (!file) return;

    const area = document.getElementById(`imgArea_${fieldId}`);
    const preview = area.querySelector('.image-preview');
    preview.innerHTML = '<div style="text-align:center;padding:20px;"><i class="fas fa-spinner fa-spin" style="font-size:1.5rem;color:#0d9488;"></i><p style="color:#94a3b8;font-size:0.78rem;margin-top:6px;">Đang tải lên...</p></div>';

    const formData = new FormData();
    formData.append('image', file);

    try {
        const resp = await fetch("{{ route('reports.uploadImage') }}", {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken },
            body: formData
        });
        const data = await resp.json();
        if (data.success) {
            preview.innerHTML = `<img src="${data.url}" style="max-width:200px;max-height:150px;border-radius:8px;border:1px solid #e2e8f0;"><button type="button" class="rp-btn rp-btn-secondary rp-btn-sm" onclick="document.getElementById('imgInput_${fieldId}').click()" style="margin-top:6px;"><i class="fas fa-edit"></i> Đổi hình</button>`;
            area.querySelector(`input[name="field_${fieldId}"]`).value = data.url;
        } else {
            preview.innerHTML = '<p style="color:#ef4444;">Lỗi tải hình</p>';
        }
    } catch(e) {
        preview.innerHTML = '<p style="color:#ef4444;">Lỗi kết nối</p>';
    }
}

async function submitReport(e) {
    e.preventDefault();
    const form = e.target;
    const templateId = document.getElementById('submitTemplate').value;
    const date = toApiDate(document.getElementById('submitDate').value);
    const status = form.dataset.status || 'submitted';

    const template = allTemplates.find(t => t.id == templateId);
    if (!template) return false;

    const values = {};
    template.fields.forEach(f => {
        const input = form.querySelector(`[name="field_${f.id}"]`);
        if (input) {
            values[f.id] = input.type === 'checkbox' ? (input.checked ? '1' : '0') : input.value;
        }
    });

    try {
        const resp = await fetch("{{ route('reports.submit') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ template_id: templateId, report_date: date, status, values })
        });
        const data = await resp.json();
        if (data.success) {
            alert(data.message);
            loadSubmitForm();
        } else {
            alert(data.message || 'Lỗi');
        }
    } catch(e) {
        alert('Lỗi kết nối');
    }
    return false;
}

// ============================
// TAB 2: LỊCH SỬ
// ============================
async function loadHistory() {
    const area = document.getElementById('historyArea');
    area.innerHTML = '<div class="rp-loading"><i class="fas fa-spinner"></i> Đang tải...</div>';

    const params = new URLSearchParams();
    const date = toApiDate(document.getElementById('historyDate').value);
    const templateId = document.getElementById('historyTemplate').value;
    if (date) params.set('date', date);
    if (templateId) params.set('template_id', templateId);
    const userId = document.getElementById('historyUser')?.value;
    if (userId) params.set('user_id', userId);

    try {
        const resp = await fetch("{{ route('reports.list') }}?" + params.toString());
        const data = await resp.json();
        if (!data.success) { area.innerHTML = '<div class="rp-empty"><i class="fas fa-exclamation-triangle"></i><p>Lỗi tải dữ liệu</p></div>'; return; }

        let html = '';
        if (data.reports.length === 0) {
            html = '<div class="rp-empty"><i class="fas fa-inbox"></i><p>Không có báo cáo nào</p></div>';
        } else {
            data.reports.forEach(r => {
                const statusClass = 'status-' + r.status;
                const statusText = r.status === 'submitted' ? 'Đã nộp' : r.status === 'reviewed' ? 'Đã đánh giá' : 'Nháp';
                html += `<div class="report-card">`;
                html += `<div class="report-card-header">`;
                html += `<div><span class="report-card-user">${escapeHtml(r.user_name || 'N/A')}</span> <span class="report-card-date">· ${r.report_date} · ${escapeHtml(r.template_name)}</span></div>`;
                html += `<div style="display:flex;gap:6px;align-items:center;">`;
                html += `<span class="report-card-status ${statusClass}">${statusText}</span>`;
                if (data.is_admin && r.status !== 'reviewed') {
                    html += `<button class="rp-btn rp-btn-success rp-btn-sm" onclick="openReviewModal(${r.id})"><i class="fas fa-comment-dots"></i></button>`;
                }
                html += `</div></div>`;

                // Fields
                (r.values || []).forEach(v => {
                    if (v.field_type === 'image' && v.value) {
                        html += `<div class="report-field"><div class="report-field-label">${escapeHtml(v.label)}</div><div class="report-field-value"><img src="${v.value}" style="max-width:200px;max-height:150px;border-radius:8px;border:1px solid #e2e8f0;cursor:pointer;" onclick="window.open('${v.value}','_blank')"></div></div>`;
                    } else {
                        html += `<div class="report-field"><div class="report-field-label">${escapeHtml(v.label)}</div><div class="report-field-value">${escapeHtml(v.value || '-')}</div></div>`;
                    }
                });

                if (r.reviewer_note) {
                    html += `<div class="reviewer-note"><div class="reviewer-note-label"><i class="fas fa-user-check"></i> ${escapeHtml(r.reviewer_name || 'Admin')}</div><div class="reviewer-note-text">${escapeHtml(r.reviewer_note)}</div></div>`;
                }
                html += `</div>`;
            });
        }

        // Not submitted list
        if (data.not_submitted && data.not_submitted.length > 0) {
            html += `<div class="not-submitted"><h4><i class="fas fa-exclamation-circle"></i> Chưa nộp báo cáo (${data.not_submitted.length})</h4><div class="not-submitted-list">`;
            data.not_submitted.forEach(u => {
                html += `<span class="not-submitted-item">${escapeHtml(u.name)}</span>`;
            });
            html += `</div></div>`;
        }

        area.innerHTML = html;
    } catch(e) {
        area.innerHTML = '<div class="rp-empty"><i class="fas fa-exclamation-triangle"></i><p>Lỗi kết nối</p></div>';
    }
}

// ============================
// TAB 3: QUẢN LÝ MẪU
// ============================
async function loadTemplateList() {
    const area = document.getElementById('templateListArea');
    area.innerHTML = '<div class="rp-loading"><i class="fas fa-spinner"></i> Đang tải...</div>';

    try {
        const resp = await fetch("{{ route('reports.allTemplates') }}");
        const data = await resp.json();
        if (!data.success) return;

        if (data.templates.length === 0) {
            area.innerHTML = '<div class="rp-empty"><i class="fas fa-puzzle-piece"></i><p>Chưa có mẫu báo cáo nào</p></div>';
            return;
        }

        let html = '<div class="tpl-list">';
        data.templates.forEach(t => {
            const scheduleLabels = { daily: 'Hằng ngày', custom: 'Ngày tùy chọn', monthly: 'Cố định/tháng' };
            const scheduleClass = 'schedule-badge-' + (t.type || 'daily');
            html += `<div class="tpl-card">`;
            html += `<div style="display:flex;justify-content:space-between;align-items:flex-start;flex-direction:column;cursor:pointer;" onclick="editTemplate(${t.id})">`;
            html += `<div class="tpl-name">${escapeHtml(t.name)}</div>`;
            html += `<div style="display:flex;gap:4px;"><span class="schedule-badge ${scheduleClass}">${scheduleLabels[t.type] || 'Hằng ngày'}</span><span class="tpl-badge ${t.is_active ? 'tpl-badge-active' : 'tpl-badge-inactive'}">${t.is_active ? 'Active' : 'Inactive'}</span></div>`;
            html += `</div>`;
            html += `<div class="tpl-meta"><span><i class="fas fa-building"></i> ${escapeHtml(t.phongban_name || 'Tất cả')}</span> <span class="tpl-field-count"><i class="fas fa-list"></i> ${t.fields.length} trường</span></div>`;
            html += `<div class="tpl-actions">`;
            html += `<button class="tpl-btn-toggle" onclick="event.stopPropagation(); toggleTemplateActive(${t.id})">`;
            html += t.is_active ? '<i class="fas fa-toggle-on"></i> Tắt' : '<i class="fas fa-toggle-off"></i> Bật';
            html += `</button>`;
            html += `<button class="tpl-btn-delete" onclick="event.stopPropagation(); deleteTemplate(${t.id}, '${escapeHtml(t.name)}')"><i class="fas fa-trash"></i> Xóa</button>`;
            html += `</div>`;
            html += `</div>`;
        });
        html += '</div>';
        area.innerHTML = html;
    } catch(e) {
        area.innerHTML = '<div class="rp-empty"><i class="fas fa-exclamation-triangle"></i><p>Lỗi tải dữ liệu</p></div>';
    }
}

async function toggleTemplateActive(id) {
    try {
        const resp = await fetch(`/reports/templates/${id}/toggle-active`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
        });
        const data = await resp.json();
        if (data.success) {
            loadTemplateList();
            loadTemplates();
        } else {
            alert(data.message || 'Lỗi');
        }
    } catch(e) {
        alert('Lỗi kết nối');
    }
}

async function deleteTemplate(id, name) {
    if (!confirm(`Bạn có chắc muốn xóa mẫu "${name}"?\nTất cả báo cáo liên quan sẽ bị xóa.`)) return;
    try {
        const resp = await fetch(`/reports/templates/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken },
        });
        const data = await resp.json();
        if (data.success) {
            loadTemplateList();
            loadTemplates();
        } else {
            alert(data.message || 'Lỗi');
        }
    } catch(e) {
        alert('Lỗi kết nối');
    }
}

function openTemplateModal(templateData) {
    try {
        document.getElementById('tplModalTitle').textContent = templateData ? 'Chỉnh Sửa Mẫu' : 'Tạo Mẫu Báo Cáo';
        document.getElementById('tplEditId').value = templateData ? templateData.id : '';
        document.getElementById('tplName').value = templateData ? templateData.name : '';
        document.getElementById('tplPhongBan').value = templateData ? (templateData.MaPB || '') : '';
        document.getElementById('tplScheduleType').value = templateData ? (templateData.type || 'daily') : 'daily';
        toggleScheduleConfig(templateData ? templateData.schedule_config : null);

        const fieldsArea = document.getElementById('tplFields');
        fieldsArea.innerHTML = '';

        if (templateData && templateData.fields) {
            templateData.fields.forEach(f => addTemplateField(f));
        } else {
            addTemplateField();
        }

        // Init Select2 cho nhân viên
        if (!$('#tplUsers').data('select2')) {
            $('#tplUsers').select2({
                placeholder: 'Chọn nhân viên...',
                allowClear: true,
                width: '100%',
                dropdownParent: $('#templateModal')
            });
        }

        // Load users nếu đang sửa và có MaPB
        if (templateData && templateData.MaPB) {
            loadUsersForDepartment(templateData.user_ids || []);
        } else {
            $('#tplUsers').empty().trigger('change');
        }

        document.getElementById('templateModal').classList.add('show');
    } catch(e) {
        console.error('openTemplateModal error:', e);
        alert('Lỗi mở modal: ' + e.message);
    }
}

function closeTemplateModal() {
    document.getElementById('templateModal').classList.remove('show');
}

async function loadUsersForDepartment(preSelectedIds) {
    const MaPB = document.getElementById('tplPhongBan').value;
    const $select = $('#tplUsers');

    if (!MaPB) {
        $select.empty().trigger('change');
        return;
    }

    try {
        const resp = await fetch(`{{ route('reports.usersByDept') }}?MaPB=${MaPB}`);
        const data = await resp.json();
        if (!data.success) return;

        $select.empty();
        data.users.forEach(u => {
            $select.append(new Option(u.name, u.id, false, false));
        });

        // Auto-select: nếu có preSelectedIds thì chọn theo đó, không thì chọn tất cả
        if (preSelectedIds && preSelectedIds.length > 0) {
            $select.val(preSelectedIds.map(String)).trigger('change');
        } else {
            // Chọn tất cả nhân viên
            const allIds = data.users.map(u => String(u.id));
            $select.val(allIds).trigger('change');
        }
    } catch(e) {
        console.error('Error loading users:', e);
    }
}

let dragSrcEl = null;

function addTemplateField(fieldData) {
    const container = document.getElementById('tplFields');
    const div = document.createElement('div');
    div.className = 'builder-field';
    div.draggable = true;
    div.innerHTML = `
        <span class="drag-handle"><i class="fas fa-grip-vertical"></i></span>
        <div class="builder-field-inputs">
            <input type="text" class="builder-input field-label" placeholder="Tên trường" value="${fieldData ? escapeHtml(fieldData.label) : ''}">
            <select class="builder-input field-type" onchange="toggleOptionsInput(this)">
                <option value="text" ${fieldData?.field_type === 'text' ? 'selected' : ''}>Text</option>
                <option value="textarea" ${fieldData?.field_type === 'textarea' ? 'selected' : ''}>Textarea</option>
                <option value="number" ${fieldData?.field_type === 'number' ? 'selected' : ''}>Number</option>
                <option value="select" ${fieldData?.field_type === 'select' ? 'selected' : ''}>Select</option>
                <option value="checkbox" ${fieldData?.field_type === 'checkbox' ? 'selected' : ''}>Checkbox</option>
                <option value="image" ${fieldData?.field_type === 'image' ? 'selected' : ''}>📷 Hình đính kèm</option>
            </select>
            <label style="color:#94a3b8;font-size:0.72rem;display:flex;align-items:center;gap:4px;white-space:nowrap;">
                <input type="checkbox" class="field-required" ${fieldData?.is_required ? 'checked' : ''}> Bắt buộc
            </label>
        </div>
        <input type="text" class="builder-input field-options" placeholder="Các lựa chọn (cách nhau bởi dấu phẩy)" value="${fieldData?.options ? fieldData.options.join(', ') : ''}" style="display:${fieldData?.field_type === 'select' ? 'block' : 'none'}; margin-top:6px; width:100%;">
        <button class="builder-remove" onclick="this.closest('.builder-field').remove()"><i class="fas fa-trash"></i></button>
    `;

    // Drag & Drop events
    div.addEventListener('dragstart', function(e) {
        dragSrcEl = this;
        this.classList.add('dragging');
        e.dataTransfer.effectAllowed = 'move';
        e.dataTransfer.setData('text/html', this.outerHTML);
    });
    div.addEventListener('dragend', function() {
        this.classList.remove('dragging');
        container.querySelectorAll('.builder-field').forEach(f => f.classList.remove('drag-over'));
    });
    div.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.dataTransfer.dropEffect = 'move';
        this.classList.add('drag-over');
    });
    div.addEventListener('dragleave', function() {
        this.classList.remove('drag-over');
    });
    div.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('drag-over');
        if (dragSrcEl !== this && dragSrcEl) {
            // Determine position: insert before or after based on mouse Y
            const rect = this.getBoundingClientRect();
            const midY = rect.top + rect.height / 2;
            if (e.clientY < midY) {
                container.insertBefore(dragSrcEl, this);
            } else {
                container.insertBefore(dragSrcEl, this.nextSibling);
            }
        }
    });

    // Prevent drag when interacting with inputs
    div.querySelectorAll('input, select, button, textarea').forEach(inp => {
        inp.addEventListener('mousedown', () => { div.draggable = false; });
        inp.addEventListener('mouseup', () => { div.draggable = true; });
    });
    // Re-enable drag on handle
    div.querySelector('.drag-handle').addEventListener('mousedown', () => { div.draggable = true; });

    container.appendChild(div);
}

function toggleOptionsInput(select) {
    const field = select.closest('.builder-field');
    const optionsInput = field.querySelector('.field-options');
    optionsInput.style.display = select.value === 'select' ? 'block' : 'none';
}

function toggleScheduleConfig(existingConfig) {
    const type = document.getElementById('tplScheduleType').value;
    const area = document.getElementById('scheduleConfigArea');
    area.innerHTML = '';

    if (type === 'custom') {
        let html = `<div class="schedule-group">`;
        html += `<label><i class="fas fa-calendar-plus"></i> Chọn các ngày báo cáo</label>`;
        html += `<div style="display:flex;gap:6px;align-items:center;"><input type="text" class="rp-input rp-datepicker" id="scheduleAddDate" placeholder="dd/mm/yyyy" style="flex:1;" readonly> <button class="rp-btn rp-btn-primary rp-btn-sm" onclick="addScheduleDate()"><i class="fas fa-plus"></i></button></div>`;
        html += `<div class="schedule-dates-list" id="scheduleDatesList"></div>`;
        html += `</div>`;
        area.innerHTML = html;

        // Init flatpickr on the newly created element
        setTimeout(() => { initFlatpickr('#scheduleAddDate'); }, 50);

        // Populate existing dates
        if (existingConfig && existingConfig.dates) {
            existingConfig.dates.forEach(d => addScheduleDateTag(d));
        }
    } else if (type === 'monthly') {
        let html = `<div class="schedule-group">`;
        html += `<label><i class="fas fa-calendar-day"></i> Ngày cố định trong tháng (1-31)</label>`;
        const existDay = existingConfig && existingConfig.day_of_month ? existingConfig.day_of_month : '';
        html += `<input type="number" class="rp-number-input" id="scheduleMonthDay" min="1" max="31" placeholder="VD: 15" value="${existDay}">`;
        html += `</div>`;
        area.innerHTML = html;
    }
}

function addScheduleDate() {
    const input = document.getElementById('scheduleAddDate');
    if (!input.value) return;
    addScheduleDateTag(input.value);
    input.value = '';
}

function addScheduleDateTag(dateStr) {
    const list = document.getElementById('scheduleDatesList');
    // Avoid duplicate
    if (list.querySelector(`[data-value="${dateStr}"]`)) return;
    const tag = document.createElement('span');
    tag.className = 'schedule-date-tag';
    tag.dataset.value = dateStr;
    tag.innerHTML = `${dateStr} <span class="remove-tag" onclick="this.parentElement.remove()">&times;</span>`;
    list.appendChild(tag);
}

async function saveTemplate() {
    const name = document.getElementById('tplName').value.trim();
    const MaPB = document.getElementById('tplPhongBan').value || null;
    const editId = document.getElementById('tplEditId').value;

    if (!name) { alert('Vui lòng nhập tên mẫu'); return; }

    const type = document.getElementById('tplScheduleType').value;
    let schedule_config = null;
    if (type === 'custom') {
        const tags = document.querySelectorAll('#scheduleConfigArea .schedule-date-tag');
        const dates = [];
        tags.forEach(t => { const d = t.dataset.value; if (d) dates.push(d); });
        if (dates.length === 0) { alert('Vui lòng chọn ít nhất 1 ngày'); return; }
        schedule_config = { dates };
    } else if (type === 'monthly') {
        const dayInput = document.getElementById('scheduleMonthDay');
        const day = dayInput ? parseInt(dayInput.value) : null;
        if (!day || day < 1 || day > 31) { alert('Vui lòng chọn ngày hợp lệ (1-31)'); return; }
        schedule_config = { day_of_month: day };
    }

    const fieldEls = document.querySelectorAll('#tplFields .builder-field');
    if (fieldEls.length === 0) { alert('Vui lòng thêm ít nhất 1 trường'); return; }

    const fields = [];
    let valid = true;
    fieldEls.forEach(el => {
        const label = el.querySelector('.field-label').value.trim();
        const fieldType = el.querySelector('.field-type').value;
        const isRequired = el.querySelector('.field-required').checked;
        const optionsStr = el.querySelector('.field-options').value.trim();
        if (!label) { valid = false; return; }
        const field = { label, field_type: fieldType, is_required: isRequired };
        if (fieldType === 'select' && optionsStr) {
            field.options = optionsStr.split(',').map(s => s.trim()).filter(s => s);
        }
        fields.push(field);
    });

    if (!valid) { alert('Vui lòng nhập tên cho tất cả các trường'); return; }

    const url = editId ? `{{ url('/reports/templates') }}/${editId}` : "{{ route('reports.templates.store') }}";
    const method = editId ? 'PUT' : 'POST';

    // Lấy danh sách user_ids từ Select2
    const user_ids = $('#tplUsers').val() || [];

    try {
        const resp = await fetch(url, {
            method,
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ name, MaPB, type, schedule_config, fields, user_ids })
        });
        const data = await resp.json();
        if (data.success) {
            closeTemplateModal();
            loadTemplateList();
            loadTemplates();
            alert(data.message);
        } else {
            alert(data.message || 'Lỗi');
        }
    } catch(e) {
        alert('Lỗi kết nối');
    }
}

async function editTemplate(id) {
    try {
        const resp = await fetch("{{ route('reports.allTemplates') }}");
        const data = await resp.json();
        if (!data.success) { console.error('API error', data); return; }
        console.log('Templates loaded:', data.templates.length, 'Looking for id:', id, typeof id);
        const t = data.templates.find(x => x.id == id);
        if (t) {
            console.log('Found template:', t.name);
            openTemplateModal(t);
        } else {
            console.error('Template not found with id:', id);
            alert('Không tìm thấy mẫu báo cáo');
        }
    } catch(e) {
        console.error('editTemplate error:', e);
        alert('Lỗi: ' + e.message);
    }
}

async function deleteTemplate(id) {
    if (!confirm('Xóa mẫu này? Tất cả báo cáo theo mẫu này cũng sẽ bị xóa.')) return;
    try {
        const resp = await fetch(`{{ url('/reports/templates') }}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        const data = await resp.json();
        if (data.success) {
            loadTemplateList();
            loadTemplates();
        }
    } catch(e) {}
}

// ============================
// TAB 4: THỐNG KÊ
// ============================
async function loadStats() {
    const area = document.getElementById('statsArea');
    area.innerHTML = '<div class="rp-loading"><i class="fas fa-spinner"></i> Đang tải...</div>';

    const params = new URLSearchParams();
    const from = toApiDate(document.getElementById('statsFrom').value);
    const to = toApiDate(document.getElementById('statsTo').value);
    const templateId = document.getElementById('statsTemplate').value;
    if (from) params.set('from', from);
    if (to) params.set('to', to);
    if (templateId) params.set('template_id', templateId);

    try {
        const resp = await fetch("{{ route('reports.stats') }}?" + params.toString());
        const data = await resp.json();
        if (!data.success) return;

        const totalReports = data.stats.reduce((s, u) => s + u.total_reports, 0);
        const totalReviewed = data.stats.reduce((s, u) => s + u.reviewed, 0);

        let html = '<div class="stats-grid">';
        html += `<div class="stat-card"><div class="stat-label">Nhân viên đã nộp</div><div class="stat-value">${data.stats.length}</div></div>`;
        html += `<div class="stat-card"><div class="stat-label">Tổng báo cáo</div><div class="stat-value">${totalReports}</div></div>`;
        html += `<div class="stat-card"><div class="stat-label">Ngày làm việc</div><div class="stat-value">${data.working_days}</div></div>`;
        html += `<div class="stat-card"><div class="stat-label">Đã đánh giá</div><div class="stat-value">${totalReviewed}</div></div>`;
        html += '</div>';

        if (data.stats.length > 0) {
            html += '<table class="stats-table"><thead><tr><th>Nhân viên</th><th>Số báo cáo</th><th>Tỷ lệ nộp</th><th>Đã đánh giá</th></tr></thead><tbody>';
            data.stats.forEach(s => {
                const pct = data.working_days > 0 ? Math.round((s.total_reports / data.working_days) * 100) : 0;
                const color = pct >= 80 ? '#22c55e' : pct >= 50 ? '#f59e0b' : '#ef4444';
                html += `<tr>`;
                html += `<td style="font-weight:600;">${escapeHtml(s.user_name)}</td>`;
                html += `<td>${s.total_reports} / ${data.working_days}</td>`;
                html += `<td><div style="display:flex;align-items:center;gap:8px;"><div class="completion-bar"><div class="completion-fill" style="width:${pct}%;background:${color};"></div></div><span style="font-size:0.78rem;color:${color};font-weight:700;">${pct}%</span></div></td>`;
                html += `<td>${s.reviewed}</td>`;
                html += `</tr>`;
            });
            html += '</tbody></table>';
        }

        area.innerHTML = html;
    } catch(e) {
        area.innerHTML = '<div class="rp-empty"><i class="fas fa-exclamation-triangle"></i><p>Lỗi tải dữ liệu</p></div>';
    }
}

// ============================
// REVIEW MODAL
// ============================
function openReviewModal(reportId) {
    document.getElementById('reviewReportId').value = reportId;
    document.getElementById('reviewNote').value = '';
    document.getElementById('reviewModal').classList.add('show');
}

function closeReviewModal() {
    document.getElementById('reviewModal').classList.remove('show');
}

async function submitReview() {
    const id = document.getElementById('reviewReportId').value;
    const note = document.getElementById('reviewNote').value;

    try {
        const resp = await fetch(`{{ url('/reports/review') }}/${id}`, {
            method: 'PUT',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ note })
        });
        const data = await resp.json();
        if (data.success) {
            closeReviewModal();
            loadHistory();
        }
    } catch(e) {
        alert('Lỗi kết nối');
    }
}

// ============================
// HELPERS
// ============================
function escapeHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

// Keyboard
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        closeTemplateModal();
        closeReviewModal();
    }
});
</script>
@endpush
