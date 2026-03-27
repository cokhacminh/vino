@extends('main.layouts.app')

@section('title', 'Đồng Bộ Database')

@push('styles')
<style>
    .dbsync-wrap {
        max-width: 960px;
        margin: 0 auto;
        padding: 24px 16px;
    }
    .dbsync-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 28px;
    }
    .dbsync-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0;
    }
    .badge-local {
        background: linear-gradient(135deg, #f97316, #ea580c);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 20px;
        letter-spacing: .5px;
        text-transform: uppercase;
        box-shadow: 0 2px 8px rgba(249,115,22,.35);
    }
    .card-sync {
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 2px 16px rgba(0,0,0,.08);
        padding: 28px;
        margin-bottom: 22px;
    }
    .card-sync h2 {
        font-size: 1rem;
        font-weight: 700;
        color: #334155;
        margin: 0 0 18px 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .card-sync h2 i { color: #6d28d9; }
    .form-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 14px;
    }
    .form-grid .span-full { grid-column: 1/-1; }
    .form-group label {
        display: block;
        font-size: 13px;
        font-weight: 600;
        color: #475569;
        margin-bottom: 6px;
    }
    .form-group input {
        width: 100%;
        padding: 9px 13px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color .2s;
        box-sizing: border-box;
        background: #f8fafc;
    }
    .form-group input:focus {
        outline: none;
        border-color: #6d28d9;
        background: #fff;
    }
    .btn-test {
        margin-top: 16px;
        padding: 10px 24px;
        background: linear-gradient(135deg, #6d28d9, #7c3aed);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: opacity .2s, transform .15s;
    }
    .btn-test:hover { opacity: .9; transform: translateY(-1px); }
    .btn-test:disabled { opacity: .6; cursor: not-allowed; }

    #conn-status {
        margin-top: 14px;
        padding: 12px 16px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        display: none;
    }
    #conn-status.success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
    #conn-status.error   { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }

    /* Table list */
    #tables-section { display: none; }
    .table-search-box {
        position: relative;
        margin-bottom: 14px;
    }
    .table-search-box input {
        width: 100%;
        padding: 9px 13px 9px 36px;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        font-size: 14px;
        box-sizing: border-box;
        background: #f8fafc;
    }
    .table-search-box input:focus { outline: none; border-color: #6d28d9; }
    .table-search-box i {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 13px;
    }
    .tables-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 10px;
        max-height: 340px;
        overflow-y: auto;
        padding: 4px 2px;
    }
    .table-item {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 9px 12px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 8px;
        cursor: pointer;
        transition: all .18s;
        font-size: 13px;
        color: #374151;
    }
    .table-item:hover { border-color: #7c3aed; background: #f5f3ff; }
    .table-item.selected { border-color: #7c3aed; background: #ede9fe; color: #5b21b6; font-weight: 600; }
    .table-item input[type=checkbox] { accent-color: #7c3aed; width: 15px; height: 15px; }
    .table-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 16px;
        flex-wrap: wrap;
    }
    .btn-select-all {
        padding: 7px 16px;
        border: 1.5px solid #7c3aed;
        color: #7c3aed;
        background: transparent;
        border-radius: 7px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: all .18s;
    }
    .btn-select-all:hover { background: #ede9fe; }
    .btn-sync {
        padding: 10px 28px;
        background: linear-gradient(135deg, #059669, #10b981);
        color: #fff;
        border: none;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 700;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: opacity .2s, transform .15s;
        box-shadow: 0 2px 10px rgba(16,185,129,.3);
    }
    .btn-sync:hover { opacity: .9; transform: translateY(-1px); }
    .btn-sync:disabled { opacity: .6; cursor: not-allowed; }
    .selected-count {
        font-size: 13px;
        color: #64748b;
        margin-left: auto;
    }

    /* Log */
    #sync-log { display: none; }
    .log-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        gap: 8px;
    }
    .log-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 14px;
        border-radius: 8px;
        font-size: 13px;
    }
    .log-item.ok { background: #dcfce7; color: #15803d; }
    .log-item.err { background: #fee2e2; color: #b91c1c; }
    .log-item i { font-size: 14px; flex-shrink: 0; }
    .log-item .log-table { font-weight: 700; min-width: 120px; }
    .log-item .log-msg { flex: 1; }
    .log-summary {
        margin-top: 12px;
        padding: 11px 14px;
        background: #f1f5f9;
        border-radius: 8px;
        font-size: 13px;
        color: #475569;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="dbsync-wrap">
    <div class="dbsync-header">
        <h1 class="dbsync-title"><i class="fa-solid fa-database" style="color:#6d28d9"></i> Đồng Bộ Database</h1>
        <span class="badge-local"><i class="fa-solid fa-laptop-code"></i> Localhost Only</span>
    </div>

    {{-- Cấu hình kết nối --}}
    <div class="card-sync">
        <h2><i class="fa-solid fa-plug"></i> Kết Nối Remote Database (Hosting)</h2>
        <div class="form-grid">
            <div class="form-group">
                <label>Host</label>
                <input type="text" id="inp-host" placeholder="vd: 103.x.x.x hoặc thuysansg.com" value="{{ $remoteConfig['host'] }}">
            </div>
            <div class="form-group">
                <label>Port</label>
                <input type="number" id="inp-port" placeholder="3306" value="{{ $remoteConfig['port'] }}">
            </div>
            <div class="form-group">
                <label>Database Name</label>
                <input type="text" id="inp-database" placeholder="tên database" value="{{ $remoteConfig['database'] }}">
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" id="inp-username" placeholder="username" value="{{ $remoteConfig['username'] }}">
            </div>
            <div class="form-group span-full">
                <label>Password</label>
                <input type="password" id="inp-password" placeholder="password" value="{{ $remoteConfig['password'] }}">
            </div>
        </div>
        <button class="btn-test" id="btn-test" onclick="testConnection()">
            <i class="fa-solid fa-bolt"></i> Kiểm Tra Kết Nối
        </button>
        <div id="conn-status"></div>
    </div>

    {{-- Chọn bảng --}}
    <div class="card-sync" id="tables-section">
        <h2><i class="fa-solid fa-table-list"></i> Chọn Bảng Để Đồng Bộ</h2>
        <div class="table-search-box">
            <i class="fa-solid fa-magnifying-glass"></i>
            <input type="text" id="table-search" placeholder="Tìm kiếm bảng..." oninput="filterTables()">
        </div>
        <div class="tables-grid" id="tables-grid"></div>
        <div class="table-actions">
            <button class="btn-select-all" onclick="selectAll()">Chọn Tất Cả</button>
            <button class="btn-select-all" onclick="deselectAll()">Bỏ Chọn</button>
            <span class="selected-count" id="selected-count">0 bảng được chọn</span>
            <button class="btn-sync" id="btn-sync" onclick="doSync()">
                <i class="fa-solid fa-rotate"></i> Đồng Bộ Đã Chọn
            </button>
        </div>
    </div>

    {{-- Log kết quả --}}
    <div class="card-sync" id="sync-log">
        <h2><i class="fa-solid fa-list-check"></i> Kết Quả Đồng Bộ</h2>
        <ul class="log-list" id="log-list"></ul>
        <div class="log-summary" id="log-summary"></div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let allTables = [];

function testConnection() {
    const host     = document.getElementById('inp-host').value.trim();
    const port     = document.getElementById('inp-port').value.trim();
    const database = document.getElementById('inp-database').value.trim();
    const username = document.getElementById('inp-username').value.trim();
    const password = document.getElementById('inp-password').value;

    if (!host || !database || !username) {
        showStatus('error', 'Vui lòng điền đầy đủ Host, Database và Username.');
        return;
    }

    const btn = document.getElementById('btn-test');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang kiểm tra...';

    fetch('{{ route("dbSync.testConnection") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ host, port, database, username, password })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            showStatus('success', '<i class="fa-solid fa-circle-check"></i> ' + data.message + ' — Tìm thấy ' + data.tables.length + ' bảng.');
            renderTables(data.tables);
        } else {
            showStatus('error', '<i class="fa-solid fa-circle-xmark"></i> ' + data.message);
            document.getElementById('tables-section').style.display = 'none';
        }
    })
    .catch(err => {
        showStatus('error', 'Lỗi kết nối server: ' + err.message);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-bolt"></i> Kiểm Tra Kết Nối';
    });
}

function showStatus(type, msg) {
    const el = document.getElementById('conn-status');
    el.className = '';
    el.classList.add(type);
    el.innerHTML = msg;
    el.style.display = 'block';
}

function renderTables(tables) {
    allTables = tables;
    const grid = document.getElementById('tables-grid');
    grid.innerHTML = '';
    tables.forEach(t => {
        const item = document.createElement('label');
        item.className = 'table-item';
        item.setAttribute('data-table', t.toLowerCase());
        item.innerHTML = `<input type="checkbox" class="tbl-cb" value="${t}" onchange="updateCount()">
            <i class="fa-solid fa-table" style="color:#94a3b8;font-size:12px"></i> ${t}`;
        item.querySelector('input').addEventListener('change', function() {
            item.classList.toggle('selected', this.checked);
        });
        grid.appendChild(item);
    });
    document.getElementById('tables-section').style.display = 'block';
    document.getElementById('sync-log').style.display = 'none';
    updateCount();
}

function filterTables() {
    const q = document.getElementById('table-search').value.toLowerCase();
    document.querySelectorAll('.table-item').forEach(el => {
        el.style.display = el.getAttribute('data-table').includes(q) ? '' : 'none';
    });
}

function updateCount() {
    const cnt = document.querySelectorAll('.tbl-cb:checked').length;
    document.getElementById('selected-count').textContent = cnt + ' bảng được chọn';
}

function selectAll() {
    document.querySelectorAll('.tbl-cb').forEach(cb => {
        if (cb.closest('.table-item').style.display !== 'none') {
            cb.checked = true;
            cb.closest('.table-item').classList.add('selected');
        }
    });
    updateCount();
}

function deselectAll() {
    document.querySelectorAll('.tbl-cb').forEach(cb => {
        cb.checked = false;
        cb.closest('.table-item').classList.remove('selected');
    });
    updateCount();
}

function doSync() {
    const selected = [...document.querySelectorAll('.tbl-cb:checked')].map(cb => cb.value);
    if (selected.length === 0) {
        alert('Vui lòng chọn ít nhất 1 bảng để đồng bộ.');
        return;
    }
    if (!confirm(`Bạn sắp đồng bộ ${selected.length} bảng từ remote về local.\nDữ liệu local trong các bảng này sẽ bị GHI ĐÈ hoàn toàn.\n\nBạn chắc chắn?`)) return;

    const btn = document.getElementById('btn-sync');
    btn.disabled = true;
    btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Đang Đồng Bộ...';

    const logSection = document.getElementById('sync-log');
    const logList = document.getElementById('log-list');
    logList.innerHTML = '<li class="log-item ok" style="background:#f1f5f9;color:#475569"><i class="fa-solid fa-spinner fa-spin"></i> Đang xử lý...</li>';
    logSection.style.display = 'block';
    logSection.scrollIntoView({ behavior: 'smooth', block: 'start' });

    fetch('{{ route("dbSync.sync") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ tables: selected })
    })
    .then(r => r.json())
    .then(data => {
        logList.innerHTML = '';
        if (!data.success) {
            logList.innerHTML = `<li class="log-item err"><i class="fa-solid fa-circle-xmark"></i><span class="log-msg">${data.message}</span></li>`;
            return;
        }
        let ok = 0, fail = 0;
        data.results.forEach(r => {
            const cls = r.success ? 'ok' : 'err';
            const icon = r.success ? 'fa-circle-check' : 'fa-circle-xmark';
            const li = document.createElement('li');
            li.className = `log-item ${cls}`;
            li.innerHTML = `<i class="fa-solid ${icon}"></i><span class="log-table">${r.table}</span><span class="log-msg">${r.message}</span>`;
            logList.appendChild(li);
            r.success ? ok++ : fail++;
        });
        document.getElementById('log-summary').innerHTML =
            `✅ Thành công: ${ok} bảng &nbsp;|&nbsp; ❌ Thất bại: ${fail} bảng`;
    })
    .catch(err => {
        logList.innerHTML = `<li class="log-item err"><i class="fa-solid fa-circle-xmark"></i><span class="log-msg">Lỗi server: ${err.message}</span></li>`;
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="fa-solid fa-rotate"></i> Đồng Bộ Đã Chọn';
    });
}
</script>
@endpush
