<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: #f5f5f5;
            line-height: 1.6;
        }

        .topbar {
            background: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 0 20px;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-nav {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .nav-link {
            color: #6b7280;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            padding: 8px 16px;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .nav-link:hover {
            color: #374151;
            background-color: #f3f4f6;
        }

        .nav-link.active {
            color: #3b82f6;
            background-color: #eff6ff;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #333;
        }

        .user-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #007bff;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
        }

        .notification-container {
            position: relative;
        }

        .notification-button {
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            border-radius: 6px;
            transition: background-color 0.2s;
            position: relative;
        }

        .notification-button:hover {
            background-color: #f0f0f0;
        }

        .notification-icon {
            width: 24px;
            height: 24px;
            color: #666;
        }

        .notification-badge {
            position: absolute;
            top: 0;
            right: 0;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        .notification-dropdown {
            position: absolute;
            top: 100%;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            width: 350px;
            max-height: 400px;
            overflow-y: auto;
            z-index: 1001;
        }

        .notification-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }

        .notification-header h3 {
            font-size: 16px;
            color: #333;
        }

        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .notification-item {
            padding: 15px 20px;
            border-bottom: 1px solid #f0f0f0;
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
        }

        .notification-item.unread {
            background-color: #f8f9ff;
            border-left: 3px solid #007bff;
        }

        .notification-content {
            flex: 1;
        }

        .notification-title {
            font-weight: 600;
            color: #333;
            margin-bottom: 4px;
        }

        .notification-message {
            color: #666;
            font-size: 14px;
            margin-bottom: 6px;
        }

        .notification-time {
            color: #999;
            font-size: 12px;
        }

        .mark-read-button {
            background: #007bff;
            color: white;
            border: none;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            cursor: pointer;
            margin-left: 10px;
        }

        .mark-read-button:hover {
            background: #0056b3;
        }

        .no-notifications {
            padding: 40px 20px;
            text-align: center;
            color: #999;
        }

        .main-content {
            margin-top: 60px;
            padding: 30px;
        }

        .content-area {
            background: white;
            border-radius: 8px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .welcome-message {
            color: #333;
            font-size: 24px;
            margin-bottom: 20px;
        }

        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }

        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 8px;
            border-left: 4px solid #007bff;
        }

        .stat-title {
            color: #666;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .stat-value {
            color: #333;
            font-size: 28px;
            font-weight: bold;
        }

        .notification-footer {
            border-top: 1px solid #e5e7eb;
            padding: 12px 16px;
            background-color: #f9fafb;
        }

        .view-all-button {
            width: 100%;
            padding: 8px 16px;
            background-color: #3b82f6;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: background-color 0.2s ease;
            text-align: center;
        }

        .view-all-button:hover {
            background-color: #2563eb;
        }

        .view-all-button:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        .view-all-button:active {
            background-color: #1d4ed8;
            transform: translateY(1px);
        }

        .notifications-page {
            max-width: 1200px;
            margin: 0 auto;
            padding: 32px 16px;
        }

        .notifications-container {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .notifications-header {
            padding: 24px;
            border-bottom: 1px solid #e5e7eb;
        }

        .notifications-title {
            margin: 0;
            font-size: 24px;
            font-weight: 600;
            color: #111827;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .notifications-table {
            width: 100%;
            border-collapse: collapse;
            table-layout: auto;
        }

        .notifications-table thead {
            background-color: #f9fafb;
        }

        .notifications-table th {
            padding: 12px 24px;
            text-align: left;
            font-size: 12px;
            font-weight: 500;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border-bottom: 1px solid #e5e7eb;
        }

        .notifications-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
            transition: background-color 0.2s ease;
        }

        .notification-row.unread {
            background-color: #eff6ff;
        }

        .notification-row.read {
            background-color: #ffffff;
        }

        .notifications-table tbody tr:hover {
            background-color: #f3f4f6;
        }

        .notifications-table td {
            padding: 16px 24px;
            vertical-align: top;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 4px 8px;
            border-radius: 9999px;
            font-size: 12px;
            font-weight: 500;
            white-space: nowrap;
        }

        .status-read {
            background-color: #dcfce7;
            color: #166534;
        }

        .status-unread {
            background-color: #dbeafe;
            color: #1e40af;
        }

        .notification-title-cell {
            font-size: 14px;
            font-weight: 500;
            color: #111827;
            line-height: 1.4;
        }

        .notification-message-cell {
            font-size: 14px;
            color: #6b7280;
            line-height: 1.4;
        }

        .notification-date-cell {
            font-size: 14px;
            color: #6b7280;
            white-space: nowrap;
        }

        .pagination-wrapper {
            padding: 16px 24px;
            border-top: 1px solid #e5e7eb;
        }

        .no-notifications {
            padding: 48px 24px;
            text-align: center;
        }

        .no-notifications p {
            margin: 0;
            color: #6b7280;
            font-size: 16px;
        }

        /* Pagination Styles */
        nav {
            display: flex;
            justify-content: center;
            margin-top: 16px;
        }

        .pagination {
            display: flex;
            align-items: center;
            gap: 4px;
            margin: 0;
            padding: 0;
            list-style: none;
        }

        .pagination li {
            display: inline-block;
        }

        .pagination li a,
        .pagination li span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 8px 12px;
            font-size: 14px;
            font-weight: 500;
            color: #6b7280;
            background-color: #ffffff;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            text-decoration: none;
            transition: all 0.2s ease;
            min-width: 40px;
            height: 40px;
        }

        .pagination li a:hover {
            background-color: #f3f4f6;
            color: #374151;
            border-color: #9ca3af;
        }

        .pagination li.active span {
            background-color: #3b82f6;
            color: #ffffff;
            border-color: #3b82f6;
        }

        .pagination li.disabled span {
            color: #9ca3af;
            background-color: #ffffff;
            border-color: #e5e7eb;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .pagination li.disabled span:hover {
            color: #9ca3af;
            background-color: #ffffff;
            border-color: #e5e7eb;
        }

        .pagination-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            font-size: 14px;
            color: #6b7280;
        }

        .pagination-container {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        /* Toast Styles */
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 9999;
        }

        .toast {
            background: #333;
            color: white;
            padding: 16px 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
            margin-bottom: 10px;
            min-width: 300px;
            opacity: 0;
            transform: translateX(100%);
            transition: all 0.3s ease;
        }

        .toast.show {
            opacity: 1;
            transform: translateX(0);
        }

        .toast-title {
            font-weight: bold;
            margin-bottom: 4px;
        }

        .toast-message {
            font-size: 14px;
            opacity: 0.9;
        }

        .toast.success {
            background: #10b981;
        }

        .toast.error {
            background: #ef4444;
        }

        .toast.warning {
            background: #f59e0b;
        }

        .toast.info {
            background: #3b82f6;
        }
    </style>
    <meta name="user-id" content="{{ Auth::id() }}">
    @livewireStyles
