<?php

namespace App\Livewire;

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
        $this->notifications = $this->notificationService->latestUnreadForUser(Auth::id());

        $this->unreadCount = $this->notificationService->countUnreadForUser(Auth::id());
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

    public function viewAllNotifications()
    {
        return $this->redirect(route('notifications.index'));
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
