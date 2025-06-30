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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    </style>
    <meta name="user-id" content="{{ Auth::id() }}">
    @livewireStyles
</head>
<body>
    <header class="topbar">
        <div class="logo">
            Dashboard
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
            <h1 class="welcome-message">Bem-vindo, {{ Auth::user()->name }}!</h1>
            <p>Esta é sua área principal do dashboard.</p>

            <div class="dashboard-stats">
                <div class="stat-card">
                    <div class="stat-title">Total de Notificações</div>
                    <div class="stat-value">{{ Auth::user()->notifications()->count() }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Não Lidas</div>
                    <div class="stat-value">{{ Auth::user()->unreadNotifications()->count() }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-title">Hoje</div>
                    <div class="stat-value">{{ Auth::user()->notifications()->whereDate('created_at', today())->count() }}</div>
                </div>
            </div>
        </div>
    </main>

    @livewireScripts
    @vite(['resources/js/app.js'])

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Configuração do Laravel Echo
            window.Echo.private(`${window.Laravel.user.id}.user.notifications`)
                .listen('NotificationCreated', (e) => {
                    // Dispara evento Livewire para atualizar as notificações
                    Livewire.dispatch('notification-created');
                });

            // Fechar dropdown ao clicar fora
            document.addEventListener('click', function(e) {
                if (!e.target.closest('.notification-container')) {
                    Livewire.dispatch('close-dropdown');
                }
            });
        });
    </script>
</body>
</html>