</head>
<body>
<header class="topbar">
    <div class="topbar-nav">
        <div class="logo">
            @yield('title')
        </div>

        <nav>
            <a href="{{ route('home') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Home
            </a>
            <a href="{{ route('notifications.index') }}"
               class="nav-link {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                Notifications
            </a>
        </nav>
    </div>

    <div class="user-section">
        @livewire('notification-bar')

        <div class="user-info">
            <div class="user-avatar">
                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
            </div>
            <span>{{ Auth::user()->name }}</span>
        </div>
    </div>
</header>

<main class="main-content">
    <div class="content-area">
        @yield('content')
    </div>
</main>

@livewireScripts
@vite(['resources/js/app.js'])

<script>
    document.addEventListener('DOMContentLoaded', function () {
        // toast container
        if (!document.querySelector('.toast-container')) {
            const toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container';
            document.body.appendChild(toastContainer);
        }

        function showToast(title, message, type = 'info') {
            const toastContainer = document.querySelector('.toast-container');

            const toast = document.createElement('div');
            toast.className = `toast ${type}`;
            toast.innerHTML = `
                <div class="toast-title">${title}</div>
                <div class="toast-message">${message}</div>
            `;

            toastContainer.appendChild(toast);

            setTimeout(() => toast.classList.add('show'), 100);

            // remove after 2 seconds
            setTimeout(() => {
                toast.classList.remove('show');
                setTimeout(() => toast.remove(), 300);
            }, 4000);
        }

        // Laravel Echo
        window.Echo.private(`${window.Laravel.user.id}.user.notifications`)
            .listen('NotificationCreated', (e) => {
                console.log('New notification:', e);

                // Show a toast notification
                showToast(
                    e.notification.title,
                    e.notification.message,
                    e.notification.type
                );

                // Dispatch Livewire event to update the notification bar
                Livewire.dispatch('notification-created');
            });

        // dropdown
        document.addEventListener('click', function (e) {
            if (!e.target.closest('.notification-container')) {
                Livewire.dispatch('close-dropdown');
            }
        });
    });
</script>
</body>
</html>