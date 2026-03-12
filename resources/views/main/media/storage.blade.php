@extends('main.layouts.app')

@section('title', 'Quản Lý Dữ Liệu')

@push('styles')
<style>
/* ========================================
   MEDIA STORAGE MANAGEMENT STYLES
   ======================================== */

/* Page Header */
.media-page-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 28px;
    flex-wrap: wrap;
    gap: 16px;
}
.media-page-header h1 {
    font-size: 1.7rem;
    font-weight: 800;
    background: linear-gradient(135deg, #6d28d9, #a78bfa, #c084fc);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -0.5px;
    margin: 0;
}
.media-page-header .header-subtitle {
    font-size: 0.92rem;
    color: #94a3b8;
    margin-top: 4px;
}

/* Connection Status Badge */
.connection-status {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 18px;
    border-radius: 999px;
    font-size: 0.85rem;
    font-weight: 600;
    transition: all 0.3s ease;
}
.connection-status.connected {
    background: rgba(34, 197, 94, 0.12);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.25);
}
.connection-status.disconnected {
    background: rgba(239, 68, 68, 0.12);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.25);
}
.connection-status .status-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    animation: pulse-dot 2s infinite;
}
.connection-status.connected .status-dot { background: #22c55e; }
.connection-status.disconnected .status-dot { background: #ef4444; }

@keyframes pulse-dot {
    0%, 100% { opacity: 1; transform: scale(1); }
    50% { opacity: 0.5; transform: scale(1.3); }
}

/* ---- Config Section ---- */
.media-config-card {
    background: linear-gradient(135deg, rgba(30, 27, 75, 0.95), rgba(20, 20, 50, 0.98));
    border: 1px solid rgba(139, 92, 246, 0.15);
    border-radius: 18px;
    padding: 28px 32px;
    margin-bottom: 28px;
    position: relative;
    overflow: hidden;
}
.media-config-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(90deg, #6d28d9, #a78bfa, #c084fc, #6d28d9);
    background-size: 200%;
    animation: gradient-slide 3s linear infinite;
}
@keyframes gradient-slide {
    0% { background-position: 0% 50%; }
    100% { background-position: 200% 50%; }
}
.config-card-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #e2e8f0;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
}
.config-card-title i {
    color: #a78bfa;
    font-size: 1.1rem;
}
.config-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 16px;
}
.config-grid .config-field {
    display: flex;
    flex-direction: column;
    gap: 6px;
}
.config-grid .config-field.full-width {
    grid-column: 1 / -1;
}
.config-field label {
    font-size: 0.82rem;
    font-weight: 600;
    color: #94a3b8;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}
.config-field input {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-radius: 10px;
    padding: 10px 14px;
    color: #e2e8f0;
    font-size: 0.92rem;
    transition: all 0.3s ease;
    outline: none;
}
.config-field input:focus {
    border-color: #a78bfa;
    box-shadow: 0 0 0 3px rgba(139, 92, 246, 0.1);
}
.config-field input::placeholder { color: #475569; }
.config-actions {
    display: flex;
    gap: 12px;
    margin-top: 22px;
    flex-wrap: wrap;
}
.btn-test-connection, .btn-save-config {
    padding: 10px 24px;
    border-radius: 10px;
    font-size: 0.9rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}
.btn-test-connection {
    background: linear-gradient(135deg, #0ea5e9, #38bdf8);
    color: #fff;
}
.btn-test-connection:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(14, 165, 233, 0.3);
}
.btn-save-config {
    background: linear-gradient(135deg, #6d28d9, #a78bfa);
    color: #fff;
}
.btn-save-config:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(109, 40, 217, 0.3);
}
.btn-test-connection:disabled, .btn-save-config:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}
.config-result {
    margin-top: 14px;
    padding: 10px 16px;
    border-radius: 10px;
    font-size: 0.88rem;
    display: none;
}
.config-result.success {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
    border: 1px solid rgba(34, 197, 94, 0.2);
    display: block;
}
.config-result.error {
    background: rgba(239, 68, 68, 0.1);
    color: #ef4444;
    border: 1px solid rgba(239, 68, 68, 0.2);
    display: block;
}

/* Toggle config section */
.config-toggle-btn {
    background: rgba(139, 92, 246, 0.1);
    border: 1px solid rgba(139, 92, 246, 0.2);
    color: #a78bfa;
    padding: 8px 18px;
    border-radius: 10px;
    font-size: 0.85rem;
    font-weight: 600;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}
.config-toggle-btn:hover {
    background: rgba(139, 92, 246, 0.2);
}

/* ---- File Browser ---- */
.file-browser-card {
    background: linear-gradient(135deg, rgba(30, 27, 75, 0.95), rgba(20, 20, 50, 0.98));
    border: 1px solid rgba(139, 92, 246, 0.15);
    border-radius: 18px;
    overflow: hidden;
}

/* Toolbar */
.browser-toolbar {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 16px 24px;
    border-bottom: 1px solid rgba(139, 92, 246, 0.1);
    flex-wrap: wrap;
    gap: 12px;
}
.browser-breadcrumb {
    display: flex;
    align-items: center;
    gap: 4px;
    flex-wrap: wrap;
}
.breadcrumb-item {
    color: #94a3b8;
    font-size: 0.88rem;
    cursor: pointer;
    padding: 4px 10px;
    border-radius: 6px;
    transition: all 0.2s ease;
    background: none;
    border: none;
    font-weight: 500;
}
.breadcrumb-item:hover, .breadcrumb-item.active {
    color: #a78bfa;
    background: rgba(139, 92, 246, 0.1);
}
.breadcrumb-separator {
    color: #475569;
    font-size: 0.75rem;
}
.browser-actions {
    display: flex;
    gap: 8px;
}
.btn-browser-action {
    padding: 8px 16px;
    border-radius: 10px;
    font-size: 0.82rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all 0.3s ease;
}
.btn-upload {
    background: linear-gradient(135deg, #22c55e, #4ade80);
    color: #fff;
}
.btn-upload:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 14px rgba(34, 197, 94, 0.3);
}
.btn-new-folder {
    background: rgba(139, 92, 246, 0.12);
    color: #a78bfa;
    border: 1px solid rgba(139, 92, 246, 0.2);
}
.btn-new-folder:hover {
    background: rgba(139, 92, 246, 0.22);
}
.btn-refresh {
    background: rgba(14, 165, 233, 0.12);
    color: #38bdf8;
    border: 1px solid rgba(14, 165, 233, 0.2);
}
.btn-refresh:hover {
    background: rgba(14, 165, 233, 0.22);
}

/* Drop Zone */
.drop-zone {
    border: 2px dashed rgba(139, 92, 246, 0.2);
    border-radius: 14px;
    padding: 40px;
    text-align: center;
    margin: 16px 24px;
    transition: all 0.3s ease;
    display: none;
}
.drop-zone.active {
    display: block;
}
.drop-zone.drag-over {
    border-color: #a78bfa;
    background: rgba(139, 92, 246, 0.08);
}
.drop-zone-icon {
    font-size: 2.5rem;
    color: #6d28d9;
    margin-bottom: 12px;
}
.drop-zone-text {
    color: #94a3b8;
    font-size: 0.92rem;
}
.drop-zone-text strong {
    color: #a78bfa;
    cursor: pointer;
}

/* File Grid */
.file-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
    gap: 14px;
    padding: 20px 24px;
}
.file-item {
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(139, 92, 246, 0.08);
    border-radius: 14px;
    padding: 16px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}
