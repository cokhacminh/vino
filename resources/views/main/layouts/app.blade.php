<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Main Panel') - VINO CRM</title>
    <link rel="stylesheet" href="{{ asset('css/main/main.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @stack('styles')
</head>
<body>
    <div class="maincp-container">
        <div class="maincp-body">
            <!-- Animated Background Particles -->
            <div class="sidebar-particles"></div>

            <aside class="sidebar modern-sidebar">
                <!-- Sidebar Header -->
                <div class="sidebar-header">
                    <div class="sidebar-user-greeting">
                        <div class="greeting-avatar">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&size=44&background=6d28d9&color=fff&bold=true" alt="Avatar" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">
                        </div>
                        <div class="greeting-info">
                            <span class="greeting-label">Xin chào</span>
                            <span class="greeting-name">{{ Auth::user()->name ?? 'User' }}</span>
                        </div>
                    </div>
                </div>

                <nav class="sidebar-nav">
                    <!-- 1. Dashboard -->
                    <a href="{{ route('dashboard') }}" class="nav-item ripple-effect {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <span class="nav-icon floating">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </span>
                        <span class="nav-text">Thống Kê Chung</span>
                        <span class="nav-glow"></span>
                    </a>

                    <div class="nav-divider"></div>

                    <!-- 2. Quản Lý Tài Khoản (Admin & Kế Toán) -->
                    @if(in_array(Auth::user()->Permission, ['Admin', 'Kế Toán']))
                    <a href="{{ route('accounts.index') }}" class="nav-item ripple-effect {{ request()->routeIs('accounts.*') ? 'active' : '' }}">
                        <span class="nav-icon floating">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                        <span class="nav-text">Quản Lý Tài Khoản</span>
                        <span class="nav-glow"></span>
                    </a>
                    @endif

                    <!-- 3. Quản Lý Đơn Hàng -->
                    <a href="{{ route('orders.index') }}" class="nav-item ripple-effect {{ request()->routeIs('orders.*') ? 'active' : '' }}">
                        <span class="nav-icon floating">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                            </svg>
                        </span>
                        <span class="nav-text">Quản Lý Đơn Hàng</span>
                        <span class="nav-glow"></span>
                    </a>

                    <!-- 3b. Đơn Thành Công -->
                    <a href="{{ route('successOrders.index') }}" class="nav-item ripple-effect {{ request()->routeIs('successOrders.*') ? 'active' : '' }}">
                        <span class="nav-icon floating">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </span>
                        <span class="nav-text">Đơn Thành Công</span>
                        <span class="nav-glow"></span>
                    </a>

                    <!-- 3c. Đơn Hàng Đã Xóa -->
                    <a href="{{ route('activityLog.index') }}" class="nav-item ripple-effect {{ request()->routeIs('activityLog.*') ? 'active' : '' }}">
                        <span class="nav-icon floating">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                        </span>
                        <span class="nav-text">Đơn Hàng Đã Xóa</span>
                        <span class="nav-glow"></span>
                    </a>

                    <!-- 4. Quản Lý Sản Phẩm -->
                    <a href="{{ route('products.index') }}" class="nav-item ripple-effect {{ request()->routeIs('products.*') ? 'active' : '' }}">
                        <span class="nav-icon floating">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10" />
                            </svg>
                        </span>
                        <span class="nav-text">Quản Lý Sản Phẩm</span>
                        <span class="nav-glow"></span>
                    </a>

                    <!-- 5. Quản Lý Kho -->
                    <a href="{{ route('inventory.index') }}" class="nav-item ripple-effect {{ request()->routeIs('inventory.*') ? 'active' : '' }}">
                        <span class="nav-icon floating">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </span>
                        <span class="nav-text">Quản Lý Kho</span>
                        <span class="nav-glow"></span>
                    </a>

                    <!-- 6. Chuyển Đơn Hàng (Admin & Kế Toán) -->
                    @if(in_array(Auth::user()->Permission, ['Admin', 'Kế Toán']))
                    <a href="{{ route('transferOrders.index') }}" class="nav-item ripple-effect {{ request()->routeIs('transferOrders.*') ? 'active' : '' }}">
                        <span class="nav-icon floating">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                        </span>
                        <span class="nav-text">Chuyển Đơn Hàng</span>
                        <span class="nav-glow"></span>
                    </a>
                    @endif

                    <!-- 7. Đối Chiếu Đơn Hàng (Admin & Kế Toán) -->
                    @if(in_array(Auth::user()->Permission, ['Admin', 'Kế Toán']))
                    <a href="{{ route('reconciliation.index') }}" class="nav-item ripple-effect {{ request()->routeIs('reconciliation.*') ? 'active' : '' }}">
                        <span class="nav-icon floating">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" />
                            </svg>
                        </span>
                        <span class="nav-text">Đối Chiếu Đơn Hàng</span>
                        <span class="nav-glow"></span>
                    </a>
                    @endif

                    <div class="nav-divider"></div>

                    <!-- Đăng xuất -->
                    <form action="{{ route('logout') }}" method="POST" style="display: inline; width: 100%;">
                        @csrf
                        <button type="submit" class="nav-item ripple-effect" style="width: 100%; border: none; background: none; text-align: left; cursor: pointer;">
                            <span class="nav-icon floating">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                                </svg>
                            </span>
                            <span class="nav-text">Đăng xuất</span>
                            <span class="nav-glow"></span>
                        </button>
                    </form>
                </nav>

                <div style="padding:8px 12px;border-top:1px solid rgba(255,255,255,0.12)">
                    <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()" title="Ẩn/Hiện Sidebar">
                        <i class="fa-solid fa-angles-left" id="toggleIcon"></i>
                        <span class="nav-text" style="font-size:13px;color:rgba(255,255,255,0.75)">Thu gọn</span>
                    </button>
                </div>

                <!-- Decorative Elements -->
                <div class="sidebar-decorations">
                    <div class="decoration-orb orb-1"></div>
                    <div class="decoration-orb orb-2"></div>
                    <div class="decoration-orb orb-3"></div>
                </div>
            </aside>

            <main class="maincp-main">
                @if(session('success'))
                    <div class="alert alert-success">
                        <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
                    </div>
                @endif
                @if(session('error'))
                    <div class="alert alert-error">
                        <i class="fa-solid fa-circle-exclamation"></i> {{ session('error') }}
                    </div>
                @endif
                @if($errors->any())
                    <div class="alert alert-error">
                        <ul style="margin: 0; padding-left: 20px;">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @yield('content')
            </main>
    </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Ripple Effect
            document.querySelectorAll('.ripple-effect').forEach(element => {
                element.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    ripple.classList.add('ripple');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;
                    ripple.style.width = ripple.style.height = size + 'px';
                    ripple.style.left = x + 'px';
                    ripple.style.top = y + 'px';
                    this.appendChild(ripple);
                    setTimeout(() => ripple.remove(), 600);
                });
            });

            // Accordion
            document.querySelectorAll('.nav-group-header').forEach(header => {
                header.addEventListener('click', function(e) {
                    e.preventDefault();
                    const navGroup = this.closest('.nav-group');
                    const content = navGroup.querySelector('.nav-group-content');
                    const chevron = this.querySelector('.nav-chevron');
                    const isExpanded = navGroup.classList.contains('expanded');
                    if (isExpanded) {
                        content.style.maxHeight = content.scrollHeight + 'px';
                        setTimeout(() => { content.style.maxHeight = '0'; }, 10);
                        navGroup.classList.remove('expanded');
                        chevron.style.transform = 'rotate(0deg)';
                    } else {
                        navGroup.classList.add('expanded');
                        content.style.maxHeight = content.scrollHeight + 'px';
                        chevron.style.transform = 'rotate(180deg)';
                        setTimeout(() => { if (navGroup.classList.contains('expanded')) content.style.maxHeight = 'none'; }, 400);
                    }
                });
            });

            // Particles
            createParticles();

            // Restore sidebar state
            if (localStorage.getItem('sidebarCollapsed') === '1') {
                document.body.classList.add('sidebar-collapsed');
            }
        });

        function createParticles() {
            const c = document.querySelector('.sidebar-particles');
            if (!c) return;
            for (let i = 0; i < 20; i++) {
                const p = document.createElement('div');
                p.classList.add('particle');
                p.style.left = Math.random() * 100 + '%';
                p.style.animationDelay = Math.random() * 10 + 's';
                p.style.animationDuration = (15 + Math.random() * 10) + 's';
                c.appendChild(p);
            }
        }

        function toggleSidebar() {
            document.body.classList.toggle('sidebar-collapsed');
            localStorage.setItem('sidebarCollapsed', document.body.classList.contains('sidebar-collapsed') ? '1' : '0');
        }
    </script>
    @stack('scripts')
</body>
</html>
