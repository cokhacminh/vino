@extends('main.layouts.app')

@section('title', 'Thư Viện Video')

@push('styles')

<style>

/* ============================

   VIDEO GALLERY STYLES

   ============================ */

.vg-container {

    padding: 20px;

    background: white;

    border-radius: 10px;

    box-shadow: 0 4px 12px rgba(0,0,0,0.1);

}



/* Header */

.vg-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    flex-wrap: wrap;

    gap: 12px;

    margin-bottom: 20px;

}

.vg-header h2 {

    font-size: 1.3rem;

    font-weight: 700;

    color: black;

    display: flex;

    align-items: center;

    gap: 10px;

}

.vg-header h2 i { color: black; }

.vg-stats {

    display: flex;

    gap: 16px;

    font-size: 16px;

    color: #94a3b8;

}

.vg-stats span { display: flex; align-items: center; gap: 6px; }

.vg-stats i { color: #260096; }



/* Toolbar */

.vg-toolbar {

    display: flex;

    align-items: center;

    gap: 10px;

    flex-wrap: wrap;

    margin-bottom: 16px;

}

.vg-btn {

    display: flex;

    align-items: center;

    gap: 6px;

    padding: 8px 14px;

    border-radius: 10px;

    border: none;

    cursor: pointer;

    font-size: 0.78rem;

    font-weight: 600;

    transition: all 0.25s;

}

.vg-btn-upload {

    background: linear-gradient(135deg, #6366f1, #8b5cf6);

    color: #fff;

}

.vg-btn-upload:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(139,92,246,0.3); }

.vg-btn-album {

    background: green;

    color: white;

    border: 1px solid rgba(56,189,248,0.2);

}

.vg-btn-album:hover { background:#31d831; }

.vg-btn-view {

    background: rgba(255,255,255,0.05);

    color: #94a3b8;

    border: 1px solid rgba(255,255,255,0.08);

    padding: 5px 8px;

    font-size: 21px;

    border-radius: 6px;

}

.vg-btn-view.active {

    background: rgba(139,92,246,0.15);

    color: #a78bfa;

    border-color: rgba(139,92,246,0.3);

}

.vg-toolbar-right {

    margin-left: auto;

    display: flex;

    gap: 6px;

}



/* Back Button */

.back-btn {

    display: flex;

    align-items: center;

    gap: 6px;

    padding: 7px 14px;

    border-radius: 10px;

    border: 1px solid #f06600;

    background: orange;

    color: #030f49;

    cursor: pointer;

    font-size: 0.78rem;

    font-weight: 600;

    transition: all 0.2s;

    margin-bottom: 12px;

}

.back-btn:hover { background: #f8b788ff}



/* Folder Cards */

.folder-grid {

    display: grid;

    grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));

    gap: 14px;

    margin-bottom: 20px;

}

.folder-card {
    position: relative;
    background: linear-gradient(145deg, #1e1b4b, #312e81);
    border: 1px solid rgba(129,140,248,0.2);
    border-radius: 14px;
    cursor: pointer;
    overflow: hidden;
    transition: all 0.3s;
}
.folder-card:hover {
    transform: translateY(-3px);
    border-color: rgba(129,140,248,0.5);
    box-shadow: 0 6px 24px rgba(99,102,241,0.3);
}
/* Folders with media content */
.folder-card.has-media {
    background: linear-gradient(145deg, #064e3b, #065f46);
    border-color: rgba(52,211,153,0.2);
}
.folder-card.has-media:hover {
    border-color: rgba(52,211,153,0.5);
    box-shadow: 0 6px 24px rgba(16,185,129,0.3);
}

.folder-card-thumb {
    height: 110px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(139,92,246,0.25));
}
.folder-card.has-media .folder-card-thumb {
    background: linear-gradient(135deg, rgba(16,185,129,0.15), rgba(52,211,153,0.25));
}
.folder-card-thumb i {
    font-size: 2.5rem;
    color: rgba(165,143,255,0.7);
}
.folder-card.has-media .folder-card-thumb i {
    color: rgba(52,211,153,0.8);
}

.folder-card-info {

    padding: 10px;

    text-align: center;

}

.folder-card-name {
    font-size: 15px;
    font-weight: 600;
    color: #e0e7ff;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
}
.folder-card.has-media .folder-card-name {
    color: #d1fae5;
}
.folder-card-count {
    font-size: 14px;
    color: #a5b4fc;
    margin-top: 3px;
}
.folder-card.has-media .folder-card-count {
    color: #6ee7b7;
}

.folder-actions {

    position: absolute;

    top: 8px;

    right: 8px;

    display: flex;

    gap: 4px;

    opacity: 0;

    transition: opacity 0.2s;

}

.folder-card:hover .folder-actions { opacity: 1; }

.folder-act-btn {

    width: 28px; height: 28px;

    border-radius: 7px;

    border: none;

    cursor: pointer;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 0.7rem;

    transition: all 0.2s;

}

.folder-act-btn.edit { background: #d97706; color: white; }

.folder-act-btn.edit:hover { background: #b45309; transform: scale(1.1); }

.folder-act-btn.move { background: #0284c7; color: white; }

.folder-act-btn.move:hover { background: #0369a1; transform: scale(1.1); }

.folder-act-btn.del { background: #dc2626; color: white; }

.folder-act-btn.del:hover { background: #b91c1c; transform: scale(1.1); }



/* Video Grid (Thumbnail View) */

.video-grid {

    display: grid;

    grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));

    gap: 16px;

}

.video-card {

    position: relative;

    border-radius: 14px;

    overflow: hidden;

    background: rgba(255,255,255,0.03);

    border: 1px solid rgba(255,255,255,0.06);

    cursor: pointer;

    transition: all 0.3s;

}

.video-card:hover {

    transform: translateY(-3px);

    border-color: rgba(139,92,246,0.3);

    box-shadow: 0 8px 28px rgba(0,0,0,0.3);

}

.video-card-preview {

    position: relative;

    height: 160px;

    background: #0a0a1a;

    display: flex;

    align-items: center;

    justify-content: center;

    overflow: hidden;

}

.video-card-preview video.vid-thumb {

    width: 100%;

    height: 100%;

    object-fit: cover;

    pointer-events: none;

}

.video-card-preview .play-overlay {

    position: absolute;

    top: 0; left: 0; width: 100%; height: 100%;

    display: flex;

    align-items: center;

    justify-content: center;

    background: rgba(0,0,0,0.25);

    transition: background 0.2s;

}

.video-card:hover .play-overlay { background: rgba(0,0,0,0.15); }

.video-card-preview i.play-icon {

    font-size: 2.5rem;

    color: rgba(255,255,255,0.85);

    z-index: 2;

    text-shadow: 0 2px 12px rgba(0,0,0,0.6);

    transition: all 0.2s;

}

.video-card:hover .play-icon { color: #a78bfa; transform: scale(1.15); }

.video-card-ext {

    position: absolute;

    top: 8px;

    left: 8px;

    background: rgba(139,92,246,0.8);

    padding: 2px 8px;

    border-radius: 6px;

    font-size: 0.65rem;

    font-weight: 700;

    color: #fff;

    text-transform: uppercase;

}

.video-card-info {

    padding: 10px 12px;

}

.video-card-name {

    font-size: 15px;

    font-weight: 600;

    color: black;

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;

}

.video-card-meta {

    display: flex;

    align-items: center;

    gap: 8px;

    margin-top: 4px;

    font-size: 0.68rem;

    color: #64748b;

}

.video-card-meta .uploader {

    color: #a40015;

    font-weight: 600;

}

.video-item-actions {

    position: absolute;

    top: 8px;

    right: 8px;

    display: flex;

    gap: 4px;

    opacity: 0;

    transition: opacity 0.2s;

    z-index: 3;

}

.video-card:hover .video-item-actions { opacity: 1; }

.item-act-btn {

    width: 28px; height: 28px;

    border-radius: 7px;

    border: none;

    cursor: pointer;

    display: flex;

    align-items: center;

    justify-content: center;

    font-size: 0.7rem;

    transition: all 0.2s;

}

.item-act-btn.rename-btn { background: #d97706; color: white; }

.item-act-btn.rename-btn:hover { background: #b45309; transform: scale(1.1); }

.item-act-btn.move-btn { background: #0284c7; color: white; }

.item-act-btn.move-btn:hover { background: #0369a1; transform: scale(1.1); }

.item-act-btn.delete-btn { background: #dc2626; color: white; }

.item-act-btn.delete-btn:hover { background: #b91c1c; transform: scale(1.1); }

.item-act-btn.download-btn { background: #16a34a; color: white; text-decoration: none; }

.item-act-btn.download-btn:hover { background: #15803d; transform: scale(1.1); }

.batch-bar-btn.download { background: #0d9488; color: white; }

.batch-bar-btn.download:hover { background: #0f766e; }

/* List View */

.list-view { display: flex; flex-direction: column; gap: 2px; }

.list-header {

    display: grid;

    grid-template-columns: 40px 1fr 100px 120px 100px;

    gap: 8px;

    padding: 8px 12px;

    font-size: 0.72rem;

    color: black;

    font-weight: 600;

    text-transform: uppercase;

    letter-spacing: 0.04em;

    border-bottom: 1px solid rgba(255,255,255,0.06);

}

.list-row {

    display: grid;

    grid-template-columns: 40px 1fr 100px 120px 100px;

    gap: 8px;

    padding: 8px 12px;

    align-items: center;

    border-radius: 8px;

    transition: background 0.15s;

    font-size: 0.78rem;

    color: #94a3b8;

}

.list-row:hover { background: rgba(255,255,255,0.04); }

.list-row.selected { background: rgba(139,92,246,0.12) !important; }

.list-name {

    overflow: hidden;

    text-overflow: ellipsis;

    white-space: nowrap;

    color: #004a1d;

    cursor: pointer;

    font-weight: 600;

}

.list-name:hover { color: red; }

.list-uploader { font-size: 0.68rem; color: #64748b; }

.list-actions-cell {

    display: flex;

    gap: 3px;

    justify-content: flex-end;

}

.list-checkbox {

    width: 18px; height: 18px;

    accent-color: #a78bfa;

    cursor: pointer;

}



/* Video Player Modal */

.vp-overlay {

    position: fixed;

    top: 0; left: 0;

    width: 100%; height: 100%;

    background: rgba(0,0,0,0.92);

    z-index: 999;

    display: none;

    align-items: center;

    justify-content: center;

    flex-direction: column;

}

.vp-overlay.show { display: flex; }

.vp-close {

    position: absolute;

    top: 16px; right: 24px;

    background: rgba(255,255,255,0.1);

    border: none;

    color: #fff;

    font-size: 1.2rem;

    width: 40px; height: 40px;

    border-radius: 12px;

    cursor: pointer;

    z-index: 1001;

}

.vp-close:hover { background: rgba(255,255,255,0.2); }

.vp-video-container {

    max-width: 900px;

    width: 90%;

}

.vp-video-container video {

    width: 100%;

    border-radius: 12px;

    max-height: 75vh;

    background: #000;

}

.vp-title {

    color: #e2e8f0;

    font-size: 0.85rem;

    margin-top: 12px;

    text-align: center;

}



/* Modals */

.g-modal-overlay {

    position: fixed;

    top: 0; left: 0;

    width: 100%; height: 100%;

    background: rgba(0,0,0,0.7);

    backdrop-filter: blur(6px);

    z-index: 1000;

    display: none;

    align-items: center;

    justify-content: center;

}

.g-modal-overlay.show { display: flex; }

.g-modal {

    background: linear-gradient(145deg, #1e1b4b, #0f172a);

    border: 1px solid rgba(139,92,246,0.15);

    border-radius: 16px;

    padding: 24px;

    min-width: 340px;

    max-width: 450px;

    box-shadow: 0 20px 60px rgba(0,0,0,0.5);

}

.g-modal h3 {

    font-size: 1rem;

    color: #e2e8f0;

    margin-bottom: 16px;

    display: flex;

    align-items: center;

    gap: 8px;

}

.g-modal-input {

    width: 100%;

    padding: 10px 14px;

    background: rgba(255,255,255,0.06);

    border: 1px solid rgba(139,92,246,0.2);

    border-radius: 10px;

    color: #e2e8f0;

    font-size: 0.85rem;

    outline: none;

    transition: border 0.2s;

    box-sizing: border-box;

}

.g-modal-input:focus { border-color: #a78bfa; }

.g-modal-actions {

    display: flex;

    gap: 8px;

    justify-content: flex-end;

    margin-top: 16px;

}

.g-btn-cancel {

    padding: 8px 16px;

    border-radius: 8px;

    border: 1px solid rgba(255,255,255,0.1);

    background: transparent;

    color: #94a3b8;

    cursor: pointer;

    font-size: 0.8rem;

}

.g-btn-confirm {

    padding: 8px 16px;

    border-radius: 8px;

    border: none;

    background: linear-gradient(135deg, #6366f1, #8b5cf6);

    color: #fff;

    cursor: pointer;

    font-weight: 600;

    font-size: 0.8rem;

}

.g-btn-confirm:hover { opacity: 0.9; }

.g-btn-danger {

    padding: 8px 16px;

    border-radius: 8px;

    border: none;

    background: linear-gradient(135deg, #dc2626, #ef4444);

    color: #fff;

    cursor: pointer;

    font-weight: 600;

    font-size: 0.8rem;

}



/* Upload Modal */

.upload-zone {

    border: 2px dashed rgba(139,92,246,0.3);

    border-radius: 14px;

    padding: 30px;

    text-align: center;

    cursor: pointer;

    transition: all 0.3s;

    margin-bottom: 12px;

}

.upload-zone:hover, .upload-zone.drag-over {

    border-color: #a78bfa;

    background: rgba(139,92,246,0.06);

}

.upload-zone i { font-size: 2rem; color: #a78bfa; display: block; margin-bottom: 8px; }

.upload-zone p { color: #94a3b8; font-size: 0.82rem; }

.upload-file-list {

    max-height: 150px;

    overflow-y: auto;

    margin-bottom: 12px;

}

.upload-file-item {

    display: flex;

    justify-content: space-between;

    padding: 5px 8px;

    font-size: 0.75rem;

    color: #94a3b8;

    border-bottom: 1px solid rgba(255,255,255,0.04);

}

.upload-progress {

    height: 3px;

    background: rgba(139,92,246,0.15);

    border-radius: 10px;

    overflow: hidden;

    margin-bottom: 12px;

}

.upload-progress-bar {

    height: 100%;

    background: linear-gradient(90deg, #6366f1, #a78bfa);

    width: 0;

    transition: width 0.3s;

}



/* Folder tree picker */

.folder-tree-container {

    max-height: 260px;

    overflow-y: auto;

    background: rgba(255,255,255,0.03);

    border: 1px solid rgba(139,92,246,0.15);

    border-radius: 10px;

    margin: 12px 0;

    padding: 6px 0;

}

.folder-tree-container::-webkit-scrollbar { width: 5px; }

.folder-tree-container::-webkit-scrollbar-thumb { background: rgba(139,92,246,0.3); border-radius: 10px; }

.ft-item {

    display: flex;

    align-items: center;

    gap: 8px;

    padding: 7px 12px;

    cursor: pointer;

    color: #94a3b8;

    font-size: 0.8rem;

    transition: all 0.15s;

    border-left: 3px solid transparent;

}

.ft-item:hover { background: rgba(139,92,246,0.08); color: #e2e8f0; }

.ft-item.selected {

    background: rgba(139,92,246,0.15);

    color: #a78bfa;

    border-left-color: #a78bfa;

    font-weight: 600;

}

.ft-item i { width: 16px; text-align: center; font-size: 0.75rem; }



/* Batch bar */

.batch-bar {

    position: fixed;

    bottom: 24px;

    left: 50%;

    transform: translateX(-50%);

    display: flex;

    align-items: center;

    gap: 12px;

    padding: 10px 20px;

    background: rgba(30, 27, 75, 0.95);

    border: 1px solid rgba(139, 92, 246, 0.3);

    border-radius: 14px;

    backdrop-filter: blur(12px);

    box-shadow: 0 8px 32px rgba(0,0,0,0.4);

    z-index: 100;

    animation: batchBarIn 0.25s ease;

}

@keyframes batchBarIn { from { transform: translateX(-50%) translateY(30px); opacity: 0; } }

.batch-bar-count {

    color: #a78bfa;

    font-weight: 700;

    font-size: 0.85rem;

    min-width: 80px;

}

.batch-bar-btn {

    display: flex;

    align-items: center;

    gap: 6px;

    padding: 7px 14px;

    border-radius: 8px;

    border: none;

    cursor: pointer;

    font-size: 0.78rem;

    font-weight: 600;

    transition: all 0.2s;

}

.batch-bar-btn.move { background: rgba(56,189,248,0.2); color: #38bdf8; }

.batch-bar-btn.move:hover { background: rgba(56,189,248,0.35); }

.batch-bar-btn.del { background: rgba(239,68,68,0.2); color: #ef4444; }

.batch-bar-btn.del:hover { background: rgba(239,68,68,0.35); }

.batch-bar-btn.cancel { background: rgba(148,163,184,0.15); color: #94a3b8; }

.batch-bar-btn.cancel:hover { background: rgba(148,163,184,0.25); }



/* Loading & Empty */

.vg-loading {

    display: flex;

    flex-direction: column;

    align-items: center;

    gap: 12px;

    padding: 60px;

    color: #64748b;

}

.vg-loading i { font-size: 1.5rem; animation: spin 1s linear infinite; }

@keyframes spin { to { transform: rotate(360deg); } }

.vg-empty {

    text-align: center;

    padding: 60px 20px;

    color: #64748b;

}

.vg-empty i { font-size: 2.5rem; display: block; margin-bottom: 12px; color: #334155; }

</style>

@endpush



@section('content')

<div class="vg-container">

    <!-- Header -->

    <div class="vg-header">

        <h2><i class="fas fa-film"></i> Thư Viện Video</h2>

        <div class="vg-stats">

            <span><i class="fas fa-folder"></i> <strong id="statFolders">0</strong> albums</span>

            <span><i class="fas fa-video"></i> <strong id="statVideos">0</strong> videos</span>

        </div>

    </div>



    <!-- Toolbar -->

    <div class="vg-toolbar">

        <button class="vg-btn vg-btn-upload" id="btnUpload" onclick="openUploadModal()" style="display:none;">

            <i class="fas fa-cloud-upload-alt"></i> Upload

        </button>

        <button class="vg-btn vg-btn-album" id="btnCreateAlbum" onclick="openCreateAlbumModal()" style="display:none;">

            <i class="fas fa-folder-plus"></i> Tạo Album

        </button>

        <div class="vg-toolbar-right">

            <button class="vg-btn vg-btn-view active" data-view="thumb" onclick="switchView('thumb')"><i class="fas fa-th"></i></button>

            <button class="vg-btn vg-btn-view" data-view="list" onclick="switchView('list')"><i class="fas fa-list"></i></button>

        </div>

    </div>



    <!-- Content -->

    <div id="galleryContent">

        <div class="vg-loading"><i class="fas fa-spinner"></i><span>Đang tải...</span></div>

    </div>

</div>



<!-- Video Player Modal -->

<div class="vp-overlay" id="videoPlayer">

    <button class="vp-close" onclick="closeVideoPlayer()"><i class="fas fa-times"></i></button>

    <div class="vp-video-container">

        <video id="vpVideo" controls></video>

        <div class="vp-title" id="vpTitle"></div>

    </div>

</div>



<!-- Upload Modal -->

<div class="g-modal-overlay" id="uploadModal">

    <div class="g-modal" style="min-width: 420px;">

        <h3><i class="fas fa-cloud-upload-alt" style="color:#a78bfa;"></i> Upload Video</h3>

        <div class="upload-zone" id="dropZone" onclick="document.getElementById('fileInput').click();">

            <i class="fas fa-film"></i>

            <p>Kéo thả video vào đây hoặc click để chọn</p>

        </div>

        <input type="file" id="fileInput" multiple accept="video/*" style="display:none;" onchange="handleFileSelect(this.files)">

        <div class="upload-file-list" id="uploadFileList"></div>

        <div class="upload-progress" id="uploadProgress" style="display:none;">

            <div class="upload-progress-bar" id="uploadProgressBar"></div>

        </div>

        <div id="uploadProgressText" style="display:none; text-align:center; font-size:0.78rem; color:#a78bfa; margin-bottom:8px; font-weight:600;"></div>

        <div class="g-modal-actions">

            <button class="g-btn-cancel" onclick="closeUploadModal()">Hủy</button>

            <button class="g-btn-confirm" id="btnStartUpload" onclick="startUpload()" disabled>Upload</button>

        </div>

    </div>

</div>



<!-- Rename Modal -->

<div class="g-modal-overlay" id="renameModal">

    <div class="g-modal">

        <h3><i class="fas fa-edit" style="color:#eab308;"></i> Đổi Tên</h3>

        <input type="text" class="g-modal-input" id="renameInput" placeholder="Nhập tên mới...">

        <div class="g-modal-actions">

            <button class="g-btn-cancel" onclick="closeRenameModal()">Hủy</button>

            <button class="g-btn-confirm" onclick="submitRename()">Lưu</button>

        </div>

    </div>

</div>



<!-- Delete Modal -->

<div class="g-modal-overlay" id="deleteModal">

    <div class="g-modal">

        <h3><i class="fas fa-trash" style="color:#ef4444;"></i> Xác Nhận Xóa</h3>

        <p style="color:#94a3b8; font-size:0.85rem;">Bạn có chắc muốn xóa <strong id="deleteItemName" style="color:#e2e8f0;"></strong>?</p>

        <div class="g-modal-actions">

            <button class="g-btn-cancel" onclick="closeDeleteModal()">Hủy</button>

            <button class="g-btn-danger" onclick="submitDelete()">Xóa</button>

        </div>

    </div>

</div>



<!-- Move Modal -->

<div class="g-modal-overlay" id="moveModal">

    <div class="g-modal">

        <h3><i class="fas fa-arrows-alt" style="color:#38bdf8;"></i> Di Chuyển</h3>

        <p style="color:#94a3b8; font-size:0.82rem; margin:8px 0;">Di chuyển <strong id="moveItemName" style="color:#e2e8f0;"></strong> tới:</p>

        <div class="folder-tree-container" id="moveTreeContainer"></div>

        <input type="hidden" id="moveDestValue" value="">

        <div class="g-modal-actions">

            <button class="g-btn-cancel" onclick="closeMoveModal()">Hủy</button>

            <button class="g-btn-confirm" onclick="submitMove()">Di chuyển</button>

        </div>

    </div>

</div>



<!-- Create Album Modal -->

<div class="g-modal-overlay" id="createAlbumModal">

    <div class="g-modal">

        <h3><i class="fas fa-folder-plus" style="color:#38bdf8;"></i> Tạo Album Mới</h3>

        <input type="text" class="g-modal-input" id="albumNameInput" placeholder="Tên album...">

        <div class="g-modal-actions">

            <button class="g-btn-cancel" onclick="closeCreateAlbumModal()">Hủy</button>

            <button class="g-btn-confirm" onclick="submitCreateAlbum()">Tạo</button>

        </div>

    </div>

</div>

@endsection



@push('scripts')

<script>

const csrfToken = '{{ csrf_token() }}';

const basePath = 'Thư Viện Video';



let currentFolder = '';

let galleryVideos = [];

let allFolders = [];

let currentPerms = { can_upload: false, can_rename: false, can_delete: false };

let currentUserId = null;

let isAdmin = false;

let viewMode = 'thumb';

let renameTarget = {};

let deleteTarget = {};

let moveTarget = {};

let uploadFiles = [];

let selectedItems = new Set();



function escapeHtml(s) { const d = document.createElement('div'); d.textContent = s; return d.innerHTML; }

function escapeJs(s) { return String(s).replace(/\\/g,'\\\\').replace(/'/g,"\\'").replace(/"/g,'\\"'); }

function formatSize(b) {

    if (b < 1024) return b + ' B';

    if (b < 1024*1024) return (b/1024).toFixed(1) + ' KB';

    if (b < 1024*1024*1024) return (b/1024/1024).toFixed(1) + ' MB';

    return (b/1024/1024/1024).toFixed(2) + ' GB';

}



// ============================

// LOAD GALLERY

// ============================

async function loadGallery(folder) {

    currentFolder = folder;

    clearSelection();

    const content = document.getElementById('galleryContent');

    content.innerHTML = '<div class="vg-loading"><i class="fas fa-spinner"></i><span>Đang tải...</span></div>';



    try {

        const resp = await fetch("{{ route('media.videoGalleryApi') }}?folder=" + encodeURIComponent(folder));

        const data = await resp.json();



        if (!data.success) {

            content.innerHTML = `<div class="vg-empty"><i class="fas fa-exclamation-triangle"></i><p>${data.message || 'Lỗi tải dữ liệu'}</p></div>`;

            return;

        }



        document.getElementById('statFolders').textContent = data.folders.length;

        document.getElementById('statVideos').textContent = data.videos.length;



        currentPerms = data.permissions || { can_upload: false, can_rename: false, can_delete: false };

        currentUserId = data.current_user_id || null;

        isAdmin = data.is_admin || false;

        document.getElementById('btnUpload').style.display = currentPerms.can_upload ? '' : 'none';

        document.getElementById('btnCreateAlbum').style.display = currentPerms.can_upload ? '' : 'none';



        galleryVideos = data.videos || [];

        allFolders = data.allFolders || [];



        let html = '';



        // Back button

        if (folder) {

            const parentParts = folder.split('/');

            parentParts.pop();

            const parentFolder = parentParts.join('/');

            html += `<button class="back-btn" onclick="loadGallery('${escapeJs(parentFolder)}')"><i class="fas fa-arrow-left"></i> Quay lại</button>`;

        }



        // Album cards

        if (data.folders.length > 0) {

            html += '<div class="folder-grid">';

            data.folders.forEach(f => {

                const fullPath = folder ? basePath + '/' + folder + '/' + f.name : basePath + '/' + f.name;

                let actionsHtml = '<div class="folder-actions">';

                if (isAdmin) {

                    actionsHtml += `<button class="folder-act-btn edit" onclick="event.stopPropagation(); openRenameModal('${escapeJs(fullPath)}', '${escapeJs(f.name)}', 'folder')" title="Đổi tên"><i class="fas fa-edit"></i></button>`;

                }

                if (isAdmin) {

                    actionsHtml += `<button class="folder-act-btn move" onclick="event.stopPropagation(); openMoveModal('${escapeJs(fullPath)}', 'folder', '${escapeJs(f.name)}')" title="Di chuyển"><i class="fas fa-arrows-alt"></i></button>`;

                }

                if (currentPerms.can_delete) {

                    actionsHtml += `<button class="folder-act-btn del" onclick="event.stopPropagation(); openDeleteModal('${escapeJs(fullPath)}', 'folder', '${escapeJs(f.name)}')" title="Xóa"><i class="fas fa-trash"></i></button>`;

                }

                actionsHtml += '</div>';



                const hasMedia = f.count > 0;
                const cardClass = hasMedia ? 'folder-card has-media' : 'folder-card';
                const folderIcon = hasMedia ? 'fas fa-photo-film' : 'fas fa-folder';

                html += `<div class="${cardClass}" onclick="loadGallery('${escapeJs(f.path)}')">

                    ${actionsHtml}

                    <div class="folder-card-thumb"><i class="${folderIcon}"></i></div>

                    <div class="folder-card-info">

                        <div class="folder-card-name">${escapeHtml(f.name)}</div>

                        <div class="folder-card-count">${f.count} video</div>

                    </div>

                </div>`;

            });

            html += '</div>';

        }



        // Videos

        if (galleryVideos.length > 0) {

            if (viewMode === 'thumb') {

                html += renderThumbView();

            } else {

                html += renderListView();

            }

        } else if (data.folders.length === 0) {

            let backBtn = '';

            if (folder) {

                const parentParts = folder.split('/');

                parentParts.pop();

                const parentFolder = parentParts.join('/');

                backBtn = `<button class="back-btn" onclick="loadGallery('${escapeJs(parentFolder)}')" style="margin:12px auto 0;"><i class="fas fa-arrow-left"></i> Quay lại</button>`;

            }

            html += `<div class="vg-empty"><i class="fas fa-film"></i><p>Chưa có video nào</p>${backBtn}</div>`;

        }



        content.innerHTML = html;

    } catch (err) {

        content.innerHTML = `<div class="vg-empty"><i class="fas fa-exclamation-triangle"></i><p>Lỗi: ${err.message}</p></div>`;

    }

}



// ============================

// VIEW RENDERERS

// ============================

function renderThumbView() {

    let html = '<div class="video-grid">';

    galleryVideos.forEach((v, idx) => {

        let actionsHtml = '<div class="video-item-actions">';

        const isOwner = currentUserId && v.uploaded_by_id == currentUserId;

        if (isAdmin || isOwner) {

            actionsHtml += `<button class="item-act-btn rename-btn" onclick="event.stopPropagation(); openRenameModal('${escapeJs(v.path)}', '${escapeJs(v.name)}', 'file')" title="Đổi tên"><i class="fas fa-edit"></i></button>`;

        }

        if (isAdmin) {

            actionsHtml += `<button class="item-act-btn move-btn" onclick="event.stopPropagation(); openMoveModal('${escapeJs(v.path)}', 'file', '${escapeJs(v.name)}')" title="Di chuyển"><i class="fas fa-arrows-alt"></i></button>`;

        }

        if (currentPerms.can_delete || isOwner) {

            actionsHtml += `<button class="item-act-btn delete-btn" onclick="event.stopPropagation(); openDeleteModal('${escapeJs(v.path)}', 'file', '${escapeJs(v.name)}')" title="Xóa"><i class="fas fa-trash"></i></button>`;

        }

        actionsHtml += `<a class="item-act-btn download-btn" href="${escapeHtml(v.url)}" download="${escapeHtml(v.name)}" onclick="event.stopPropagation()" title="Tải về"><i class="fas fa-download"></i></a>`;

        actionsHtml += '</div>';



        html += `<div class="video-card" onclick="openVideoPlayer(${idx})">

            ${actionsHtml}

            <div class="video-card-preview">

                <video class="vid-thumb" src="${escapeHtml(v.url)}#t=1" preload="metadata" muted></video>

                <div class="play-overlay">

                    <span class="video-card-ext">${v.extension}</span>

                    <i class="fas fa-play-circle play-icon"></i>

                </div>

            </div>

            <div class="video-card-info">

                <div class="video-card-name" title="${escapeHtml(v.name)}">${escapeHtml(v.name)}</div>

                <div class="video-card-meta">

                    <span>${v.size ? formatSize(v.size) : ''}</span>

                    ${v.uploaded_by ? '<span class="uploader"><i class="fas fa-user"></i> ' + escapeHtml(v.uploaded_by) + '</span>' : ''}

                </div>

            </div>

        </div>`;

    });

    html += '</div>';

    return html;

}



function renderListView() {

    let html = '<div class="list-view">';

    html += '<div class="list-header"><span><input type="checkbox" class="list-checkbox" id="selectAllCb" onchange="toggleSelectAll(this)"></span><span>Tên</span><span>Kích thước</span><span>Người tải</span><span></span></div>';

    galleryVideos.forEach((v, idx) => {

        let actionsHtml = '';

        const isOwner = currentUserId && v.uploaded_by_id == currentUserId;

        if (isAdmin || isOwner) {

            actionsHtml += `<button class="item-act-btn rename-btn" onclick="event.stopPropagation(); openRenameModal('${escapeJs(v.path)}', '${escapeJs(v.name)}', 'file')" title="Đổi tên"><i class="fas fa-edit"></i></button>`;

        }

        if (isAdmin) {

            actionsHtml += `<button class="item-act-btn move-btn" onclick="event.stopPropagation(); openMoveModal('${escapeJs(v.path)}', 'file', '${escapeJs(v.name)}')" title="Di chuyển"><i class="fas fa-arrows-alt"></i></button>`;

        }

        if (currentPerms.can_delete || isOwner) {

            actionsHtml += `<button class="item-act-btn delete-btn" onclick="event.stopPropagation(); openDeleteModal('${escapeJs(v.path)}', 'file', '${escapeJs(v.name)}')" title="Xóa"><i class="fas fa-trash"></i></button>`;

        }

        actionsHtml += `<a class="item-act-btn download-btn" href="${escapeHtml(v.url)}" download="${escapeHtml(v.name)}" onclick="event.stopPropagation()" title="Tải về"><i class="fas fa-download"></i></a>`;

        html += `

        <div class="list-row" data-idx="${idx}" data-path="${escapeHtml(v.path)}">

            <div><input type="checkbox" class="list-checkbox item-cb" data-idx="${idx}" onclick="event.stopPropagation(); toggleSelect(${idx})"></div>

            <div class="list-name" title="${escapeHtml(v.name)}" onclick="openVideoPlayer(${idx})"><i class="fas fa-film" style="color:#a78bfa; margin-right:6px;"></i>${escapeHtml(v.name)}</div>

            <div class="list-name">${v.size ? formatSize(v.size) : '-'}</div>

            <div class="list-name">${v.uploaded_by ? escapeHtml(v.uploaded_by) : '-'}</div>

            <div class="list-actions-cell">${actionsHtml}</div>

        </div>`;

    });

    html += '</div>';

    return html;

}



// ============================

// VIEW SWITCH

// ============================

function switchView(mode) {

    viewMode = mode;

    document.querySelectorAll('.vg-btn-view').forEach(b => {

        b.classList.toggle('active', b.dataset.view === mode);

    });

    loadGallery(currentFolder);

}



// ============================

// VIDEO PLAYER

// ============================

function openVideoPlayer(idx) {

    const v = galleryVideos[idx];

    if (!v) return;

    document.getElementById('vpVideo').src = v.url;

    document.getElementById('vpTitle').textContent = v.name;

    document.getElementById('videoPlayer').classList.add('show');

}

function closeVideoPlayer() {

    const vid = document.getElementById('vpVideo');

    vid.pause();

    vid.src = '';

    document.getElementById('videoPlayer').classList.remove('show');

}



// ============================

// UPLOAD

// ============================

function openUploadModal() {

    uploadFiles = [];

    document.getElementById('uploadFileList').innerHTML = '';

    document.getElementById('fileInput').value = '';

    document.getElementById('uploadProgress').style.display = 'none';

    document.getElementById('uploadProgressBar').style.width = '0';

    document.getElementById('uploadProgressText').style.display = 'none';

    document.getElementById('uploadProgressText').textContent = '';

    document.getElementById('btnStartUpload').disabled = true;

    document.getElementById('uploadModal').classList.add('show');

}

function closeUploadModal() {

    document.getElementById('uploadModal').classList.remove('show');

    document.getElementById('uploadProgressText').style.display = 'none';

}

function handleFileSelect(files) {

    uploadFiles = Array.from(files);

    const list = document.getElementById('uploadFileList');

    list.innerHTML = uploadFiles.map(f => `<div class="upload-file-item"><span>${escapeHtml(f.name)}</span><span>${formatSize(f.size)}</span></div>`).join('');

    document.getElementById('btnStartUpload').disabled = uploadFiles.length === 0;

}

function startUpload() {

    if (!uploadFiles.length) return;

    const progressEl = document.getElementById('uploadProgress');

    const progressBar = document.getElementById('uploadProgressBar');

    const progressText = document.getElementById('uploadProgressText');

    progressEl.style.display = 'block';

    progressText.style.display = 'block';

    progressBar.style.width = '0%';

    progressText.textContent = '0 MB / 0 MB (0%)';

    document.getElementById('btnStartUpload').disabled = true;



    const formData = new FormData();

    uploadFiles.forEach(f => formData.append('files[]', f));

    formData.append('path', currentFolder ? basePath + '/' + currentFolder : basePath);



    const xhr = new XMLHttpRequest();

    xhr.open('POST', "{{ route('media.upload') }}");

    xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken);



    xhr.upload.onprogress = function(e) {

        if (e.lengthComputable) {

            const pct = Math.round((e.loaded / e.total) * 100);

            const loadedMB = (e.loaded / 1024 / 1024).toFixed(1);

            const totalMB = (e.total / 1024 / 1024).toFixed(1);

            progressBar.style.width = pct + '%';

            progressText.textContent = loadedMB + ' MB / ' + totalMB + ' MB (' + pct + '%)';

        }

    };



    xhr.onload = function() {

        progressBar.style.width = '100%';

        try {

            const data = JSON.parse(xhr.responseText);

            if (data.success) {

                progressText.textContent = 'Upload hoàn tất!';

                setTimeout(() => { closeUploadModal(); loadGallery(currentFolder); }, 600);

            } else {

                alert(data.message || 'Lỗi upload');

                document.getElementById('btnStartUpload').disabled = false;

            }

        } catch (e) {

            alert('Lỗi phản hồi server');

            document.getElementById('btnStartUpload').disabled = false;

        }

    };



    xhr.onerror = function() {

        alert('Lỗi kết nối khi upload');

        document.getElementById('btnStartUpload').disabled = false;

    };



    xhr.send(formData);

}



// Drop zone

const dz = document.getElementById('dropZone');

if (dz) {

    dz.addEventListener('dragover', e => { e.preventDefault(); dz.classList.add('drag-over'); });

    dz.addEventListener('dragleave', () => dz.classList.remove('drag-over'));

    dz.addEventListener('drop', e => {

        e.preventDefault();

        dz.classList.remove('drag-over');

        if (e.dataTransfer.files.length) handleFileSelect(e.dataTransfer.files);

    });

}



// ============================

// MULTI-SELECT / BATCH ACTIONS

// ============================

function toggleSelect(idx) {

    if (selectedItems.has(idx)) selectedItems.delete(idx);

    else selectedItems.add(idx);

    updateBatchBar();

    const row = document.querySelector(`.list-row[data-idx="${idx}"]`);

    if (row) row.classList.toggle('selected', selectedItems.has(idx));

}

function toggleSelectAll(el) {

    const cbs = document.querySelectorAll('.item-cb');

    if (el.checked) {

        galleryVideos.forEach((_, i) => selectedItems.add(i));

        cbs.forEach(c => c.checked = true);

        document.querySelectorAll('.list-row').forEach(r => r.classList.add('selected'));

    } else {

        selectedItems.clear();

        cbs.forEach(c => c.checked = false);

        document.querySelectorAll('.list-row').forEach(r => r.classList.remove('selected'));

    }

    updateBatchBar();

}

function updateBatchBar() {

    let bar = document.getElementById('batchBar');

    if (selectedItems.size > 0) {

        if (!bar) {

            bar = document.createElement('div');

            bar.id = 'batchBar';

            bar.className = 'batch-bar';

            document.body.appendChild(bar);

        }

        let btns = `<span class="batch-bar-count">${selectedItems.size} video đã chọn</span>`;

        btns += `<button class="batch-bar-btn download" onclick="batchDownload()"><i class="fas fa-download"></i> Tải về</button>`;

        if (isAdmin) btns += `<button class="batch-bar-btn move" onclick="batchMove()"><i class="fas fa-arrows-alt"></i> Di chuyển</button>`;

        if (isAdmin) btns += `<button class="batch-bar-btn del" onclick="batchDelete()"><i class="fas fa-trash"></i> Xóa</button>`;

        btns += `<button class="batch-bar-btn cancel" onclick="clearSelection()"><i class="fas fa-times"></i> Bỏ chọn</button>`;

        bar.innerHTML = btns;

    } else if (bar) {

        bar.remove();

    }

}

function clearSelection() {

    selectedItems.clear();

    document.querySelectorAll('.item-cb').forEach(c => c.checked = false);

    document.querySelectorAll('.list-row').forEach(r => r.classList.remove('selected'));

    const sa = document.getElementById('selectAllCb');

    if (sa) sa.checked = false;

    updateBatchBar();

}

async function batchDelete() {

    if (!confirm(`Bạn có chắc muốn xóa ${selectedItems.size} video?`)) return;

    for (const idx of selectedItems) {

        const v = galleryVideos[idx];

        if (!v) continue;

        try {

            await fetch("{{ route('media.delete') }}", {

                method: 'DELETE',

                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },

                body: JSON.stringify({ path: v.path, type: 'file' })

            });

        } catch (e) {}

    }

    clearSelection();

    loadGallery(currentFolder);

}

function batchDownload() {
    for (const idx of selectedItems) {
        const v = galleryVideos[idx];
        if (!v || !v.url) continue;
        const a = document.createElement('a');
        a.href = v.url;
        a.download = v.name || 'video';
        a.style.display = 'none';
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
    }
}

function batchMove() {

    moveTarget = { path: '__batch__', type: 'batch', name: selectedItems.size + ' video' };

    document.getElementById('moveItemName').textContent = selectedItems.size + ' video đã chọn';

    renderFolderTree();

    document.getElementById('moveModal').classList.add('show');

}



// ============================

// MOVE

// ============================

function renderFolderTree() {

    const container = document.getElementById('moveTreeContainer');

    document.getElementById('moveDestValue').value = '';

    let html = '';

    allFolders.forEach(f => {

        const depth = f.path ? f.path.split('/').length - 1 : 0;

        const indent = depth * 18;

        const icon = f.path === '' ? 'fa-home' : 'fa-folder';

        const label = f.path === '' ? 'Thư Viện Video (gốc)' : f.path.split('/').pop();

        html += `<div class="ft-item" data-path="${escapeHtml(f.path)}" onclick="selectTreeFolder(this)" style="padding-left:${12 + indent}px;">

            <i class="fas ${icon}"></i> ${escapeHtml(label)}

        </div>`;

    });

    container.innerHTML = html;

    const first = container.querySelector('.ft-item');

    if (first) selectTreeFolder(first);

}

function selectTreeFolder(el) {

    document.querySelectorAll('#moveTreeContainer .ft-item').forEach(e => e.classList.remove('selected'));

    el.classList.add('selected');

    document.getElementById('moveDestValue').value = el.dataset.path;

}

function openMoveModal(path, type, name) {

    moveTarget = { path, type, name };

    document.getElementById('moveItemName').textContent = name;

    renderFolderTree();

    document.getElementById('moveModal').classList.add('show');

}

function closeMoveModal() {

    document.getElementById('moveModal').classList.remove('show');

}

async function submitMove() {

    const dest = document.getElementById('moveDestValue').value;

    try {

        if (moveTarget.type === 'batch') {

            for (const idx of selectedItems) {

                const v = galleryVideos[idx];

                if (!v) continue;

                await fetch("{{ route('media.move') }}", {

                    method: 'POST',

                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },

                    body: JSON.stringify({ source_path: v.path, dest_folder: dest, type: 'file' })

                });

            }

            clearSelection();

            closeMoveModal();

            loadGallery(currentFolder);

        } else {

            const resp = await fetch("{{ route('media.move') }}", {

                method: 'POST',

                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },

                body: JSON.stringify({ source_path: moveTarget.path, dest_folder: dest, type: moveTarget.type })

            });

            const data = await resp.json();

            if (data.success) {

                closeMoveModal();

                loadGallery(currentFolder);

            } else {

                alert(data.message || 'Lỗi di chuyển');

            }

        }

    } catch (err) {

        alert('Lỗi: ' + err.message);

    }

}



// ============================

// RENAME

// ============================

function openRenameModal(path, name, type) {

    renameTarget = { path, type };

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

            body: JSON.stringify({ old_path: renameTarget.path, new_name: newName, type: renameTarget.type })

        });

        const data = await resp.json();

        if (data.success) { closeRenameModal(); loadGallery(currentFolder); }

        else alert(data.message || 'Lỗi đổi tên');

    } catch (err) { alert('Lỗi: ' + err.message); }

}



// ============================

// DELETE

// ============================

function openDeleteModal(path, type, name) {

    deleteTarget = { path, type, name };

    document.getElementById('deleteItemName').textContent = name;

    document.getElementById('deleteModal').classList.add('show');

}

function closeDeleteModal() {

    document.getElementById('deleteModal').classList.remove('show');

}

async function submitDelete() {

    try {

        const resp = await fetch("{{ route('media.delete') }}", {

            method: 'DELETE',

            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },

            body: JSON.stringify({ path: deleteTarget.path, type: deleteTarget.type })

        });

        const data = await resp.json();

        if (data.success) { closeDeleteModal(); loadGallery(currentFolder); }

        else alert(data.message || 'Lỗi xóa');

    } catch (err) { alert('Lỗi: ' + err.message); }

}



// ============================

// CREATE ALBUM

// ============================

function openCreateAlbumModal() {

    document.getElementById('albumNameInput').value = '';

    document.getElementById('createAlbumModal').classList.add('show');

    setTimeout(() => document.getElementById('albumNameInput').focus(), 100);

}

function closeCreateAlbumModal() {

    document.getElementById('createAlbumModal').classList.remove('show');

}

async function submitCreateAlbum() {

    const name = document.getElementById('albumNameInput').value.trim();

    if (!name) return alert('Vui lòng nhập tên album.');

    try {

        const resp = await fetch("{{ route('media.createFolder') }}", {

            method: 'POST',

            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },

            body: JSON.stringify({

                folder_name: name,

                current_path: currentFolder ? basePath + '/' + currentFolder : basePath

            })

        });

        const data = await resp.json();

        if (data.success) { closeCreateAlbumModal(); loadGallery(currentFolder); }

        else alert(data.message || 'Lỗi tạo album');

    } catch (err) { alert('Lỗi: ' + err.message); }

}



// ============================

// KEYBOARD

// ============================

document.addEventListener('keydown', function(e) {

    const vp = document.getElementById('videoPlayer');

    if (vp.classList.contains('show')) {

        if (e.key === 'Escape') closeVideoPlayer();

        return;

    }

    if (e.key === 'Enter' && document.getElementById('renameModal').classList.contains('show')) {

        submitRename(); return;

    }

    if (e.key === 'Enter' && document.getElementById('createAlbumModal').classList.contains('show')) {

        submitCreateAlbum(); return;

    }

    if (e.key === 'Escape') {

        closeUploadModal();

        closeRenameModal();

        closeDeleteModal();

        closeCreateAlbumModal();

        closeMoveModal();

    }

});



// ============================

// INIT

// ============================

loadGallery('');

</script>

@endpush

