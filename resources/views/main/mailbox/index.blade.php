@extends('main.layouts.app')
@section('title', 'Hộp Thư')

@push('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
<style>
    .mail-container {
        padding: 20px;
        background:white;
        border-radius: 10px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    }
    .mail-header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 24px; flex-wrap: wrap; gap: 12px;
    }
    .mail-header h2 {
        font-size: 22px; font-weight: 700; color: #1e293b;
        display: flex; align-items: center; gap: 10px;
    }
    .mail-header h2 .badge {
        background: linear-gradient(135deg, #6d28d9, #a855f7);
        color: #fff; border-radius: 12px; padding: 2px 10px; font-size: 13px; font-weight: 600;
    }
    .btn-compose {
        background: linear-gradient(135deg, #6d28d9, #7c3aed);
        color: #fff; border: none; padding: 10px 24px; border-radius: 12px;
        font-size: 14px; font-weight: 600; cursor: pointer;
        display: flex; align-items: center; gap: 8px;
        transition: all 0.3s; box-shadow: 0 4px 15px rgba(109,40,217,0.3);
    }
    .btn-compose:hover {
        transform: translateY(-2px); box-shadow: 0 6px 20px rgba(109,40,217,0.4);
    }

    /* Tabs */
    .mail-tabs {
        display: flex; gap: 0; margin-bottom: 20px;
        background: #f1f5f9; border-radius: 12px; padding: 4px; width: fit-content;
    }
    .mail-tab {
        padding: 8px 24px; border-radius: 10px; border: none; background: transparent;
        font-size: 14px; font-weight: 600; color: #64748b; cursor: pointer;
        transition: all 0.3s; display: flex; align-items: center; gap: 6px;
    }
    .mail-tab.active {
        background: #fff; color: #6d28d9;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    .mail-tab .tab-badge {
        background: #ef4444; color: #fff; border-radius: 8px;
        padding: 1px 6px; font-size: 11px; min-width: 18px; text-align: center;
    }

    /* Mail list */
    .mail-list { background: #fff; border-radius: 6px; overflow: hidden; box-shadow: 0 4px 20px rgba(0,0,0,0.06); }
    .mail-item {
        display: flex; align-items: center; padding: 14px 30px; gap: 14px;
        border-bottom: 1px solid #f1f5f9; cursor: pointer;
        transition: all 0.2s;
        position: relative;
    }
    .mail-item:last-child { border-bottom: none; }
    .mail-item:hover { background: #f8f5ff; }
    .mail-item.unread { background: #d8ddf0; }
    .mail-item.unread .mail-subject { font-weight: 700; color: #1e293b; }
    .mail-item.unread::before {
        content: ''; width: 8px; height: 8px; border-radius: 50%;
        background: #6d28d9; flex-shrink: 0;
        position: absolute;
        left: 10px;
    }
    .mail-avatar {
        width: 40px; height: 40px; border-radius: 50%; flex-shrink: 0;
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 16px; color: #fff;
        background: linear-gradient(135deg, #6d28d9, #a855f7);
    }
    .mail-avatar img { width: 100%; height: 100%; border-radius: 50%; object-fit: cover; }
    .mail-content { flex: 1; min-width: 0;line-height: 18px; }
    .mail-subject {
        font-size: 16px; font-weight: 500; color: #334155;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .mail-preview {
        font-size: 14px; color: #a60000; margin-top: 2px;font-weight: 600;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
    }
    .mail-meta {
        flex-shrink: 0; text-align: right; display: flex; flex-direction: column; gap: 4px; align-items: flex-end;
    }
    .mail-time { font-size: 12px; color: #94a3b8; white-space: nowrap; }
    .mail-delete {
        background: none; border: none; color: #cbd5e1; cursor: pointer;
        padding: 4px; border-radius: 6px; transition: all 0.2s;
    }
    .mail-delete:hover { color: #ef4444; background: #fef2f2; }

    .mail-empty {
        padding: 60px 20px; text-align: center; color: #94a3b8;
    }
    .mail-empty i { font-size: 48px; margin-bottom: 12px; display: block; }

    /* Modals */
    .modal-overlay {
        display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%;
        background: rgba(0,0,0,0.4); z-index: 9999; align-items: center; justify-content: center;
    }
    .modal-overlay.show { display: flex; }
    .modal-box {
        background: white; border-radius: 6px; max-width: 95vw;
        max-height: 90vh; overflow-y: auto; box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        animation: modalSlideIn 0.3s ease;
    }
    @keyframes modalSlideIn { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
    .modal-header {
        display: flex; justify-content: space-between; align-items: center;
        padding: 16px 24px; border-bottom: 1px solid #f1f5f9;
    }
    .modal-header h3 { font-size: 18px; font-weight: 700; color: #1e293b; margin: 0; }
    .modal-close {
        background: none; border: none; font-size: 20px; color: #94a3b8;
        cursor: pointer; padding: 4px 8px; border-radius: 8px; transition: all 0.2s;
    }
    .modal-close:hover { background: #f1f5f9; color: #334155; }
    .modal-body { padding: 20px 24px; }
    .modal-footer {
        padding: 16px 24px; border-top: 1px solid #f1f5f9;
        display: flex; justify-content: flex-end; gap: 10px;
    }

    .form-group { margin-bottom: 16px; }
    .form-group label { display: block; font-size: 13px; font-weight: 600; color: #475569; margin-bottom: 6px; }
    .form-group input, .form-group select { width: 100%; padding: 10px 14px; border: 2px solid #e2e8f0; border-radius: 10px; font-size: 14px; transition: border-color 0.2s; box-sizing: border-box; }
    .form-group input:focus, .form-group select:focus { outline: none; border-color: #6d28d9; }

    .btn-primary {
        background: linear-gradient(135deg, #6d28d9, #7c3aed);
        color: #fff; border: none; padding: 10px 24px; border-radius: 10px;
        font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .btn-primary:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(109,40,217,0.3); }
    .btn-secondary {
        background: #f1f5f9; color: #475569; border: none; padding: 10px 24px;
        border-radius: 10px; font-size: 14px; font-weight: 600; cursor: pointer; transition: all 0.2s;
    }
    .btn-secondary:hover { background: #e2e8f0; }

    /* Select2 override */
    .select2-container--default .select2-selection--multiple {
        border: 2px solid #e2e8f0 !important; border-radius: 10px !important;
        padding: 4px 8px !important; min-height: 42px !important;
    }
    .select2-container--default .select2-selection--multiple:focus-within,
    .select2-container--default.select2-container--focus .select2-selection--multiple {
        border-color: #6d28d9 !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background: linear-gradient(135deg, #6d28d9, #7c3aed) !important;
        color: #fff !important; border: none !important; border-radius: 6px !important;
        padding: 3px 8px !important; font-size: 13px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: rgba(255,255,255,0.7) !important; margin-right: 4px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover { color: #fff !important; }
    .select2-dropdown { z-index: 99999 !important; border-radius: 10px !important; border: 2px solid #e2e8f0 !important; box-shadow: 0 8px 30px rgba(0,0,0,0.1) !important; }
    .select2-results__option--highlighted { background: #6d28d9 !important; }

    /* TinyMCE in modal */
    .tox-tinymce { border-radius: 10px !important; border-color: #e2e8f0 !important; }
    .tox-tinymce-aux { z-index: 99999 !important; }
    .tox.tox-silver-sink { z-index: 99999 !important; }
    .tox .tox-dialog-wrap { z-index: 99999 !important; }
    .tox .tox-menu { z-index: 99999 !important; }

    /* Read mail view */
    .read-header { padding: 20px 24px; border-bottom: 1px solid #00478e36; display: flex; justify-content: space-between; align-items: flex-start; }
    .read-info { flex: 1; }
    .read-info-row { display: flex; align-items: baseline; padding: 4px 0; font-size: 14px; }
    .read-info-label { font-weight: 500; color: black; min-width: 90px; flex-shrink: 0; }
    .read-info-value { color: #1e293b; font-weight: 500; }
    .read-info-row:first-child .read-info-value { font-size: 18px; font-weight: 700; }
    .read-body { padding:12px 24px; line-height: 1.7; color: #334155; font-size: 14px; }
    .read-body img { max-width: 100%; height: auto; border-radius: 8px; }

    @media (max-width: 768px) {
        .mail-container { padding: 12px; }
        .mail-header { flex-direction: column; align-items: flex-start; }
        .mail-item { padding: 12px 14px; }
        .modal-box { width: 100% !important; border-radius: 16px 16px 0 0; max-height: 95vh; }
    }
</style>
@endpush

@section('content')
<div class="mail-container">
    <div class="mail-header">
        <h2>✉️ Hộp Thư
            @if($unreadCount > 0)
                <span class="badge">{{ $unreadCount }} mới</span>
            @endif
        </h2>
        <button class="btn-compose" onclick="openCompose()">
            <i class="fa-solid fa-pen-to-square"></i> Soạn Thư Mới
        </button>
    </div>

    <div class="mail-tabs">
        <button class="mail-tab active" onclick="switchTab('inbox', this)">
            📥 Hộp Thư Đến
            @if($unreadCount > 0)<span class="tab-badge">{{ $unreadCount }}</span>@endif
        </button>
        <button class="mail-tab" onclick="switchTab('sent', this)">
            📤 Thư Đã Gửi
        </button>
    </div>

    <!-- INBOX -->
    <div class="mail-list" id="inboxList">
        @forelse($inbox as $msg)
        <div class="mail-item {{ !$msg->is_read ? 'unread' : '' }}" onclick="viewMessage({{ $msg->id }})">
            <div class="mail-avatar">
                @if($msg->sender_avatar)
                    <img src="{{ asset('storage/avatars/' . $msg->sender_avatar) }}" alt="">
                @else
                    {{ mb_substr($msg->sender_name, 0, 1) }}
                @endif
            </div>
            <div class="mail-content">
                <div class="mail-subject">{{ $msg->subject }}</div>
                <div class="mail-preview">{{ $msg->sender_name }}</div>
            </div>
            <div class="mail-meta">
                <span class="mail-time">{{ \Carbon\Carbon::parse($msg->created_at)->diffForHumans() }}</span>
                <button class="mail-delete" onclick="event.stopPropagation(); deleteMail({{ $msg->id }}, event)" title="Xóa">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="mail-empty">
            <i class="fa-regular fa-envelope-open"></i>
            <p>Chưa có thư nào</p>
        </div>
        @endforelse
    </div>

    <!-- SENT -->
    <div class="mail-list" id="sentList" style="display:none;">
        @forelse($sent as $msg)
        <div class="mail-item" onclick="viewMessage({{ $msg->id }})">
            <div class="mail-avatar" style="background: linear-gradient(135deg, #059669, #34d399);">
                <i class="fa-solid fa-paper-plane" style="font-size:16px;"></i>
            </div>
            <div class="mail-content">
                <div class="mail-subject">{{ $msg->subject }}</div>
                <div class="mail-preview">{{ $msg->recipients }}</div>
            </div>
            <div class="mail-meta">
                <span class="mail-time">{{ \Carbon\Carbon::parse($msg->created_at)->diffForHumans() }}</span>
                <button class="mail-delete" onclick="event.stopPropagation(); deleteMail({{ $msg->id }}, event)" title="Xóa">
                    <i class="fa-regular fa-trash-can"></i>
                </button>
            </div>
        </div>
        @empty
        <div class="mail-empty">
            <i class="fa-regular fa-paper-plane"></i>
            <p>Chưa gửi thư nào</p>
        </div>
        @endforelse
    </div>
</div>

{{-- ===== MODAL SOẠN THƯ ===== --}}
<div class="modal-overlay" id="modalCompose">
    <div class="modal-box" style="width:800px;">
        <div class="modal-header">
            <h3>✏️ Soạn Thư Mới</h3>
            <button class="modal-close" onclick="closeModal('modalCompose')">✕</button>
        </div>
        <form action="{{ route('mailbox.send') }}" method="POST" id="composeForm">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label>Người Nhận <span style="color:#ef4444">*</span></label>
                    <select name="recipients[]" id="recipientSelect" multiple="multiple" style="width:100%;">
                        @foreach($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label>Tiêu Đề <span style="color:#ef4444">*</span></label>
                    <input type="text" name="subject" id="composeSubject" placeholder="Nhập tiêu đề thư..." required>
                </div>
                <div class="form-group">
                    <label>Nội Dung</label>
                    <textarea id="composeEditor"></textarea>
                    <input type="hidden" name="body" id="composeBody">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-secondary" onclick="closeModal('modalCompose')">Hủy</button>
                <button type="submit" class="btn-primary"><i class="fa-solid fa-paper-plane"></i> Gửi Thư</button>
            </div>
        </form>
    </div>
</div>

{{-- ===== MODAL XEM THƯ ===== --}}
<div class="modal-overlay" id="modalRead">
    <div class="modal-box" style="width:800px;">
        <div class="read-header">
            <div class="read-info">
                <div class="read-info-row">
                    <span class="read-info-label">Tiêu đề:</span>
                    <span class="read-info-value" id="readSubject"></span>
                </div>
                <div class="read-info-row">
                    <span class="read-info-label">Người gửi:</span>
                    <span class="read-info-value" id="readSender" style="color:#6e0000;font-weight:600;"></span>
                </div>
                <div class="read-info-row">
                    <span class="read-info-label">Gửi đến:</span>
                    <span class="read-info-value" id="readRecipients" style="color:#0824e0;font-weight:600;"></span>
                </div>
                <div class="read-info-row">
                    <span class="read-info-label">Lúc:</span>
                    <span class="read-info-value" id="readTime"></span>
                </div>
            </div>
            <button class="modal-close" onclick="closeModal('modalRead')">✕</button>
        </div>
        <div class="read-body" id="readBody"></div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@include('components._tinymce')
<script>
    // Tab switching
    function switchTab(tab, el) {
        document.querySelectorAll('.mail-tab').forEach(t => t.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('inboxList').style.display = tab === 'inbox' ? 'block' : 'none';
        document.getElementById('sentList').style.display = tab === 'sent' ? 'block' : 'none';
    }

    // Compose modal
    function openCompose() {
        document.getElementById('composeSubject').value = '';
        document.getElementById('composeBody').value = '';
        $('#recipientSelect').val(null).trigger('change');

        document.getElementById('modalCompose').classList.add('show');

        setTimeout(function() {
            initTinyMCE('#composeEditor', { height: 300 }).then(function(editors) {
                if (editors && editors[0]) editors[0].setContent('');
            });
        }, 300);
    }

    // Sync TinyMCE before submit
    document.getElementById('composeForm')?.addEventListener('submit', function(e) {
        const html = getTinyMCEContent('#composeEditor');
        document.getElementById('composeBody').value = html;
        if (!html || html === '<p></p>' || html.trim() === '') {
            e.preventDefault();
            alert('Vui lòng nhập nội dung thư');
            return false;
        }
    });

    // View message
    function viewMessage(id) {
        fetch('/mailbox/' + id)
            .then(r => r.json())
            .then(data => {
                if (!data.success) { alert(data.message); return; }
                const msg = data.message;
                document.getElementById('readSubject').textContent = msg.subject;
                document.getElementById('readSender').textContent = msg.sender_name;
                document.getElementById('readBody').innerHTML = msg.body;

                // Recipients
                const names = data.recipients.map(r => r.name).join(', ');
                document.getElementById('readRecipients').textContent = names;

                // Time
                const dt = new Date(msg.created_at);
                document.getElementById('readTime').textContent = dt.toLocaleString('vi-VN');

                document.getElementById('modalRead').classList.add('show');

                // Update unread UI
                const items = document.querySelectorAll('#inboxList .mail-item');
                items.forEach(item => {
                    if (item.getAttribute('onclick')?.includes(id)) {
                        item.classList.remove('unread');
                    }
                });
            })
            .catch(err => {
                console.error(err);
                alert('Lỗi khi tải thư');
            });
    }

    // Delete message
    function deleteMail(id, event) {
        if (!confirm('Bạn có chắc muốn xóa thư này?')) return;
        fetch('/mailbox/' + id, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            }
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                // Remove the mail item from DOM without reloading
                const btn = event.target.closest('.mail-item');
                if (btn) {
                    btn.style.transition = 'opacity 0.3s, max-height 0.3s';
                    btn.style.opacity = '0';
                    setTimeout(() => btn.remove(), 300);
                }
            } else {
                alert(data.message || 'Xóa thất bại');
            }
        })
        .catch(() => alert('Lỗi khi xóa thư'));
    }

    // Modal helpers
    function openModal(id) { document.getElementById(id).classList.add('show'); }
    function closeModal(id) { document.getElementById(id).classList.remove('show'); }
    document.querySelectorAll('.modal-overlay').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) m.classList.remove('show'); });
    });

    // Init Select2
    $(document).ready(function() {
        $('#recipientSelect').select2({
            placeholder: 'Chọn người nhận...',
            allowClear: true,
            width: '100%',
            dropdownParent: $('#modalCompose'),
        });
    });
</script>
@endpush
