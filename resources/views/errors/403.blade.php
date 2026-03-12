<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>403 - Không Có Quyền Truy Cập</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 50%, #0f172a 100%);
            color: #e2e8f0;
            overflow: hidden;
        }

        /* Animated background particles */
        .bg-particles {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            z-index: 0;
            pointer-events: none;
        }
        .bg-particles::before,
        .bg-particles::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        .bg-particles::before {
            width: 300px; height: 300px;
            background: radial-gradient(circle, rgba(239, 68, 68, 0.08) 0%, transparent 70%);
            top: 10%; left: 15%;
        }
        .bg-particles::after {
            width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.06) 0%, transparent 70%);
            bottom: 10%; right: 10%;
            animation-delay: -3s;
        }
        @keyframes float {
            0%, 100% { transform: translateY(0) scale(1); }
            50% { transform: translateY(-20px) scale(1.05); }
        }

        .container {
            position: relative;
            z-index: 1;
            text-align: center;
            padding: 40px;
            max-width: 520px;
        }

        /* Lock icon */
        .lock-icon {
            width: 120px; height: 120px;
            margin: 0 auto 32px;
            position: relative;
            animation: pulse-glow 2s ease-in-out infinite;
        }
        .lock-icon svg {
            width: 100%; height: 100%;
            filter: drop-shadow(0 0 20px rgba(239, 68, 68, 0.3));
        }
        @keyframes pulse-glow {
            0%, 100% { transform: scale(1); filter: drop-shadow(0 0 15px rgba(239, 68, 68, 0.2)); }
            50% { transform: scale(1.05); filter: drop-shadow(0 0 25px rgba(239, 68, 68, 0.4)); }
        }

        /* Error code */
        .error-code {
            font-size: 80px;
            font-weight: 800;
            line-height: 1;
            margin-bottom: 8px;
            background: linear-gradient(135deg, #ef4444, #f59e0b);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: -2px;
        }

        .error-title {
            font-size: 22px;
            font-weight: 700;
            color: #f1f5f9;
            margin-bottom: 16px;
        }

        .error-message {
            font-size: 15px;
            color: #94a3b8;
            line-height: 1.7;
            margin-bottom: 32px;
            max-width: 400px;
            margin-left: auto;
            margin-right: auto;
        }

        .error-detail {
            display: inline-block;
            padding: 10px 20px;
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            border-radius: 10px;
            font-size: 13px;
            color: #fca5a5;
            margin-bottom: 32px;
            max-width: 100%;
            word-break: break-word;
        }

        .actions {
            display: flex;
            gap: 12px;
            justify-content: center;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 12px 28px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.25s ease;
            cursor: pointer;
            border: none;
        }

        .btn-primary {
            background: linear-gradient(135deg, #3b82f6, #2563eb);
            color: white;
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
        }
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, 0.05);
            color: #94a3b8;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.1);
            color: #e2e8f0;
            transform: translateY(-2px);
        }

        /* User info */
        .user-info {
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid rgba(255, 255, 255, 0.06);
            font-size: 12px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="bg-particles"></div>

    <div class="container">
        <div class="lock-icon">
            <svg viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                <path d="M12 2C9.24 2 7 4.24 7 7V10H6C4.9 10 4 10.9 4 12V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V12C20 10.9 19.1 10 18 10H17V7C17 4.24 14.76 2 12 2ZM12 4C13.66 4 15 5.34 15 7V10H9V7C9 5.34 10.34 4 12 4Z" fill="url(#lockGradient)"/>
                <circle cx="12" cy="16" r="1.5" fill="#1e293b"/>
                <defs>
                    <linearGradient id="lockGradient" x1="4" y1="2" x2="20" y2="22" gradientUnits="userSpaceOnUse">
                        <stop stop-color="#ef4444"/>
                        <stop offset="1" stop-color="#f59e0b"/>
                    </linearGradient>
                </defs>
            </svg>
        </div>

        <div class="error-code">403</div>
        <h1 class="error-title">Không Có Quyền Truy Cập</h1>
        <p class="error-message">
            Bạn không có quyền hạn để truy cập trang này. 
            Vui lòng liên hệ quản trị viên nếu bạn cho rằng đây là lỗi.
        </p>

        @if($exception->getMessage() && $exception->getMessage() !== 'Forbidden')
        <div class="error-detail">
            ⚠️ {{ $exception->getMessage() }}
        </div>
        @endif

        <div class="actions">
            <a href="{{ url()->previous() }}" class="btn btn-primary">
                ← Quay Lại
            </a>
            <a href="{{ route('dashboard') }}" class="btn btn-secondary">
                🏠 Trang Chủ
            </a>
        </div>

        @auth
        <div class="user-info">
            Đang đăng nhập: <strong>{{ Auth::user()->name }}</strong>
        </div>
        @endauth
    </div>
</body>
</html>
