<div class="dashboard-grid">
    <!-- Filtros -->
    <div class="filters-section">
        <h3 class="stat-title" style="margin-bottom: 16px;">Filtros</h3>
        <div class="filters-grid">
            <div class="filter-group">
                <label class="filter-label">Type</label>
                <select wire:model.live="filters.type" class="filter-input">
                    <option value="">All types</option>
                    @foreach($this->getNotificationTypes() as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">Status</label>
                <select wire:model.live="filters.read_status" class="filter-input">
                    <option value="">All</option>
                    <option value="read">Read</option>
                    <option value="unread">Unread</option>
                </select>
            </div>

            <div class="filter-group">
                <label class="filter-label">From date</label>
                <input type="date" wire:model.live="filters.date_from" class="filter-input">
            </div>

            <div class="filter-group">
                <label class="filter-label">To date</label>
                <input type="date" wire:model.live="filters.date_to" class="filter-input">
            </div>

            <div class="filter-group">
                <button wire:click="resetFilters" class="filter-button secondary">
                    Clear
                </button>
            </div>
        </div>
    </div>

    <!-- Cards de Estatísticas -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Total</h3>
            </div>
            <p class="stat-value">{{ number_format($stats['total_notifications'] ?? 0) }}</p>
        </div>

        <div class="stat-card success">
            <div class="stat-header">
                <h3 class="stat-title">Read</h3>
            </div>
            <p class="stat-value">{{ number_format($stats['read_count'] ?? 0) }}</p>
            <p class="stat-subtitle">{{ $stats['read_rate'] ?? 0 }}% of read</p>
        </div>

        <div class="stat-card warning">
            <div class="stat-header">
                <h3 class="stat-title">Unread</h3>
            </div>
            <p class="stat-value">{{ number_format($stats['unread_count'] ?? 0) }}</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">Today</h3>
            </div>
            <p class="stat-value">{{ number_format($stats['today_count'] ?? 0) }}</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">This week</h3>
            </div>
            <p class="stat-value">{{ number_format($stats['this_week_count'] ?? 0) }}</p>
        </div>

        <div class="stat-card">
            <div class="stat-header">
                <h3 class="stat-title">This month</h3>
            </div>
            <p class="stat-value">{{ number_format($stats['this_month_count'] ?? 0) }}</p>
        </div>
    </div>

    <!-- Gráfico de Tendências -->
    <div class="chart-section">
        <div class="chart-header">
            <h3 class="chart-title">Trend</h3>
            <div class="period-selector">
                <button wire:click="$set('filters.period', 'day')"
                        class="period-button {{ $filters['period'] === 'day' ? 'active' : '' }}">
                    Hoje
                </button>
                <button wire:click="$set('filters.period', 'week')"
                        class="period-button {{ $filters['period'] === 'week' ? 'active' : '' }}">
                    7 Dias
                </button>
                <button wire:click="$set('filters.period', 'month')"
                        class="period-button {{ $filters['period'] === 'month' ? 'active' : '' }}">
                    30 Dias
                </button>
                <button wire:click="$set('filters.period', 'year')"
                        class="period-button {{ $filters['period'] === 'year' ? 'active' : '' }}">
                    12 Meses
                </button>
            </div>
        </div>

        <div class="chart-container">
            @if($loading)
                <div class="loading-overlay">
                    <div class="spinner"></div>
                </div>
            @endif
            <canvas id="trendsChart"></canvas>
        </div>
    </div>

    <!-- Insights Adicionais -->
    <div class="insights-grid">
        <div class="insight-card">
            <h4 class="insight-title">Notifications by type</h4>
            <div class="type-stats">
                @foreach(($stats['notifications_by_type'] ?? []) as $type => $count)
                    <div class="type-stat-item">
                        <span class="type-name">{{ ucfirst($type) }}</span>
                        <span class="type-count">{{ number_format($count) }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        @if(isset($stats['avg_read_time']) && $stats['avg_read_time'])
            <div class="insight-card">
                <h4 class="insight-title">Average reading time</h4>
                <p class="stat-value" style="font-size: 24px;">
                    {{ number_format($stats['avg_read_time'], 1) }} min
                </p>
            </div>
        @endif
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        let trendsChart = null;

        function initChart() {
            const ctx = document.getElementById('trendsChart');
            if (!ctx) return;

            if (trendsChart) {
                trendsChart.destroy();
            }

            trendsChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Notificações',
                        data: [],
                        borderColor: '#3b82f6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        tension: 0.4,
                        fill: true
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                precision: 0
                            }
                        }
                    }
                }
            });

            updateChart(@json($trendData ?? []));
        }

        function updateChart(data) {
            if (!trendsChart || !data) return;

            trendsChart.data.labels = data.map(item => item.label);
            trendsChart.data.datasets[0].data = data.map(item => item.value);
            trendsChart.update('none');
        }

        initChart();

        document.addEventListener('livewire:init', () => {
            Livewire.on('dashboard-updated', (event) => {
                updateChart(event.trends);
            });
        });
    });
</script>