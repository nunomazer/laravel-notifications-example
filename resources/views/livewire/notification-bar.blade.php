<div class="notification-container">
    <button wire:click="toggleDropdown" class="notification-button">
        <svg class="notification-icon" viewBox="0 0 24 24" fill="currentColor">
            <path d="M12 22c1.1 0 2-.9 2-2h-4c0 1.1.89 2 2 2zm6-6v-5c0-3.07-1.64-5.64-4.5-6.32V4c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5v.68C7.63 5.36 6 7.92 6 11v5l-2 2v1h16v-1l-2-2z"/>
        </svg>
        @if($unreadCount > 0)
            <span class="notification-badge">{{ $unreadCount }}</span>
        @endif
    </button>

    @if($showDropdown)
        <div class="notification-dropdown">
            <div class="notification-header">
                <h3>Notifications</h3>
            </div>
            <div class="notification-list">
                @forelse($notifications as $notification)
                    <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}">
                        <div class="notification-content">
                            <p class="notification-title">{{ $notification->title }}</p>
                            <p class="notification-message">{{ \Illuminate\Support\Str::limit($notification->message, 100) }}</p>
                            <span class="notification-time">{{ $notification->created_at->diffForHumans() }}</span>
                        </div>
                        @if(!$notification->read_at)
                            <button wire:click="markAsRead('{{ $notification->id }}')" class="mark-read-button">
                                Mark as read
                            </button>
                        @endif
                    </div>
                @empty
                    <div class="no-notifications">
                        <p>No notification</p>
                    </div>
                @endforelse
            </div>
            <div class="notification-footer">
                <button wire:click="viewAllNotifications" class="view-all-button">
                    View All Notifications
                </button>
            </div>
        </div>
    @endif
</div>