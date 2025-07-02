<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class DashboardService
{
    public function __construct(
        private NotificationCacheService $cacheService
    ) {
    }

    /**
     * Get aggregated notification statistics for a user
     */
    public function getNotificationStats(int $userId, array $filters = []): array
    {
        $cacheKey = "dashboard_stats_{$userId}_" . md5(serialize($filters));

        return $this->cacheService->remember($userId, $cacheKey, 600, function () use ($userId, $filters) {
            $query = $this->buildNotificationQuery($userId, $filters);

            return [
                'total_notifications' => $this->getTotalNotifications($query),
                'unread_count' => $this->getUnreadCount($query),
                'read_count' => $this->getReadCount($query),
                'today_count' => $this->getTodayCount($query),
                'this_week_count' => $this->getThisWeekCount($query),
                'this_month_count' => $this->getThisMonthCount($query),
                'notifications_by_type' => $this->getNotificationsByType($query),
                'daily_stats' => $this->getDailyStats($userId, $filters),
                'weekly_stats' => $this->getWeeklyStats($userId, $filters),
                'monthly_stats' => $this->getMonthlyStats($userId, $filters),
                'read_rate' => $this->getReadRate($query),
                'avg_read_time' => $this->getAverageReadTime($query),
            ];
        });
    }

    /**
     * Get trend data for charts
     */
    public function getTrendData(int $userId, string $period = 'week', array $filters = []): array
    {
        $cacheKey = "dashboard_trends_{$userId}_{$period}_" . md5(serialize($filters));

        return $this->cacheService->remember($userId, $cacheKey, 300, function () use ($userId, $period, $filters) {
            switch ($period) {
                case 'day':
                    return $this->getHourlyTrends($userId, $filters);
                case 'week':
                    return $this->getDailyTrends($userId, $filters);
                case 'month':
                    return $this->getWeeklyTrends($userId, $filters);
                case 'year':
                    return $this->getMonthlyTrends($userId, $filters);
                default:
                    return $this->getDailyTrends($userId, $filters);
            }
        });
    }

    private function buildNotificationQuery(int $userId, array $filters = []): Builder
    {
        $query = Notification::forUser($userId);

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['read_status'])) {
            if ($filters['read_status'] === 'read') {
                $query->read();
            } elseif ($filters['read_status'] === 'unread') {
                $query->unread();
            }
        }

        if (!empty($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (!empty($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        return $query;
    }

    private function getTotalNotifications(Builder $query): int
    {
        return (clone $query)->count();
    }

    private function getUnreadCount(Builder $query): int
    {
        return (clone $query)->unread()->count();
    }

    private function getReadCount(Builder $query): int
    {
        return (clone $query)->read()->count();
    }

    private function getTodayCount(Builder $query): int
    {
        return (clone $query)->whereDate('created_at', today())->count();
    }

    private function getThisWeekCount(Builder $query): int
    {
        return (clone $query)->whereBetween('created_at', [
            Carbon::now()->startOfWeek(),
            Carbon::now()->endOfWeek()
        ])->count();
    }

    private function getThisMonthCount(Builder $query): int
    {
        return (clone $query)->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    private function getNotificationsByType(Builder $query): Collection
    {
        return (clone $query)
            ->select('type', DB::raw('count(*) as count'))
            ->groupBy('type')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->type => $item->count];
            });
    }

    private function getDailyStats(int $userId, array $filters = []): Collection
    {
        $query = $this->buildNotificationQuery($userId, $filters);

        return (clone $query)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN read_at IS NOT NULL THEN 1 ELSE 0 END) as read_count'),
                DB::raw('SUM(CASE WHEN read_at IS NULL THEN 1 ELSE 0 END) as unread_count')
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date', 'desc')
            ->limit(30)
            ->get();
    }

    private function getWeeklyStats(int $userId, array $filters = []): Collection
    {
        $query = $this->buildNotificationQuery($userId, $filters);

        return (clone $query)
            ->select(
                DB::raw('YEARWEEK(created_at) as week'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN read_at IS NOT NULL THEN 1 ELSE 0 END) as read_count'),
                DB::raw('SUM(CASE WHEN read_at IS NULL THEN 1 ELSE 0 END) as unread_count')
            )
            ->groupBy(DB::raw('YEARWEEK(created_at)'))
            ->orderBy('week', 'desc')
            ->limit(12)
            ->get();
    }

    private function getMonthlyStats(int $userId, array $filters = []): Collection
    {
        $query = $this->buildNotificationQuery($userId, $filters);

        return (clone $query)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN read_at IS NOT NULL THEN 1 ELSE 0 END) as read_count'),
                DB::raw('SUM(CASE WHEN read_at IS NULL THEN 1 ELSE 0 END) as unread_count')
            )
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year', 'desc')
            ->orderBy('month', 'desc')
            ->limit(12)
            ->get();
    }

    private function getReadRate(Builder $query): float
    {
        $total = $this->getTotalNotifications($query);
        if ($total === 0) {
            return 0;
        }

        $read = $this->getReadCount($query);
        return round(($read / $total) * 100, 2);
    }

    private function getAverageReadTime(Builder $query): ?float
    {
        $avgSeconds = (clone $query)
            ->whereNotNull('read_at')
            ->select(DB::raw('AVG(TIMESTAMPDIFF(SECOND, created_at, read_at)) as avg_time'))
            ->value('avg_time');

        return $avgSeconds ? round($avgSeconds / 60, 2) : null; // em minutos
    }

    private function getHourlyTrends(int $userId, array $filters = []): array
    {
        $query = $this->buildNotificationQuery($userId, $filters);

        $data = (clone $query)
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as count')
            )
            ->whereDate('created_at', today())
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get()
            ->pluck('count', 'hour');

        // Preencher horas faltantes com 0
        $trends = [];
        for ($i = 0; $i < 24; $i++) {
            $trends[] = [
                'label' => sprintf('%02d:00', $i),
                'value' => $data->get($i, 0)
            ];
        }

        return $trends;
    }

    private function getDailyTrends(int $userId, array $filters = []): array
    {
        $query = $this->buildNotificationQuery($userId, $filters);

        $data = (clone $query)
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [
                Carbon::now()->subDays(6)->startOfDay(),
                Carbon::now()->endOfDay()
            ])
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $trends = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $trends[] = [
                'label' => Carbon::parse($date)->format('d/m'),
                'value' => $data->get($date)->count ?? 0
            ];
        }

        return $trends;
    }

    private function getWeeklyTrends(int $userId, array $filters = []): array
    {
        $query = $this->buildNotificationQuery($userId, $filters);

        $data = (clone $query)
            ->select(
                DB::raw('YEARWEEK(created_at) as week'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [
                Carbon::now()->subWeeks(3)->startOfWeek(),
                Carbon::now()->endOfWeek()
            ])
            ->groupBy(DB::raw('YEARWEEK(created_at)'))
            ->orderBy('week')
            ->get()
            ->keyBy('week');

        $trends = [];
        for ($i = 3; $i >= 0; $i--) {
            $week = Carbon::now()->subWeeks($i)->format('oW');
            $weekStart = Carbon::now()->subWeeks($i)->startOfWeek();
            $trends[] = [
                'label' => $weekStart->format('d/m'),
                'value' => $data->get($week)->count ?? 0
            ];
        }

        return $trends;
    }

    private function getMonthlyTrends(int $userId, array $filters = []): array
    {
        $query = $this->buildNotificationQuery($userId, $filters);

        $data = (clone $query)
            ->select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->whereBetween('created_at', [
                Carbon::now()->subMonths(11)->startOfMonth(),
                Carbon::now()->endOfMonth()
            ])
            ->groupBy(DB::raw('YEAR(created_at)'), DB::raw('MONTH(created_at)'))
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        $trends = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $key = $date->year . '-' . $date->month;

            $count = $data->where('year', $date->year)
                         ->where('month', $date->month)
                         ->first()->count ?? 0;

            $trends[] = [
                'label' => $date->format('M/y'),
                'value' => $count
            ];
        }

        return $trends;
    }
}