.file-item:hover {
    border-color: rgba(139, 92, 246, 0.3);
    background: rgba(139, 92, 246, 0.06);
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.2);
}
.file-item-icon {
    width: 56px;
    height: 56px;
    margin: 0 auto 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 12px;
    font-size: 1.6rem;
}
.file-item-icon.folder {
    background: rgba(251, 191, 36, 0.12);
    color: #fbbf24;
}
.file-item-icon.image {
    background: rgba(34, 197, 94, 0.12);
    color: #22c55e;
}
.file-item-icon.video {
    background: rgba(239, 68, 68, 0.12);
    color: #ef4444;
}
.file-item-icon.document {
    background: rgba(14, 165, 233, 0.12);
    color: #38bdf8;
}
.file-item-icon.other {
    background: rgba(148, 163, 184, 0.12);
    color: #94a3b8;
}
.file-item-preview {
    width: 100%;
    height: 80px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 10px;
}
.file-item-name {
    font-size: 0.82rem;
    color: #e2e8f0;
    font-weight: 500;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    margin-bottom: 4px;
}
.file-item-meta {
    font-size: 0.72rem;
    color: #64748b;
}
.file-item-actions {
    position: absolute;
    top: 8px;
    right: 8px;
    opacity: 0;
    transition: opacity 0.2s ease;
}
.file-item:hover .file-item-actions { opacity: 1; }
.file-item-delete {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(239, 68, 68, 0.2);
    border: none;
    color: #ef4444;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    transition: all 0.2s;
}
.file-item-delete:hover {
    background: rgba(239, 68, 68, 0.4);
}

/* Loading & Empty States */
.browser-loading {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px;
    gap: 12px;
    color: #94a3b8;
    font-size: 0.92rem;
}
.browser-loading .spinner {
    width: 24px;
    height: 24px;
    border: 3px solid rgba(139, 92, 246, 0.15);
    border-top-color: #a78bfa;
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}
@keyframes spin {
    to { transform: rotate(360deg); }
}
.browser-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 60px;
    color: #64748b;
    text-align: center;
}
.browser-empty i {
    font-size: 3rem;
    margin-bottom: 16px;
    opacity: 0.4;
}
.browser-empty p {
    font-size: 0.92rem;
}

/* New Folder Modal */
.media-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.6);
    backdrop-filter: blur(4px);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
}
.media-modal-overlay.show { display: flex; }
.media-modal {
    background: linear-gradient(135deg, rgba(30, 27, 75, 0.98), rgba(20, 20, 50, 1));
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-radius: 18px;
    padding: 28px 32px;
    width: 90%;
    max-width: 420px;
    animation: modalIn 0.3s ease;
}
@keyframes modalIn {
    from { opacity: 0; transform: scale(0.9) translateY(20px); }
    to { opacity: 1; transform: scale(1) translateY(0); }
}
.media-modal h3 {
    color: #e2e8f0;
    font-size: 1.1rem;
    margin-bottom: 18px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.media-modal input {
    width: 100%;
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-radius: 10px;
    padding: 10px 14px;
    color: #e2e8f0;
    font-size: 0.92rem;
    outline: none;
    box-sizing: border-box;
}
.media-modal input:focus {
    border-color: #a78bfa;
}
.media-modal-actions {
    display: flex;
    gap: 10px;
    justify-content: flex-end;
    margin-top: 18px;
}
.btn-modal-cancel {
    padding: 8px 20px;
    border-radius: 10px;
    font-size: 0.88rem;
    font-weight: 600;
    border: 1px solid rgba(148, 163, 184, 0.2);
    background: transparent;
    color: #94a3b8;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-modal-cancel:hover { background: rgba(148, 163, 184, 0.1); }
.btn-modal-confirm {
    padding: 8px 20px;
    border-radius: 10px;
    font-size: 0.88rem;
    font-weight: 600;
    border: none;
    background: linear-gradient(135deg, #6d28d9, #a78bfa);
    color: #fff;
    cursor: pointer;
    transition: all 0.2s;
}
.btn-modal-confirm:hover { transform: translateY(-1px); }

/* Preview Modal */
.preview-modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0,0,0,0.85);
    backdrop-filter: blur(8px);
    z-index: 9999;
    display: none;
    align-items: center;
    justify-content: center;
}
.preview-modal-overlay.show { display: flex; }
.preview-modal {
    max-width: 90%;
    max-height: 90%;
    position: relative;
}
.preview-modal img, .preview-modal video {
    max-width: 100%;
    max-height: 80vh;
    border-radius: 14px;
    box-shadow: 0 20px 60px rgba(0,0,0,0.5);
}
.preview-close {
    position: absolute;
    top: -40px;
    right: 0;
    background: rgba(255,255,255,0.1);
    border: none;
    color: #fff;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
}
.preview-file-info {
    color: #94a3b8;
    text-align: center;
    margin-top: 12px;
    font-size: 0.85rem;
}

/* Upload progress */
.upload-progress-bar {
    width: 100%;
    height: 4px;
    background: rgba(139, 92, 246, 0.1);
    border-radius: 2px;
    margin-top: 12px;
    overflow: hidden;
    display: none;
}
.upload-progress-bar.active { display: block; }
.upload-progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #6d28d9, #a78bfa);
    border-radius: 2px;
    width: 0%;
    transition: width 0.3s ease;
}

