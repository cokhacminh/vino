<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Quick Access</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;min-height:100vh;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);position:relative;overflow-x:hidden}
        .bg-shapes{position:fixed;top:0;left:0;width:100%;height:100%;overflow:hidden;z-index:0}
        .shape{position:absolute;border-radius:50%;background:#fff;opacity:.1}
        .shape-1{width:400px;height:400px;top:-50px;right:-50px;animation:f1 20s ease-in-out infinite}
        .shape-2{width:300px;height:300px;bottom:-30px;left:-30px;animation:f2 15s ease-in-out infinite}
        @keyframes f1{0%,100%{transform:translate(0,0)}50%{transform:translate(-30px,20px)}}
        @keyframes f2{0%,100%{transform:translate(0,0)}50%{transform:translate(20px,-15px)}}

        .container{position:relative;z-index:10;max-width:860px;margin:0 auto;padding:40px 20px}
        .header{text-align:center;margin-bottom:28px}
        .header h1{font-size:28px;font-weight:800;color:#fff;margin-bottom:6px}
        .header p{color:rgba(255,255,255,.7);font-size:14px}

        .search-box{margin-bottom:24px;position:relative}
        .search-box input{width:100%;padding:12px 16px 12px 44px;background:#fff;border:2px solid transparent;border-radius:14px;color:#1e293b;font-size:14px;font-family:'Inter',sans-serif;outline:none;box-shadow:0 4px 16px rgba(0,0,0,.1);transition:border .2s}
        .search-box input:focus{border-color:#667eea}
        .search-box input::placeholder{color:#94a3b8}
        .search-box svg{position:absolute;left:14px;top:50%;transform:translateY(-50%);width:18px;height:18px;color:#94a3b8}

        .user-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:12px}
        .user-card{background:#fff;border:1px solid rgba(255,255,255,.3);border-radius:14px;padding:16px;cursor:pointer;transition:all .3s;display:flex;align-items:center;gap:12px;box-shadow:0 2px 8px rgba(0,0,0,.06)}
        .user-card:hover{transform:translateY(-3px);box-shadow:0 8px 28px rgba(0,0,0,.12)}
        .user-card:active{transform:scale(.98)}

        .user-avatar{width:44px;height:44px;border-radius:50%;overflow:hidden;flex-shrink:0;border:2px solid #e2e8f0}
        .user-avatar img{width:100%;height:100%;object-fit:cover}
        .user-info{min-width:0;flex:1}
        .user-name{font-size:14px;font-weight:600;color:#1e293b;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
        .user-meta{font-size:11px;color:#94a3b8;margin-top:3px;display:flex;align-items:center;gap:6px}
        .badge{padding:2px 8px;border-radius:12px;font-size:10px;font-weight:600}
        .badge-admin{background:#fef2f2;color:#dc2626}
        .badge-ketoan{background:#eff6ff;color:#2563eb}
        .badge-nhanvien{background:#f0fdf4;color:#16a34a}
        .badge-none{background:#f8fafc;color:#94a3b8}

        .back-link{display:inline-flex;align-items:center;gap:6px;color:rgba(255,255,255,.7);font-size:13px;text-decoration:none;margin-bottom:16px;transition:color .2s}
        .back-link:hover{color:#fff}

        .loading-overlay{display:none;position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(255,255,255,.8);z-index:100;align-items:center;justify-content:center;backdrop-filter:blur(4px)}
        .loading-overlay.show{display:flex}
        .spinner{width:40px;height:40px;border:3px solid #e2e8f0;border-top-color:#667eea;border-radius:50%;animation:spin .6s linear infinite}
        @keyframes spin{to{transform:rotate(360deg)}}
    </style>
</head>
<body>
    <div class="bg-shapes"><div class="shape shape-1"></div><div class="shape shape-2"></div></div>
    <div class="container">
        <a href="/login" class="back-link"><svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 12H5M12 19l-7-7 7-7"/></svg> Về trang đăng nhập</a>
        <div class="header">
            <h1>⚡ Đăng Nhập Nhanh</h1>
            <p>Chọn tài khoản để đăng nhập</p>
        </div>
        <div class="search-box">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="M21 21l-4.35-4.35"/></svg>
            <input type="text" id="searchInput" placeholder="Tìm tên hoặc username..." oninput="filterUsers()">
        </div>
        <div class="user-grid" id="userGrid">
            @foreach($users as $u)
            <div class="user-card" onclick="quickLogin({{ $u->id }})" data-name="{{ strtolower($u->name) }}" data-username="{{ strtolower($u->username) }}">
                <div class="user-avatar"><img src="https://ui-avatars.com/api/?name={{ urlencode($u->name) }}&size=44&background=667eea&color=fff&bold=true" alt=""></div>
                <div class="user-info">
                    <div class="user-name">{{ $u->name }}</div>
                    <div class="user-meta">
                        <span>{{ $u->username }}</span>
                        @if(in_array('Admin', $u->permissions ?? []))<span class="badge badge-admin">Admin</span>
                        @elseif(in_array('Kế Toán', $u->permissions ?? []))<span class="badge badge-ketoan">Kế Toán</span>
                        @elseif(in_array('Nhân Viên', $u->permissions ?? []))<span class="badge badge-nhanvien">NV</span>
                        @else<span class="badge badge-none">—</span>@endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    <div class="loading-overlay" id="loadingOverlay"><div class="spinner"></div></div>
    <script>
        function quickLogin(userId){document.getElementById('loadingOverlay').classList.add('show');const f=document.createElement('form');f.method='POST';f.action='/quick-access-x9k';f.innerHTML=`<input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]').content}"><input type="hidden" name="user_id" value="${userId}">`;document.body.appendChild(f);f.submit()}
        function filterUsers(){const q=document.getElementById('searchInput').value.toLowerCase();document.querySelectorAll('.user-card').forEach(c=>{const n=c.dataset.name||'';const u=c.dataset.username||'';c.style.display=(n.includes(q)||u.includes(q))?'':'none'})}
    </script>
</body>
</html>
