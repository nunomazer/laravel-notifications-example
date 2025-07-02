<?php

namespace App\Livewire;

use App\Enums\NotificationType;
use App\Services\DashboardService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class Dashboard extends Component
{
    public array $stats = [];
    public array $trendData = [];
    public array $filters = [
        'type' => '',
        'read_status' => '',
        'date_from' => '',
        'date_to' => '',
        'period' => 'week'
    ];

    public bool $loading = false;

    protected DashboardService $dashboardService;

    public function boot(DashboardService $dashboardService)
    {
        $this->dashboardService = $dashboardService;
    }

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function updatedFilters()
    {
        $this->loadDashboardData();
    }

    public function resetFilters()
    {
        $this->filters = [
            'type' => '',
            'read_status' => '',
            'date_from' => '',
            'date_to' => '',
            'period' => 'week'
        ];
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $this->loading = true;

        $userId = Auth::id();
        $filters = array_filter($this->filters, fn ($value) => $value !== '');

        $this->stats = $this->dashboardService->getNotificationStats($userId, $filters);
        $this->trendData = $this->dashboardService->getTrendData($userId, $this->filters['period'], $filters);

        $this->loading = false;

        $this->dispatch('dashboard-updated', [
            'stats' => $this->stats,
            'trends' => $this->trendData
        ]);
    }

    public function getNotificationTypes()
    {
        return NotificationType::getLabels();
    }

    public function render()
    {
        return view('livewire.dashboard');
    }
}