/* Permission Badge on Folder */
.perm-badge {
    position: absolute;
    top: 8px;
    left: 8px;
    background: rgba(14, 165, 233, 0.2);
    color: #38bdf8;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 2px 7px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    gap: 3px;
}
.perm-badge i { font-size: 0.6rem; }

/* Permission Button on Folder Hover */
.file-item-perm {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(14, 165, 233, 0.2);
    border: none;
    color: #38bdf8;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    transition: all 0.2s;
    margin-right: 4px;
}
.file-item-perm:hover {
    background: rgba(14, 165, 233, 0.4);
}
.file-item-rename {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: rgba(251, 191, 36, 0.2);
    border: none;
    color: #fbbf24;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.78rem;
    transition: all 0.2s;
    margin-right: 4px;
}
.file-item-rename:hover {
    background: rgba(251, 191, 36, 0.4);
}
.perm-list-badge.rename {
    background: rgba(251, 191, 36, 0.15);
    color: #fbbf24;
}

/* Permission Modal (wider) */
.perm-modal {
    background: linear-gradient(135deg, rgba(30, 27, 75, 0.98), rgba(20, 20, 50, 1));
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-radius: 18px;
    padding: 28px 32px;
    width: 92%;
    max-width: 600px;
    animation: modalIn 0.3s ease;
    max-height: 85vh;
    overflow-y: auto;
}
.perm-modal h3 {
    color: #e2e8f0;
    font-size: 1.1rem;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.perm-folder-path {
    color: #64748b;
    font-size: 0.8rem;
    margin-bottom: 18px;
    word-break: break-all;
}
.perm-form-row {
    display: grid;
    grid-template-columns: 140px 1fr;
    gap: 12px;
    align-items: center;
    margin-bottom: 12px;
}
.perm-form-row label {
    font-size: 0.82rem;
    font-weight: 600;
    color: #94a3b8;
}
.perm-form-row select, .perm-form-row input[type="text"] {
    background: rgba(255,255,255,0.05);
    border: 1px solid rgba(139, 92, 246, 0.2);
    border-radius: 10px;
    padding: 8px 12px;
    color: #e2e8f0;
    font-size: 0.88rem;
    outline: none;
    width: 100%;
    box-sizing: border-box;
}
.perm-form-row select:focus { border-color: #a78bfa; }
.perm-toggles {
    display: flex;
    gap: 20px;
    align-items: center;
}
.perm-toggle-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.85rem;
    color: #cbd5e1;
}
.perm-toggle-item input[type="checkbox"] {
    width: 18px;
    height: 18px;
    accent-color: #a78bfa;
    cursor: pointer;
}
.perm-divider {
    border: none;
    border-top: 1px solid rgba(139, 92, 246, 0.1);
    margin: 18px 0;
}
.perm-list-title {
    font-size: 0.88rem;
    font-weight: 700;
    color: #94a3b8;
    margin-bottom: 10px;
}
.perm-list-empty {
    color: #475569;
    font-size: 0.82rem;
    text-align: center;
    padding: 16px;
}
.perm-list-item {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: rgba(255,255,255,0.03);
    border: 1px solid rgba(139, 92, 246, 0.08);
    border-radius: 10px;
    padding: 10px 14px;
    margin-bottom: 8px;
    transition: all 0.2s;
}
.perm-list-item:hover {
    border-color: rgba(139, 92, 246, 0.2);
}
.perm-list-info {
    display: flex;
    flex-direction: column;
    gap: 2px;
}
.perm-list-name {
    font-size: 0.88rem;
    color: #e2e8f0;
    font-weight: 600;
}
.perm-list-type {
    font-size: 0.72rem;
    color: #64748b;
}
.perm-list-badges {
    display: flex;
    gap: 6px;
    align-items: center;
}
.perm-list-badge {
    font-size: 0.68rem;
    font-weight: 700;
    padding: 3px 8px;
    border-radius: 6px;
    text-transform: uppercase;
}
.perm-list-badge.upload {
    background: rgba(34, 197, 94, 0.15);
    color: #22c55e;
}
.perm-list-badge.delete {
    background: rgba(239, 68, 68, 0.15);
    color: #ef4444;
}
.perm-list-remove {
    width: 26px;
    height: 26px;
    border-radius: 6px;
    background: rgba(239, 68, 68, 0.15);
    border: none;
    color: #ef4444;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.72rem;
    transition: all 0.2s;
    margin-left: 10px;
}
.perm-list-remove:hover {
    background: rgba(239, 68, 68, 0.3);
}

/* Responsive */
@media (max-width: 768px) {
    .config-grid { grid-template-columns: 1fr; }
    .file-grid { grid-template-columns: repeat(auto-fill, minmax(130px, 1fr)); gap: 10px; }
    .media-config-card { padding: 20px; }
    .browser-toolbar { padding: 12px 16px; }
    .file-grid { padding: 14px 16px; }
    .perm-form-row { grid-template-columns: 1fr; gap: 6px; }
    .perm-modal { padding: 20px; }
}
</style>
@endpush

@section('content')
<div class="media-page-header">
    <div>
        <h1><i class="fas fa-database"></i> Quản Lý Dữ Liệu</h1>
        <div class="header-subtitle">Thiết lập và quản lý Cloud Storage (S3)</div>
    </div>
    <div style="display:flex; gap:12px; align-items:center;">
        <span class="connection-status {{ $isConfigured ? 'connected' : 'disconnected' }}" id="connectionStatus">
            <span class="status-dot"></span>
            <span id="statusText">{{ $isConfigured ? 'Đã kết nối' : 'Chưa kết nối' }}</span>
        </span>
        <span class="connection-status connected" id="totalStorageBadge" style="display:none;">
            <i class="fas fa-database"></i>
            <span id="totalStorageText">...</span>
        </span>
        <button class="config-toggle-btn" onclick="toggleConfig()">
            <i class="fas fa-cog"></i> Cài Đặt
        </button>
    </div>
</div>

<!-- S3 Configuration Section -->
<div class="media-config-card" id="configSection" style="{{ $isConfigured ? 'display:none;' : '' }}">
    <div class="config-card-title">
        <i class="fas fa-cloud"></i> Cấu Hình Cloud Storage (S3)
    </div>
    <div class="config-grid">
        <div class="config-field full-width">
            <label>Endpoint URL</label>
            <input type="url" id="cfgEndpoint" value="{{ $config['endpoint'] }}" placeholder="https://your-s3-endpoint.com">
        </div>
        <div class="config-field">
            <label>Access Key ID</label>
            <input type="text" id="cfgAccessKey" value="{{ $config['access_key'] }}" placeholder="Your access key">
        </div>
        <div class="config-field">
            <label>Secret Access Key</label>
            <input type="password" id="cfgSecretKey" value="{{ $config['secret_key'] }}" placeholder="Your secret key">
        </div>
        <div class="config-field">
            <label>Bucket Name</label>
            <input type="text" id="cfgBucket" value="{{ $config['bucket'] }}" placeholder="your-bucket-name">
        </div>
        <div class="config-field">
            <label>Region</label>
            <input type="text" id="cfgRegion" value="{{ $config['region'] }}" placeholder="ap-southeast-1">
        </div>
    </div>
    <div class="config-actions">
        <button class="btn-test-connection" id="btnTestConnection" onclick="testConnection()">
            <i class="fas fa-plug"></i> Kiểm Tra Kết Nối
        </button>
        <button class="btn-save-config" id="btnSaveConfig" onclick="saveConfig()">
            <i class="fas fa-save"></i> Lưu Cấu Hình
        </button>
    </div>
    <div class="config-result" id="configResult"></div>
</div>

<!-- File Browser -->
<div class="file-browser-card" id="fileBrowser">
    <div class="browser-toolbar">
        <div class="browser-breadcrumb" id="breadcrumb">
            <button class="breadcrumb-item active" onclick="browsePath('')">
                <i class="fas fa-home"></i> Root
            </button>
        </div>
        <div class="browser-actions">
            <button class="btn-browser-action btn-refresh" onclick="refreshBrowser()">
                <i class="fas fa-sync-alt"></i> Làm Mới
            </button>
            <button class="btn-browser-action btn-new-folder" onclick="showNewFolderModal()">
                <i class="fas fa-folder-plus"></i> Thư Mục Mới
            </button>
            <button class="btn-browser-action btn-upload" onclick="toggleUploadZone()">
                <i class="fas fa-cloud-upload-alt"></i> Upload
            </button>
        </div>
    </div>

    <!-- Upload Drop Zone -->
    <div class="drop-zone" id="dropZone">
        <div class="drop-zone-icon"><i class="fas fa-cloud-upload-alt"></i></div>
        <div class="drop-zone-text">
            Kéo thả file vào đây hoặc <strong onclick="document.getElementById('fileInput').click()">chọn file</strong>
        </div>
        <input type="file" id="fileInput" multiple style="display:none" onchange="uploadFiles(this.files)">
        <div class="upload-progress-bar" id="uploadProgress">
            <div class="upload-progress-fill" id="uploadProgressFill"></div>
        </div>
    </div>

    <!-- Browser Content -->
    <div id="browserContent">
        <div class="browser-loading" id="browserLoading">
            <div class="spinner"></div>
            <span>Đang tải dữ liệu...</span>
        </div>
        <div class="file-grid" id="fileGrid" style="display:none;"></div>
        <div class="browser-empty" id="browserEmpty" style="display:none;">
            <i class="fas fa-folder-open"></i>
            <p>Thư mục trống</p>
        </div>
    </div>
</div>

<!-- New Folder Modal -->
<div class="media-modal-overlay" id="newFolderModal">
    <div class="media-modal">
        <h3><i class="fas fa-folder-plus" style="color:#a78bfa;"></i> Tạo Thư Mục Mới</h3>
        <input type="text" id="newFolderName" placeholder="Tên thư mục...">
        <div class="media-modal-actions">
            <button class="btn-modal-cancel" onclick="closeNewFolderModal()">Hủy</button>
            <button class="btn-modal-confirm" onclick="createFolder()">Tạo</button>
        </div>
    </div>
</div>

<!-- Preview Modal -->
<div class="preview-modal-overlay" id="previewModal" onclick="closePreview()">
    <div class="preview-modal" onclick="event.stopPropagation()">
        <button class="preview-close" onclick="closePreview()"><i class="fas fa-times"></i></button>
        <div id="previewContent"></div>
        <div class="preview-file-info" id="previewFileInfo"></div>
    </div>
</div>

<!-- Permission Modal -->
<div class="media-modal-overlay" id="permModal">
    <div class="perm-modal">
        <h3><i class="fas fa-shield-alt" style="color:#38bdf8;"></i> Phân Quyền Thư Mục</h3>
        <div class="perm-folder-path" id="permFolderPath">📁 /</div>

        <!-- Add Permission Form -->
        <div class="perm-form-row">
            <label>Loại quyền</label>
            <select id="permType" onchange="onPermTypeChange()">
                <option value="user">👤 Theo Nhân Viên</option>
                <option value="department">🏢 Theo Phòng Ban</option>
            </select>
        </div>
        <div class="perm-form-row" id="permUserRow">
            <label>Chọn nhân viên</label>
            <select id="permUserId">
                <option value="">-- Chọn --</option>
                @foreach($users as $u)
                <option value="{{ $u->id }}">{{ $u->name }} (ID: {{ $u->id }})</option>
                @endforeach
            </select>
        </div>
        <div class="perm-form-row" id="permDeptRow" style="display:none;">
            <label>Chọn phòng ban</label>
            <select id="permDeptId">
                <option value="">-- Chọn --</option>
                @foreach($departments as $d)
                <option value="{{ $d->MaPB }}">{{ $d->TenPB }}</option>
                @endforeach
            </select>
        </div>
        <div class="perm-form-row">
            <label>Quyền</label>
            <div class="perm-toggles">
                <label class="perm-toggle-item">
                    <input type="checkbox" id="permCanView" checked> Xem
                </label>
                <label class="perm-toggle-item">
                    <input type="checkbox" id="permCanUpload" checked> Upload
                </label>
                <label class="perm-toggle-item">
                    <input type="checkbox" id="permCanRename"> Đổi tên
                </label>
                <label class="perm-toggle-item">
                    <input type="checkbox" id="permCanDelete"> Xóa
                </label>
            </div>
        </div>
        <div style="display:flex; gap:10px; justify-content:flex-end; margin-top:8px;">
            <button class="btn-modal-confirm" onclick="addPermission()" style="font-size:0.82rem; padding:7px 16px;">
                <i class="fas fa-plus"></i> Thêm Quyền
            </button>
        </div>

        <hr class="perm-divider">

        <!-- Current Permissions List -->
        <div class="perm-list-title">Danh sách quyền hiện tại</div>
        <div id="permList">
            <div class="perm-list-empty">Chưa có quyền nào được cấu hình.</div>
        </div>

        <div class="media-modal-actions" style="margin-top:20px;">
            <button class="btn-modal-cancel" onclick="closePermModal()">Đóng</button>
        </div>
    </div>
</div>

<!-- Rename Modal -->
<div class="media-modal-overlay" id="renameModal">
    <div class="media-modal">
        <h3><i class="fas fa-edit" style="color:#fbbf24;"></i> Đổi Tên</h3>
        <input type="text" id="renameInput" placeholder="Tên mới...">
        <div class="media-modal-actions">
            <button class="btn-modal-cancel" onclick="closeRenameModal()">Hủy</button>
            <button class="btn-modal-confirm" onclick="submitRename()">Lưu</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
let currentPath = '';

document.addEventListener('DOMContentLoaded', function() {
    @if($isConfigured)
    browsePath('');
    @endif

    // Drag & drop
    const dropZone = document.getElementById('dropZone');
    document.addEventListener('dragover', function(e) {
        e.preventDefault();
        dropZone.classList.add('active', 'drag-over');
    });
    document.addEventListener('dragleave', function(e) {
        if (!e.relatedTarget || !dropZone.contains(e.relatedTarget)) {
            dropZone.classList.remove('drag-over');
        }
    });
    document.addEventListener('drop', function(e) {
        e.preventDefault();
        dropZone.classList.remove('drag-over');
        if (e.dataTransfer.files.length > 0) {
            uploadFiles(e.dataTransfer.files);
        }
    });
});

function toggleConfig() {
    const section = document.getElementById('configSection');
    section.style.display = section.style.display === 'none' ? '' : 'none';
}

function toggleUploadZone() {
    const zone = document.getElementById('dropZone');
    zone.classList.toggle('active');
}

// ---- Test Connection ----
async function testConnection() {
    const btn = document.getElementById('btnTestConnection');
    const result = document.getElementById('configResult');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang kiểm tra...';

    try {
        const resp = await fetch("{{ route('media.testConnection') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                endpoint: document.getElementById('cfgEndpoint').value,
                access_key: document.getElementById('cfgAccessKey').value,
                secret_key: document.getElementById('cfgSecretKey').value,
                bucket: document.getElementById('cfgBucket').value,
                region: document.getElementById('cfgRegion').value,
            })
        });
        const data = await resp.json();
        result.className = 'config-result ' + (data.success ? 'success' : 'error');
        result.textContent = data.message;

        if (data.success) {
            updateConnectionStatus(true);
        }
    } catch (err) {
        result.className = 'config-result error';
        result.textContent = 'Lỗi kết nối: ' + err.message;
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-plug"></i> Kiểm Tra Kết Nối';
}

