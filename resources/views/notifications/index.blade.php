@extends('layouts.app')

@section('title', 'All Notifications')

@section('content')
<div class="notifications-page">
    <div class="notifications-container">
        <div class="notifications-header">
            <h1 class="notifications-title">All Notifications</h1>
        </div>

        @if($notifications->count() > 0)
            <div class="table-wrapper">
                <table class="notifications-table">
                    <thead>
                        <tr>
                            <th>Status</th>
                            <th>Title</th>
                            <th>Message</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($notifications as $notification)
                            <tr class="notification-row {{ $notification->read_at ? 'read' : 'unread' }}">
                                <td>
                                    @if($notification->read_at)
                                        <span class="status-badge status-read">Read</span>
                                    @else
                                        <span class="status-badge status-unread">Unread</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="notification-title-cell">
                                        {{ $notification->title }}
                                    </div>
                                </td>
                                <td>
                                    <div class="notification-message-cell">
                                        {{ \Illuminate\Support\Str::limit($notification->message, 80) }}
                                    </div>
                                </td>
                                <td class="notification-date-cell">
                                    {{ $notification->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="pagination-wrapper">
                {{ $notifications->links('vendor.pagination.default') }}
            </div>
        @else
            <div class="no-notifications">
                <p>Você não tem notificações.</p>
            </div>
        @endif
    </div>
</div>
@endsection