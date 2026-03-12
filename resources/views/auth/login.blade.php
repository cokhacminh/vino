<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ĐĂNG NHẬP - VINO CRM</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        *{margin:0;padding:0;box-sizing:border-box}
        body{font-family:'Inter',sans-serif;min-height:100vh;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative}
        .bg-shapes{position:fixed;top:0;left:0;width:100%;height:100%;overflow:hidden;z-index:0}
        .shape{position:absolute;border-radius:50%;opacity:.15}
        .shape-1{width:500px;height:500px;background:#fff;top:-100px;right:-100px;animation:float1 20s ease-in-out infinite}
        .shape-2{width:400px;height:400px;background:#fff;bottom:-80px;left:-80px;animation:float2 15s ease-in-out infinite}
        .shape-3{width:200px;height:200px;background:#fff;top:40%;left:60%;animation:float3 18s ease-in-out infinite}
        @keyframes float1{0%,100%{transform:translate(0,0) rotate(0)}50%{transform:translate(-40px,30px) rotate(45deg)}}
        @keyframes float2{0%,100%{transform:translate(0,0) rotate(0)}50%{transform:translate(30px,-20px) rotate(-30deg)}}
        @keyframes float3{0%,100%{transform:translate(0,0)}50%{transform:translate(-30px,40px)}}

        .login-card{position:relative;z-index:10;background:#fff;border-radius:24px;padding:48px 40px;width:100%;max-width:420px;box-shadow:0 25px 60px rgba(0,0,0,.15);animation:cardIn .6s ease}
        @keyframes cardIn{from{opacity:0;transform:translateY(30px) scale(.95)}to{opacity:1;transform:none}}
        .login-logo{text-align:center;margin-bottom:32px}
        .login-logo .icon{width:64px;height:64px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:16px;display:inline-flex;align-items:center;justify-content:center;margin-bottom:12px;box-shadow:0 8px 24px rgba(102,126,234,.3)}
        .login-logo .icon i{font-size:28px;color:#fff}
        .login-logo h1{font-size:26px;font-weight:800;color:#1e293b}
        .login-logo p{font-size:14px;color:#94a3b8;margin-top:4px}
        .form-group{margin-bottom:20px}
        .form-group label{display:block;font-size:13px;font-weight:600;color:#475569;margin-bottom:6px}
        .input-wrap{position:relative}
        .input-wrap i{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:#94a3b8;font-size:16px}
        .input-wrap input{width:100%;padding:12px 14px 12px 44px;border:2px solid #e2e8f0;border-radius:12px;font-size:14px;font-family:'Inter',sans-serif;color:#1e293b;outline:none;transition:all .2s;background:#f8fafc}
        .input-wrap input:focus{border-color:#667eea;background:#fff;box-shadow:0 0 0 4px rgba(102,126,234,.1)}
        .input-wrap input::placeholder{color:#cbd5e1}
        .toggle-pw{position:absolute;right:14px;top:50%;transform:translateY(-50%);background:none;border:none;color:#94a3b8;cursor:pointer;font-size:16px}
        .toggle-pw:hover{color:#667eea}
        .remember{display:flex;align-items:center;gap:8px;margin-bottom:24px}
        .remember input{width:16px;height:16px;accent-color:#667eea}
        .remember label{font-size:13px;color:#64748b}
        .btn-login{width:100%;padding:14px;background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;border:none;border-radius:12px;font-size:15px;font-weight:700;font-family:'Inter',sans-serif;cursor:pointer;transition:all .3s;box-shadow:0 4px 16px rgba(102,126,234,.3);display:flex;align-items:center;justify-content:center;gap:8px}
        .btn-login:hover{transform:translateY(-2px);box-shadow:0 8px 28px rgba(102,126,234,.4)}
        .btn-login:active{transform:translateY(0)}
        .error-msg{background:#fef2f2;border:1px solid #fecaca;color:#dc2626;padding:10px 14px;border-radius:10px;font-size:13px;margin-bottom:16px;display:flex;align-items:center;gap:8px}
        .footer{text-align:center;margin-top:24px;color:#94a3b8;font-size:12px}
    </style>
</head>
<body>
    <div class="bg-shapes">
        <div class="shape shape-1"></div>
        <div class="shape shape-2"></div>
        <div class="shape shape-3"></div>
    </div>
    <div class="login-card">
        <div class="login-logo">
            <div class="icon"><i class="fa-solid fa-chart-column"></i></div>
            <h1>VINO CRM</h1>
            <p>Đăng nhập để tiếp tục</p>
        </div>
        @if($errors->any())
        <div class="error-msg"><i class="fa-solid fa-circle-exclamation"></i> {{ $errors->first() }}</div>
        @endif
        <form method="POST" action="{{ route('login') }}">
            @csrf
            <div class="form-group">
                <label>Tên đăng nhập</label>
                <div class="input-wrap">
                    <i class="fa-regular fa-user"></i>
                    <input type="text" name="username" value="{{ old('username') }}" placeholder="Nhập tên đăng nhập..." required autofocus>
                </div>
            </div>
            <div class="form-group">
                <label>Mật khẩu</label>
                <div class="input-wrap">
                    <i class="fa-solid fa-lock"></i>
                    <input type="password" name="password" id="pw" placeholder="Nhập mật khẩu..." required>
                    <button type="button" class="toggle-pw" onclick="const p=document.getElementById('pw');p.type=p.type==='password'?'text':'password';this.querySelector('i').className=p.type==='password'?'fa-regular fa-eye':'fa-regular fa-eye-slash'"><i class="fa-regular fa-eye"></i></button>
                </div>
            </div>
            <div class="remember">
                <input type="checkbox" name="remember" id="remember" checked>
                <label for="remember">Ghi nhớ đăng nhập</label>
            </div>
            <button type="submit" class="btn-login"><i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập</button>
        </form>
        <div class="footer">© 2026 VINO CRM. All rights reserved.</div>
    </div>
</body>
</html>