// ---- Save Config ----
async function saveConfig() {
    const btn = document.getElementById('btnSaveConfig');
    const result = document.getElementById('configResult');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';

    try {
        const resp = await fetch("{{ route('media.saveConfig') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({
                endpoint: document.getElementById('cfgEndpoint').value,
                access_key: document.getElementById('cfgAccessKey').value,
                secret_key: document.getElementById('cfgSecretKey').value,
                bucket: document.getElementById('cfgBucket').value,
                region: document.getElementById('cfgRegion').value,
            })
        });
        const data = await resp.json();
        result.className = 'config-result ' + (data.success ? 'success' : 'error');
        result.textContent = data.message;

        if (data.success) {
            updateConnectionStatus(true);
            setTimeout(() => browsePath(''), 500);
        }
    } catch (err) {
        result.className = 'config-result error';
        result.textContent = 'Lỗi: ' + err.message;
    }

    btn.disabled = false;
    btn.innerHTML = '<i class="fas fa-save"></i> Lưu Cấu Hình';
}

function updateConnectionStatus(connected) {
    const el = document.getElementById('connectionStatus');
    const text = document.getElementById('statusText');
    el.className = 'connection-status ' + (connected ? 'connected' : 'disconnected');
    text.textContent = connected ? 'Đã kết nối' : 'Chưa kết nối';
}

