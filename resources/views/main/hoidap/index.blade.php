@extends('main.layouts.app')
@section('title', 'Hỏi Đáp / Tư Vấn')

@section('content')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .thread-container {
        padding: 20px;
        background: white;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }
    .thread-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 20px;
    }
    .thread-header h2 {
        font-size: 22px; font-weight: 700; color: #1e293b;
        display: flex; align-items: center; gap: 10px;
    }
    .btn-new-thread {
        padding: 10px 20px; border: none; border-radius: 8px;
        background: linear-gradient(135deg, #6d28d9, #a855f7);
        color: white; font-weight: 600; font-size: 14px; cursor: pointer;
        transition: all 0.3s; display: flex; align-items: center; gap: 8px;
    }
    .btn-new-thread:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(109,40,217,0.3); }

    /* Thread list */
    .thread-list { display: flex; flex-direction: column; gap: 12px; }
    .thread-card {
        background: #fff; border: 1px solid #e2e8f0; border-radius: 10px;
        padding: 18px 22px; cursor: pointer; transition: all 0.2s;
        display: flex; align-items: flex-start; gap: 16px;
    }
    .thread-card:hover { border-color: #a855f7; box-shadow: 0 4px 16px rgba(168,85,247,0.1); transform: translateY(-1px); }
    .thread-card.locked { opacity: 0.75; border-left: 4px solid #ef4444; }
    .thread-avatar {
        width: 44px; height: 44px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 16px;
        background: linear-gradient(135deg, #6d28d9, #a855f7);
    }
    .thread-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
    .thread-info { flex: 1; min-width: 0; }
    .thread-title {
        font-size: 16px; font-weight: 600; color: #1e293b;
        display: flex; align-items: center; gap: 8px;
    }
    .thread-title .lock-badge {
        font-size: 11px; background: #fef2f2; color: #ef4444;
        padding: 2px 8px; border-radius: 10px; font-weight: 500;
    }
    .thread-meta {
        font-size: 13px; color: #94a3b8; margin-top: 4px;
        display: flex; align-items: center; gap: 12px; flex-wrap: wrap;
    }
    .thread-meta .comment-count {
        display: flex; align-items: center; gap: 4px;
        color: #6d28d9; font-weight: 600;
    }
    .thread-actions {
        display: flex; gap: 6px; flex-shrink: 0; align-items: flex-start;
    }
    .thread-actions button {
        border: none; background: none; cursor: pointer; padding: 6px 8px;
        border-radius: 6px; font-size: 14px; transition: background 0.2s;
    }
    .thread-actions button:hover { background: #f1f5f9; }
    .thread-actions .btn-lock { color: #f59e0b; }
    .thread-actions .btn-delete { color: #ef4444; }

    /* Empty state */
    .empty-state {
        text-align: center; padding: 60px 20px; color: #94a3b8;
    }
    .empty-state i { font-size: 48px; margin-bottom: 16px; display: block; }
    .empty-state p { font-size: 16px; }

    /* Modal */
    .modal-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.5); z-index: 9998;
        display: none; align-items: center; justify-content: center;
        backdrop-filter: blur(4px);
    }
    .modal-overlay.show { display: flex; }
    .modal-box {
        background: white; border-radius: 10px;
        max-width: 95vw; max-height: 92vh; overflow-y: auto;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        animation: modalSlideIn 0.3s ease;
    }
    @keyframes modalSlideIn { from { transform: translateY(20px); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
    .modal-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 0 24px; border-bottom: 1px solid #e2e8f0;
    }
    .modal-header h3 { font-size: 18px; font-weight: 700; color: #1e293b; }
    .modal-close {
        border: none; background: #f1f5f9; color: #64748b;
        width: 32px; height: 32px; border-radius: 50%;
        font-size: 16px; cursor: pointer; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center;
    }
    .modal-close:hover { background: #ef4444; color: white; }
    .modal-body { padding: 20px 24px; }

    /* Form controls */
    .form-group { margin-bottom: 16px; }
    .form-group label { font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; display: block; }
    .form-group input[type="text"],
    .form-group textarea {
        width: 100%; padding: 10px 14px; border: 1px solid #e2e8f0;
        border-radius: 8px; font-size: 14px; transition: border 0.2s;
        box-sizing: border-box;
    }
    .form-group input:focus, .form-group textarea:focus { border-color: #a855f7; outline: none; }
    .btn-submit {
        padding: 10px 24px; border: none; border-radius: 8px;
        background: linear-gradient(135deg, #6d28d9, #a855f7);
        color: white; font-weight: 600; cursor: pointer;
        transition: all 0.3s; font-size: 14px;
    }
    .btn-submit:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(109,40,217,0.3); }

    /* File upload preview */
    .upload-zone {
        border: 2px dashed #e2e8f0; border-radius: 8px; padding: 16px;
        text-align: center; cursor: pointer; transition: all 0.2s;
        color: #94a3b8; font-size: 13px;
    }
    .upload-zone:hover { border-color: #a855f7; color: #a855f7; }
    .upload-zone input[type="file"] { display: none; }
    .preview-images {
        display: flex; gap: 8px; flex-wrap: wrap; margin-top: 10px;
    }
    .preview-images .preview-item {
        position: relative; width: 80px; height: 80px; border-radius: 8px; overflow: hidden;
    }
    .preview-images .preview-item img {
        width: 100%; height: 100%; object-fit: cover;
    }
    .preview-images .preview-item .remove-preview {
        position: absolute; top: 2px; right: 2px; background: rgba(239,68,68,0.9);
        color: white; border: none; border-radius: 50%; width: 20px; height: 20px;
        font-size: 11px; cursor: pointer; display: flex; align-items: center; justify-content: center;
    }

    /* Detail view */
    .thread-detail-header {
        padding: 20px 24px; border-bottom: 1px solid #e2e8f0;
    }
    .thread-detail-title {
        font-size: 20px; font-weight: 700; color: #1e293b; margin-bottom: 8px;
        display: flex; align-items: center; gap: 10px;
    }
    .thread-detail-meta {
        font-size: 13px; color: #94a3b8;
        display: flex; align-items: center; gap: 12px;
    }
    .thread-detail-meta .author { color: #6e0000; font-weight: 600; }
    .thread-detail-body {
        padding: 20px 24px; line-height: 1.7; color: #334155; font-size: 14px;
    }
    .thread-detail-body img { max-width: 100%; height: auto; border-radius: 8px; }
    .thread-images {
        display: flex; gap: 8px; flex-wrap: wrap; padding: 0 24px 16px;
    }
    .thread-images img {
        width: 120px; height: 90px; object-fit: cover; border-radius: 8px;
        cursor: pointer; transition: all 0.2s; border: 1px solid #e2e8f0;
    }
    .thread-images img:hover { transform: scale(1.05); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

    /* Comments section */
    .comments-section {
        border-top: 1px solid #e2e8f0;
        padding: 16px 24px;
    }
    .comments-title {
        font-size: 15px; font-weight: 700; color: #1e293b;
        margin-bottom: 16px; display: flex; align-items: center; gap: 8px;
    }
    .comment-item {
        display: flex; gap: 12px; padding: 12px 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .comment-item:last-child { border-bottom: none; }
    .comment-avatar {
        width: 36px; height: 36px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        color: #fff; font-weight: 700; font-size: 13px;
        background: linear-gradient(135deg, #3b82f6, #60a5fa);
    }
    .comment-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
    .comment-content { flex: 1; min-width: 0; }
    .comment-header {
        display: flex; align-items: center; gap: 8px; margin-bottom: 4px;
    }
    .comment-author { font-size: 13px; font-weight: 600; color: #1e293b; }
    .comment-time { font-size: 11px; color: #94a3b8; }
    .comment-body { font-size: 14px; color: #334155; line-height: 1.5; word-wrap: break-word; }
    .comment-images {
        display: flex; gap: 6px; flex-wrap: wrap; margin-top: 8px;
    }
    .comment-images img {
        width: 100px; height: 75px; object-fit: cover; border-radius: 6px;
        cursor: pointer; transition: all 0.2s; border: 1px solid #e2e8f0;
    }
    .comment-images img:hover { transform: scale(1.05); }
    .comment-actions {
        display: flex; gap: 6px; margin-top: 6px;
    }
    .comment-actions button {
        border: none; background: none; cursor: pointer; font-size: 12px;
        color: #94a3b8; padding: 2px 6px; border-radius: 4px;
        transition: all 0.2s;
    }
    .comment-actions button:hover { background: #f1f5f9; }
    .comment-actions .btn-reply:hover { color: #6d28d9; }
    .comment-actions .btn-edit:hover { color: #2563eb; }
    .comment-actions .btn-delete:hover { color: #ef4444; }

    /* Nested replies */
    .comment-replies {
        margin-left: 48px; border-left: 2px solid #e2e8f0; padding-left: 16px;
    }
    .reply-form {
        display: flex; gap: 8px; align-items: flex-start; margin-top: 8px; margin-left: 48px;
    }
    .reply-form textarea {
        flex: 1; padding: 8px 12px; border: 1px solid #e2e8f0;
        border-radius: 8px; font-size: 13px; resize: vertical;
        min-height: 36px; font-family: inherit;
    }
    .reply-form textarea:focus { border-color: #a855f7; outline: none; }
    .reply-form button {
        padding: 6px 14px; border: none; border-radius: 6px;
        font-size: 12px; cursor: pointer; font-weight: 600; flex-shrink: 0;
    }
    .reply-form .btn-send-reply { background: #6d28d9; color: white; }
    .reply-form .btn-cancel-reply { background: #f1f5f9; color: #64748b; }

    /* Comment form */
    .comment-form {
        padding: 16px 0 0;
    }
    .comment-form-row {
        display: flex; gap: 10px; align-items: center;
    }
    .comment-form textarea {
        flex: 1; padding: 10px 14px; border: 1px solid #e2e8f0;
        border-radius: 8px; font-size: 14px; resize: vertical;
        min-height: 44px; max-height: 120px; transition: border 0.2s;
        font-family: inherit;
    }
    .comment-form textarea:focus { border-color: #a855f7; outline: none; }
    .comment-form .btn-send {
        border: none; background: linear-gradient(135deg, #6d28d9, #a855f7);
        color: white; width: 36px; height: 36px; border-radius: 8px;
        cursor: pointer; font-size: 16px; transition: all 0.2s;
        display: flex; align-items: center; justify-content: center; flex-shrink: 0;
    }
    .comment-form .btn-send:hover { transform: scale(1.05); }
    .comment-form .btn-send:disabled { opacity: 0.5; cursor: not-allowed; transform: none; }
    .comment-form-bottom {
        display: flex; align-items: center; gap: 8px; margin-top: 8px;
    }
    .comment-form .btn-attach {
        border: none; background: #f1f5f9; color: #64748b;
        padding: 6px 12px; border-radius: 6px;
        cursor: pointer; font-size: 13px; transition: all 0.2s;
        display: flex; align-items: center; gap: 6px;
    }
    .comment-form .btn-attach:hover { background: #e2e8f0; color: #6d28d9; }

    .locked-notice {
        text-align: center; padding: 16px; color: #ef4444;
        font-size: 14px; font-weight: 500;
        background: #fef2f2; border-radius: 8px; margin-top: 12px;
    }

    /* Edit comment inline */
    .edit-comment-area {
        display: flex; gap: 8px; align-items: flex-start; margin-top: 6px;
    }
    .edit-comment-area textarea {
        flex: 1; padding: 8px 12px; border: 1px solid #a855f7;
        border-radius: 8px; font-size: 13px; resize: vertical;
        min-height: 40px; font-family: inherit;
    }
    .edit-comment-area button {
        padding: 6px 14px; border: none; border-radius: 6px;
        font-size: 12px; cursor: pointer; font-weight: 600;
    }
    .edit-comment-area .btn-save { background: #6d28d9; color: white; }
    .edit-comment-area .btn-cancel { background: #f1f5f9; color: #64748b; }

    /* Image lightbox */
    .lightbox-overlay {
        position: fixed; top: 0; left: 0; right: 0; bottom: 0;
        background: rgba(0,0,0,0.9); z-index: 99999;
        display: none; align-items: center; justify-content: center;
        cursor: pointer;
    }
    .lightbox-overlay.show { display: flex; }
    .lightbox-overlay img {
        max-width: 90vw; max-height: 90vh; border-radius: 8px;
        object-fit: contain;
    }

    @media (max-width: 768px) {
        .thread-header { flex-direction: column; gap: 12px; align-items: stretch; }
        .thread-card { flex-direction: column; gap: 10px; }
        .thread-actions { align-self: flex-end; }
        .modal-box { width: 95vw !important; }
    }
</style>

<div class="thread-container">
    {{-- Header --}}
    <div class="thread-header">
        <h2>❓ Hỏi Đáp / Tư Vấn</h2>
        <button class="btn-new-thread" onclick="openModal('modalCreate')">
            <i class="fa-solid fa-plus"></i> Tạo Bài Viết
        </button>
    </div>

    {{-- Thread list --}}
    <div class="thread-list" id="threadList">
        @forelse($threads as $thread)
        <div class="thread-card {{ $thread->is_locked ? 'locked' : '' }}" onclick="viewThread({{ $thread->id }})" id="thread-{{ $thread->id }}">
            <div class="thread-avatar">
                @if($thread->user_avatar)
                    <img src="/storage/avatars/{{ $thread->user_avatar }}" alt="">
                @else
                    {{ mb_substr($thread->user_name, 0, 1) }}
                @endif
            </div>
            <div class="thread-info">
                <div class="thread-title">
                    {{ $thread->title }}
                    @if($thread->is_locked)
                        <span class="lock-badge">🔒 Đã khóa</span>
                    @endif
                </div>
                <div class="thread-meta">
                    <span>{{ $thread->user_name }}</span>
                    <span>•</span>
                    <span>{{ \Carbon\Carbon::parse($thread->created_at)->diffForHumans() }}</span>
                    <span class="comment-count">
                        <i class="fa-regular fa-comment"></i> {{ $thread->comment_count }}
                    </span>
                </div>
            </div>
            <div class="thread-actions" onclick="event.stopPropagation();">
                @if($thread->user_id == Auth::id() || Auth::user()->can('Admin'))
                    <button class="btn-lock" onclick="toggleLock({{ $thread->id }})" title="{{ $thread->is_locked ? 'Mở khóa' : 'Khóa' }}">
                        <i class="fa-solid {{ $thread->is_locked ? 'fa-lock-open' : 'fa-lock' }}"></i>
                    </button>
                    <button class="btn-delete" onclick="deleteThread({{ $thread->id }}, event)" title="Xóa">
                        <i class="fa-regular fa-trash-can"></i>
                    </button>
                @endif
            </div>
        </div>
        @empty
        <div class="empty-state">
            <i class="fa-regular fa-comments"></i>
            <p>Chưa có câu hỏi nào. Hãy tạo câu hỏi đầu tiên!</p>
        </div>
        @endforelse
    </div>
</div>

{{-- MODAL TẠO BÀI VIẾT --}}
<div class="modal-overlay" id="modalCreate">
    <div class="modal-box" style="width: 700px;">
        <div class="modal-header">
            <h3>✍️ Tạo Câu Hỏi Mới</h3>
            <button class="modal-close" onclick="closeModal('modalCreate')">✕</button>
        </div>
        <div class="modal-body">
            <form method="POST" action="{{ route('hoiDap.store') }}" enctype="multipart/form-data" id="formCreate" onsubmit="return handleCreateSubmit(this)">
                @csrf
                <div class="form-group">
                    <label>Tiêu đề câu hỏi <span style="color:red">*</span></label>
                    <input type="text" name="title" required placeholder="Nhập tiêu đề câu hỏi...">
                </div>
                <div class="form-group">
                    <label>Nội dung <span style="color:red">*</span></label>
                    <textarea name="body" id="threadBody" rows="8" required placeholder="Mô tả chi tiết câu hỏi của bạn..."></textarea>
                </div>
                <div class="form-group">
                    <label>Ảnh đính kèm</label>
                    <div class="upload-zone" onclick="document.getElementById('createImages').click()">
                        <i class="fa-solid fa-cloud-arrow-up" style="font-size:24px; margin-bottom:6px;"></i><br>
                        Click để chọn ảnh (có thể chọn nhiều)
                        <input type="file" id="createImages" name="images[]" multiple accept="image/*" onchange="previewFiles(this, 'createPreview')">
                    </div>
                    <div class="preview-images" id="createPreview"></div>
                </div>
                <div style="text-align:right; padding-top:8px;">
                    <button type="submit" class="btn-submit" id="btnCreateSubmit">
                        <i class="fa-solid fa-paper-plane"></i> Đăng Bài
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- MODAL XEM CHI TIẾT --}}
<div class="modal-overlay" id="modalDetail">
    <div class="modal-box" style="width: 800px;">
        <div class="modal-header">
            <h3 id="detailModalTitle">Chi Tiết Bài Viết</h3>
            <button class="modal-close" onclick="closeModal('modalDetail')">✕</button>
        </div>
        <div class="thread-detail-header">
            <div class="thread-detail-title" id="detailTitle"></div>
            <div class="thread-detail-meta">
                Người tạo chủ đề : <span class="author" id="detailAuthor"></span>
                <span>•</span>
                <span id="detailTime"></span>
            </div>
        </div>
        <div class="thread-detail-body" id="detailBody"></div>
        <div class="thread-images" id="detailImages"></div>

        {{-- Comments --}}
        <div class="comments-section">
            <div class="comments-title">
                <i class="fa-regular fa-comments"></i>
                <span>Bình luận (<span id="commentCount">0</span>)</span>
            </div>
            <div id="commentsList"></div>

            <div id="commentFormWrap"></div>
        </div>
    </div>
</div>

{{-- LIGHTBOX --}}
<div class="lightbox-overlay" id="lightbox" onclick="this.classList.remove('show')">
    <img id="lightboxImg" src="" alt="">
</div>

<meta name="csrf-token" content="{{ csrf_token() }}">
<script>
    const currentUserId = {{ Auth::id() }};
    const isAdmin = {{ Auth::user()->can('Admin') ? 'true' : 'false' }};
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Modal
    let commentPollTimer = null;
    let threadPollTimer = null;
    let lastCommentHash = '';

    function openModal(id) { document.getElementById(id).classList.add('show'); }
    function closeModal(id) {
        document.getElementById(id).classList.remove('show');
        if (id === 'modalDetail') stopCommentPolling();
    }

    function startCommentPolling() {
        stopCommentPolling();
        commentPollTimer = setInterval(() => {
            if (!currentThreadId) return;
            fetch('/hoi-dap/' + currentThreadId, { headers: { 'Accept': 'application/json' } })
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    const newHash = JSON.stringify(data.comments.map(c => c.id + ':' + c.body + ':' + c.updated_at));
                    if (newHash !== lastCommentHash) {
                        lastCommentHash = newHash;
                        document.getElementById('commentCount').textContent = data.comments.length;
                        renderComments(data.comments, data.thread.is_locked);
                    }
                })
                .catch(() => {});
        }, 5000);
    }
    function stopCommentPolling() {
        if (commentPollTimer) { clearInterval(commentPollTimer); commentPollTimer = null; }
        lastCommentHash = '';
    }

    // Thread list auto-refresh every 15s
    function startThreadPolling() {
        threadPollTimer = setInterval(() => {
            fetch(window.location.href, { headers: { 'Accept': 'text/html' } })
                .then(r => r.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newList = doc.getElementById('threadList');
                    const oldList = document.getElementById('threadList');
                    if (newList && oldList && newList.innerHTML !== oldList.innerHTML) {
                        oldList.innerHTML = newList.innerHTML;
                    }
                })
                .catch(() => {});
        }, 15000);
    }
    startThreadPolling();

    // Pause polling when tab is hidden
    document.addEventListener('visibilitychange', () => {
        if (document.hidden) {
            stopCommentPolling();
            if (threadPollTimer) { clearInterval(threadPollTimer); threadPollTimer = null; }
        } else {
            startThreadPolling();
            if (document.getElementById('modalDetail').classList.contains('show')) startCommentPolling();
        }
    });

    // Lightbox
    function showLightbox(src) {
        document.getElementById('lightboxImg').src = src;
        document.getElementById('lightbox').classList.add('show');
    }

    // Preview files
    function previewFiles(input, previewId) {
        const container = document.getElementById(previewId);
        container.innerHTML = '';
        Array.from(input.files).forEach((file, i) => {
            const reader = new FileReader();
            reader.onload = e => {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML = `<img src="${e.target.result}"><button class="remove-preview" onclick="removePreview(this, '${input.id}', ${i})">✕</button>`;
                container.appendChild(div);
            };
            reader.readAsDataURL(file);
        });
    }
    function removePreview(btn, inputId, index) {
        btn.closest('.preview-item').remove();
    }

    // View thread detail
    let currentThreadId = null;

    function viewThread(id) {
        currentThreadId = id;
        fetch('/hoi-dap/' + id, { headers: { 'Accept': 'application/json' } })
            .then(r => r.json())
            .then(data => {
                if (!data.success) { alert(data.message); return; }
                const t = data.thread;
                const comments = data.comments;

                document.getElementById('detailTitle').innerHTML =
                    t.title + (t.is_locked ? ' <span class="lock-badge">🔒 Đã khóa</span>' : '');
                document.getElementById('detailAuthor').textContent = t.user_name;
                document.getElementById('detailTime').textContent =
                    new Date(t.created_at).toLocaleString('vi-VN');
                document.getElementById('detailBody').innerHTML = t.body;

                // Thread images
                const imgContainer = document.getElementById('detailImages');
                imgContainer.innerHTML = '';
                if (t.images && t.images.length > 0) {
                    t.images.forEach(url => {
                        const img = document.createElement('img');
                        img.src = url;
                        img.onclick = () => showLightbox(url);
                        imgContainer.appendChild(img);
                    });
                }

                // Comments
                document.getElementById('commentCount').textContent = comments.length;
                lastCommentHash = JSON.stringify(comments.map(c => c.id + ':' + c.body + ':' + c.updated_at));
                renderComments(comments, t.is_locked);

                openModal('modalDetail');
                startCommentPolling();
            })
            .catch(() => alert('Lỗi tải bài viết'));
    }

    function renderComments(comments, isLocked) {
        const list = document.getElementById('commentsList');
        list.innerHTML = '';

        // Separate root comments and replies
        const rootComments = comments.filter(c => !c.parent_id);
        const replies = comments.filter(c => c.parent_id);
        const replyMap = {};
        replies.forEach(r => {
            if (!replyMap[r.parent_id]) replyMap[r.parent_id] = [];
            replyMap[r.parent_id].push(r);
        });

        rootComments.forEach(c => {
            const el = buildCommentHtml(c, isLocked);
            list.appendChild(el);
            // Render replies
            if (replyMap[c.id]) {
                const repliesDiv = document.createElement('div');
                repliesDiv.className = 'comment-replies';
                replyMap[c.id].forEach(r => {
                    repliesDiv.appendChild(buildCommentHtml(r, isLocked, true));
                });
                list.appendChild(repliesDiv);
            }
        });

        // Comment form or locked notice
        const formWrap = document.getElementById('commentFormWrap');
        if (isLocked) {
            formWrap.innerHTML = '<div class="locked-notice">🔒 Bài viết đã bị khóa. Không thể bình luận thêm.</div>';
        } else {
            formWrap.innerHTML = `
                <div class="comment-form">
                    <div class="comment-form-row">
                        <textarea id="commentInput" placeholder="Viết bình luận..." rows="1"></textarea>
                        <button class="btn-send" id="btnCommentSend" onclick="submitComment()" title="Gửi">
                            <i class="fa-solid fa-paper-plane"></i>
                        </button>
                    </div>
                    <div class="comment-form-bottom">
                        <label class="btn-attach" title="Đính kèm ảnh">
                            <i class="fa-solid fa-image"></i> Đính kèm ảnh
                            <input type="file" id="commentImages" multiple accept="image/*" style="display:none;" onchange="previewFiles(this, 'commentPreview')">
                        </label>
                    </div>
                    <div class="preview-images" id="commentPreview"></div>
                </div>`;
        }
    }

    function buildCommentHtml(c, isLocked, isReply = false) {
        const avatarHtml = c.user_avatar
            ? `<div class="comment-avatar"><img src="/storage/avatars/${c.user_avatar}"></div>`
            : `<div class="comment-avatar">${c.user_name.charAt(0)}</div>`;

        let imagesHtml = '';
        if (c.images && c.images.length > 0) {
            imagesHtml = '<div class="comment-images">' +
                c.images.map(url => `<img src="${url}" onclick="showLightbox('${url}')">`).join('') +
                '</div>';
        }

        let actionsHtml = '';
        const replyBtn = (!isLocked && !isReply) ? `<button class="btn-reply" onclick="replyToComment(${c.id}, '${c.user_name.replace(/'/g, "\\'")}')"><i class="fa-solid fa-reply"></i> Trả lời</button>` : '';

        if (c.user_id == currentUserId) {
            actionsHtml = `
                <div class="comment-actions">
                    ${replyBtn}
                    <button class="btn-edit" onclick="editComment(${c.id}, this)"><i class="fa-solid fa-pen"></i> Sửa</button>
                    <button class="btn-delete" onclick="deleteComment(${c.id}, this)"><i class="fa-solid fa-trash"></i> Xóa</button>
                </div>`;
        } else if (isAdmin) {
            actionsHtml = `
                <div class="comment-actions">
                    ${replyBtn}
                    <button class="btn-delete" onclick="deleteComment(${c.id}, this)"><i class="fa-solid fa-trash"></i> Xóa</button>
                </div>`;
        } else if (!isLocked && !isReply) {
            actionsHtml = `<div class="comment-actions">${replyBtn}</div>`;
        }

        const dt = new Date(c.created_at);
        const div = document.createElement('div');
        div.className = 'comment-item';
        div.id = 'comment-' + c.id;
        div.innerHTML = `
            ${avatarHtml}
            <div class="comment-content">
                <div class="comment-header">
                    <span class="comment-author">${c.user_name}</span>
                    <span class="comment-time">${dt.toLocaleString('vi-VN')}</span>
                </div>
                <div class="comment-body">${c.body.replace(/\n/g, '<br>')}</div>
                ${imagesHtml}
                ${actionsHtml}
            </div>`;
        return div;
    }

    // Reply to comment
    function replyToComment(parentId, userName) {
        // Remove any existing reply form
        document.querySelectorAll('.reply-form').forEach(f => f.remove());

        const commentEl = document.getElementById('comment-' + parentId);
        const form = document.createElement('div');
        form.className = 'reply-form';
        form.innerHTML = `
            <textarea id="replyInput-${parentId}" placeholder="Trả lời ${userName}..." rows="1"></textarea>
            <button class="btn-send-reply" onclick="submitReply(${parentId})">Gửi</button>
            <button class="btn-cancel-reply" onclick="this.closest('.reply-form').remove()">Hủy</button>`;
        commentEl.after(form);
        document.getElementById('replyInput-' + parentId).focus();
    }

    function submitReply(parentId) {
        const body = document.getElementById('replyInput-' + parentId).value.trim();
        if (!body) return;

        const btn = event.target;
        if (btn.disabled) return;
        btn.disabled = true;

        const formData = new FormData();
        formData.append('body', body);
        formData.append('parent_id', parentId);
        formData.append('_token', csrfToken);

        fetch('/hoi-dap/' + currentThreadId + '/comment', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) viewThread(currentThreadId);
            else { alert(data.message || 'Lỗi'); btn.disabled = false; }
        })
        .catch(() => { alert('Lỗi gửi trả lời'); btn.disabled = false; });
    }

    // Prevent double-submit on create form
    function handleCreateSubmit(form) {
        const btn = document.getElementById('btnCreateSubmit');
        if (btn.disabled) return false;
        btn.disabled = true;
        btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang gửi...';
        return true;
    }

    // Submit comment (with double-click protection)
    function submitComment() {
        const body = document.getElementById('commentInput').value.trim();
        if (!body) { alert('Vui lòng nhập nội dung bình luận'); return; }

        const btn = document.getElementById('btnCommentSend');
        if (btn.disabled) return;
        btn.disabled = true;

        const formData = new FormData();
        formData.append('body', body);
        formData.append('_token', csrfToken);

        const fileInput = document.getElementById('commentImages');
        if (fileInput && fileInput.files.length > 0) {
            Array.from(fileInput.files).forEach(f => formData.append('images[]', f));
        }

        fetch('/hoi-dap/' + currentThreadId + '/comment', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: formData,
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                viewThread(currentThreadId); // Reload detail
            } else {
                alert(data.message || 'Lỗi gửi comment');
                btn.disabled = false;
            }
        })
        .catch(() => { alert('Lỗi gửi comment'); btn.disabled = false; });
    }

    // Edit comment
    function editComment(id, btn) {
        const item = document.getElementById('comment-' + id);
        const bodyEl = item.querySelector('.comment-body');
        const originalText = bodyEl.innerText;
        const actionsEl = item.querySelector('.comment-actions');

        actionsEl.style.display = 'none';
        bodyEl.innerHTML = `
            <div class="edit-comment-area">
                <textarea id="editArea-${id}">${originalText}</textarea>
                <button class="btn-save" onclick="saveComment(${id})">Lưu</button>
                <button class="btn-cancel" onclick="viewThread(currentThreadId)">Hủy</button>
            </div>`;
    }

    function saveComment(id) {
        const body = document.getElementById('editArea-' + id).value.trim();
        if (!body) return;

        fetch('/hoi-dap/comment/' + id, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ body }),
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) viewThread(currentThreadId);
            else alert(data.message || 'Lỗi');
        })
        .catch(() => alert('Lỗi sửa comment'));
    }

    // Delete comment
    function deleteComment(id, btn) {
        if (!confirm('Bạn có chắc muốn xóa bình luận này?')) return;

        fetch('/hoi-dap/comment/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) viewThread(currentThreadId);
            else alert(data.message || 'Lỗi');
        })
        .catch(() => alert('Lỗi xóa comment'));
    }

    // Toggle lock
    function toggleLock(id) {
        fetch('/hoi-dap/' + id + '/lock', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert(data.message || 'Lỗi');
            }
        })
        .catch(() => alert('Lỗi'));
    }

    // Delete thread
    function deleteThread(id, event) {
        if (!confirm('Bạn có chắc muốn xóa bài viết này?')) return;

        fetch('/hoi-dap/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const card = event.target.closest('.thread-card');
                if (card) {
                    card.style.transition = 'opacity 0.3s';
                    card.style.opacity = '0';
                    setTimeout(() => card.remove(), 300);
                }
            } else {
                alert(data.message || 'Lỗi');
            }
        })
        .catch(() => alert('Lỗi xóa'));
    }
</script>
@endsection
