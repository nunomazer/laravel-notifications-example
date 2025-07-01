<?php

namespace App\Livewire;

use App\Models\Notification;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBar extends Component
{
    public $unreadCount = 0;
    public $showDropdown = false;
    public $notifications = [];
    protected NotificationService $notificationService;

    public function __construct()
    {
        $this->notificationService = app(NotificationService::class);
    }

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $this->notifications = Auth::user()
            ->notifications()
            ->latest()
            ->limit(5)
            ->get();

        $this->unreadCount = Auth::user()
            ->notifications()
            ->whereNull('read_at')
            ->count();
    }

    public function toggleDropdown()
    {
        $this->showDropdown = !$this->showDropdown;
    }

    public function markAsRead(int $notificationId)
    {
        $this->notificationService->markAsRead($notificationId);
        $this->loadNotifications();
    }

    #[On('notification-created')]
    public function handleNewNotification()
    {
        $this->loadNotifications();
    }

    public function render()
    {
        return view('livewire.notification-bar');
    }
}