// ---- File Browser ----
async function browsePath(path) {
    currentPath = path;
    const loading = document.getElementById('browserLoading');
    const grid = document.getElementById('fileGrid');
    const empty = document.getElementById('browserEmpty');

    loading.style.display = 'flex';
    grid.style.display = 'none';
    empty.style.display = 'none';

    try {
        const resp = await fetch("{{ route('media.browse') }}?path=" + encodeURIComponent(path));
        const data = await resp.json();

        if (!data.success) {
            loading.innerHTML = '<i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i> ' + data.message;
            return;
        }

        // Update breadcrumb
        updateBreadcrumb(data.breadcrumb);

        // Update total storage size (only at root)
        if (data.totalStorageSize !== null && data.totalStorageSize !== undefined) {
            const badge = document.getElementById('totalStorageBadge');
            document.getElementById('totalStorageText').textContent = 'Tổng: ' + formatSize(data.totalStorageSize);
            badge.style.display = 'inline-flex';
        }

        // Render items
        const items = [...data.folders, ...data.files];
        if (items.length === 0) {
            loading.style.display = 'none';
            empty.style.display = 'flex';
            return;
        }

        grid.innerHTML = '';
        // Folders first
        data.folders.forEach(folder => {
            grid.innerHTML += renderFolderItem(folder);
        });
        // Then files
        data.files.forEach(file => {
            grid.innerHTML += renderFileItem(file);
        });

        loading.style.display = 'none';
        grid.style.display = 'grid';
    } catch (err) {
        loading.innerHTML = '<i class="fas fa-exclamation-triangle" style="color:#ef4444;"></i> Lỗi tải dữ liệu: ' + err.message;
    }
}

function refreshBrowser() {
    browsePath(currentPath);
}

function updateBreadcrumb(breadcrumb) {
    const el = document.getElementById('breadcrumb');
    el.innerHTML = '';
    breadcrumb.forEach((item, i) => {
        if (i > 0) {
            el.innerHTML += '<span class="breadcrumb-separator"><i class="fas fa-chevron-right"></i></span>';
        }
        const isLast = i === breadcrumb.length - 1;
        const icon = i === 0 ? '<i class="fas fa-home"></i> ' : '<i class="fas fa-folder"></i> ';
        el.innerHTML += `<button class="breadcrumb-item ${isLast ? 'active' : ''}" onclick="browsePath('${item.path}')">${icon}${item.name}</button>`;
    });
}

function renderFolderItem(folder) {
    const permBadge = folder.permCount > 0 
        ? `<div class="perm-badge"><i class="fas fa-shield-alt"></i> ${folder.permCount}</div>` 
        : '';
    const folderSizeStr = folder.size ? formatSize(folder.size) : '';
    return `
    <div class="file-item" ondblclick="browsePath('${folder.path}')">
        ${permBadge}
        <div class="file-item-actions">
            <button class="file-item-perm" onclick="event.stopPropagation(); showPermModal('${folder.path}', '${folder.name}')" title="Phân quyền">
                <i class="fas fa-shield-alt"></i>
            </button>
            <button class="file-item-rename" onclick="event.stopPropagation(); showRenameModal('${folder.path}', '${folder.name}', 'folder')" title="Đổi tên">
                <i class="fas fa-edit"></i>
            </button>
            <button class="file-item-delete" onclick="event.stopPropagation(); deleteItem('${folder.path}', 'folder', '${folder.name}')" title="Xóa">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        <div class="file-item-icon folder"><i class="fas fa-folder"></i></div>
        <div class="file-item-name" title="${folder.name}">${folder.name}</div>
        <div class="file-item-meta">${folderSizeStr ? folderSizeStr : 'Thư mục'}</div>
    </div>`;
}

function renderFileItem(file) {
    const sizeStr = file.size !== null ? formatSize(file.size) : '';
    const dateStr = file.lastModified ? new Date(file.lastModified * 1000).toLocaleDateString('vi-VN') : '';

    let iconClass = 'other';
    let iconHtml = '<i class="fas fa-file"></i>';
    let previewHtml = '';

    if (file.isImage) {
        iconClass = 'image';
        iconHtml = '<i class="fas fa-image"></i>';
        if (file.url) {
            previewHtml = `<img src="${file.url}" class="file-item-preview" alt="${file.name}" onerror="this.style.display='none'">`;
        }
    } else if (file.isVideo) {
        iconClass = 'video';
        iconHtml = '<i class="fas fa-video"></i>';
    } else if (['pdf'].includes(file.extension)) {
        iconClass = 'document';
        iconHtml = '<i class="fas fa-file-pdf"></i>';
    } else if (['doc', 'docx'].includes(file.extension)) {
        iconClass = 'document';
        iconHtml = '<i class="fas fa-file-word"></i>';
    } else if (['xls', 'xlsx'].includes(file.extension)) {
        iconClass = 'document';
        iconHtml = '<i class="fas fa-file-excel"></i>';
    }

    const clickAction = (file.isImage || file.isVideo) && file.url
        ? `onclick="showPreview('${file.url}', '${file.name}', ${file.isVideo}, '${sizeStr}')"` 
        : (file.url ? `onclick="window.open('${file.url}', '_blank')"` : '');

    return `
    <div class="file-item" ${clickAction}>
        <div class="file-item-actions">
            <button class="file-item-rename" onclick="event.stopPropagation(); showRenameModal('${file.path}', '${file.name}', 'file')" title="Đổi tên">
                <i class="fas fa-edit"></i>
            </button>
            <button class="file-item-delete" onclick="event.stopPropagation(); deleteItem('${file.path}', 'file', '${file.name}')" title="Xóa">
                <i class="fas fa-trash"></i>
            </button>
        </div>
        ${previewHtml || `<div class="file-item-icon ${iconClass}">${iconHtml}</div>`}
        <div class="file-item-name" title="${file.name}">${file.name}</div>
        <div class="file-item-meta">${sizeStr}${sizeStr && dateStr ? ' · ' : ''}${dateStr}</div>
    </div>`;
}

function formatSize(bytes) {
    if (bytes === 0) return '0 B';
    const k = 1024;
    const sizes = ['B', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(1)) + ' ' + sizes[i];
}

// ---- Upload ----
async function uploadFiles(fileList) {
    const progressBar = document.getElementById('uploadProgress');
    const progressFill = document.getElementById('uploadProgressFill');
    progressBar.classList.add('active');
    progressFill.style.width = '0%';

    const formData = new FormData();
    formData.append('path', currentPath);
    for (let i = 0; i < fileList.length; i++) {
        formData.append('files[]', fileList[i]);
    }

    try {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', "{{ route('media.upload') }}");
        xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);

        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                const pct = (e.loaded / e.total * 100).toFixed(0);
                progressFill.style.width = pct + '%';
            }
        };

        xhr.onload = function() {
            progressBar.classList.remove('active');
            const data = JSON.parse(xhr.responseText);
            if (data.success) {
                refreshBrowser();
                document.getElementById('dropZone').classList.remove('active');
            } else {
                alert(data.message || 'Lỗi upload');
            }
        };

        xhr.onerror = function() {
            progressBar.classList.remove('active');
            alert('Lỗi kết nối khi upload.');
        };

        xhr.send(formData);
    } catch (err) {
        progressBar.classList.remove('active');
        alert('Lỗi: ' + err.message);
    }

    // Reset file input
    document.getElementById('fileInput').value = '';
}

// ---- Delete ----
async function deleteItem(path, type, name) {
    if (!confirm(`Bạn có chắc muốn xóa "${name}"?`)) return;

    try {
        const resp = await fetch("{{ route('media.delete') }}", {
            method: 'DELETE',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ path: path, type: type })
        });
        const data = await resp.json();
        if (data.success) {
            refreshBrowser();
        } else {
            alert(data.message || 'Lỗi xóa');
        }
    } catch (err) {
        alert('Lỗi: ' + err.message);
    }
}

// ---- New Folder ----
function showNewFolderModal() {
    document.getElementById('newFolderModal').classList.add('show');
    document.getElementById('newFolderName').value = '';
    setTimeout(() => document.getElementById('newFolderName').focus(), 100);
}
function closeNewFolderModal() {
    document.getElementById('newFolderModal').classList.remove('show');
}
async function createFolder() {
    const name = document.getElementById('newFolderName').value.trim();
    if (!name) return alert('Vui lòng nhập tên thư mục.');

    try {
        const resp = await fetch("{{ route('media.createFolder') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ folder_name: name, path: currentPath })
        });
        const data = await resp.json();
        if (data.success) {
            closeNewFolderModal();
            refreshBrowser();
        } else {
            alert(data.message || 'Lỗi tạo thư mục');
        }
    } catch (err) {
        alert('Lỗi: ' + err.message);
    }
}

// ---- Preview ----
function showPreview(url, name, isVideo, sizeStr) {
    const content = document.getElementById('previewContent');
    const info = document.getElementById('previewFileInfo');
    if (isVideo) {
        content.innerHTML = `<video src="${url}" controls autoplay style="max-width:100%; max-height:80vh; border-radius:14px;"></video>`;
    } else {
        content.innerHTML = `<img src="${url}" alt="${name}" style="max-width:100%; max-height:80vh; border-radius:14px;">`;
    }
    info.textContent = name + (sizeStr ? ' · ' + sizeStr : '');
    document.getElementById('previewModal').classList.add('show');
}
function closePreview() {
    document.getElementById('previewModal').classList.remove('show');
    document.getElementById('previewContent').innerHTML = '';
}

// Enter key for new folder
document.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' && document.getElementById('newFolderModal').classList.contains('show')) {
        createFolder();
    }
    if (e.key === 'Escape') {
        closeNewFolderModal();
        closePreview();
        closePermModal();
        closeRenameModal();
    }
    if (e.key === 'Enter' && document.getElementById('renameModal').classList.contains('show')) {
        submitRename();
    }
});

// ============================
// RENAME
// ============================
let renameOldPath = '';
let renameType = 'file';

function showRenameModal(path, name, type) {
    renameOldPath = path;
    renameType = type;
    document.getElementById('renameInput').value = name;
    document.getElementById('renameModal').classList.add('show');
    setTimeout(() => document.getElementById('renameInput').focus(), 100);
}
function closeRenameModal() {
    document.getElementById('renameModal').classList.remove('show');
}
async function submitRename() {
    const newName = document.getElementById('renameInput').value.trim();
    if (!newName) return alert('Vui lòng nhập tên mới.');
    try {
        const resp = await fetch("{{ route('media.rename') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ old_path: renameOldPath, new_name: newName, type: renameType })
        });
        const data = await resp.json();
        if (data.success) {
            closeRenameModal();
            refreshBrowser();
        } else {
            alert(data.message || 'Lỗi đổi tên');
        }
    } catch (err) {
        alert('Lỗi: ' + err.message);
    }
}

// ============================
// FOLDER PERMISSIONS
// ============================
let currentPermFolder = '';

function onPermTypeChange() {
    const type = document.getElementById('permType').value;
    document.getElementById('permUserRow').style.display = type === 'user' ? '' : 'none';
    document.getElementById('permDeptRow').style.display = type === 'department' ? '' : 'none';
}

function showPermModal(folderPath, folderName) {
    currentPermFolder = folderPath;
    document.getElementById('permFolderPath').textContent = '📁 /' + folderPath;
    document.getElementById('permModal').classList.add('show');
    // Reset form
    document.getElementById('permType').value = 'user';
    onPermTypeChange();
    document.getElementById('permUserId').value = '';
    document.getElementById('permDeptId').value = '';
    document.getElementById('permCanView').checked = true;
    document.getElementById('permCanUpload').checked = true;
    document.getElementById('permCanRename').checked = false;
    document.getElementById('permCanDelete').checked = false;
    loadPermissions(folderPath);
}

function closePermModal() {
    document.getElementById('permModal').classList.remove('show');
}

async function loadPermissions(folderPath) {
    const listEl = document.getElementById('permList');
    listEl.innerHTML = '<div class="perm-list-empty"><i class="fas fa-spinner fa-spin"></i> Đang tải...</div>';

    try {
        const resp = await fetch("{{ route('media.permissions') }}?folder_path=" + encodeURIComponent(folderPath));
        const data = await resp.json();
        if (!data.success) {
            listEl.innerHTML = '<div class="perm-list-empty">Lỗi tải dữ liệu</div>';
            return;
        }
        if (data.permissions.length === 0) {
            listEl.innerHTML = '<div class="perm-list-empty">Chưa có quyền nào được cấu hình.</div>';
            return;
        }
        listEl.innerHTML = '';
        data.permissions.forEach(p => {
            const name = p.permission_type === 'user' 
                ? (p.user_name || 'User #' + p.user_id)
                : (p.department_name || 'Phòng #' + p.department_id);
            const typeLabel = p.permission_type === 'user' ? '👤 Nhân viên' : '🏢 Phòng ban';
            const badges = [];
            if (p.can_view) badges.push('<span class="perm-list-badge" style="background:rgba(14,165,233,0.15);color:#38bdf8;">Xem</span>');
            if (p.can_upload) badges.push('<span class="perm-list-badge upload">Upload</span>');
            if (p.can_rename) badges.push('<span class="perm-list-badge rename">Đổi tên</span>');
            if (p.can_delete) badges.push('<span class="perm-list-badge delete">Xóa</span>');

            listEl.innerHTML += `
            <div class="perm-list-item">
                <div class="perm-list-info">
                    <div class="perm-list-name">${name}</div>
                    <div class="perm-list-type">${typeLabel}</div>
                </div>
                <div style="display:flex; align-items:center;">
                    <div class="perm-list-badges">${badges.join('')}</div>
                    <button class="perm-list-remove" onclick="removePermission(${p.id})" title="Xóa quyền">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>`;
        });
    } catch (err) {
        listEl.innerHTML = '<div class="perm-list-empty">Lỗi: ' + err.message + '</div>';
    }
}

async function addPermission() {
    const type = document.getElementById('permType').value;
    const canView = document.getElementById('permCanView').checked;
    const canUpload = document.getElementById('permCanUpload').checked;
    const canRename = document.getElementById('permCanRename').checked;
    const canDelete = document.getElementById('permCanDelete').checked;

    const payload = {
        folder_path: currentPermFolder,
        permission_type: type,
        can_view: canView,
        can_upload: canUpload,
        can_rename: canRename,
        can_delete: canDelete
    };

    if (type === 'user') {
        const userId = document.getElementById('permUserId').value;
        if (!userId) return alert('Vui lòng chọn nhân viên.');
        payload.user_id = parseInt(userId);
    } else {
        const deptId = document.getElementById('permDeptId').value;
        if (!deptId) return alert('Vui lòng chọn phòng ban.');
        payload.department_id = parseInt(deptId);
    }

    try {
        const resp = await fetch("{{ route('media.permissions.store') }}", {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify(payload)
        });
        const data = await resp.json();
        if (data.success) {
            loadPermissions(currentPermFolder);
            // Reset selects
            document.getElementById('permUserId').value = '';
            document.getElementById('permDeptId').value = '';
        } else {
            alert(data.message || 'Lỗi thêm quyền');
        }
    } catch (err) {
        alert('Lỗi: ' + err.message);
    }
}

async function removePermission(id) {
    if (!confirm('Xóa quyền này?')) return;
    try {
        const resp = await fetch(`{{ url('media/permissions') }}/${id}`, {
            method: 'DELETE',
            headers: { 'X-CSRF-TOKEN': csrfToken }
        });
        const data = await resp.json();
        if (data.success) {
            loadPermissions(currentPermFolder);
        } else {
            alert(data.message || 'Lỗi xóa quyền');
        }
    } catch (err) {
        alert('Lỗi: ' + err.message);
    }
}
</script>
@endpush
