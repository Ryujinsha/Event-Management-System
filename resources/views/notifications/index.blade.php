@extends('layouts.app')
@section('title', 'Notifications')

@section('content')
<div class="section-header">
    <h3 class="section-title">Notifications</h3>
    @if($notifications->count())
    <form method="POST" action="{{ route('notifications.markAllAsRead') }}">
        @csrf
        <button type="submit" class="btn btn-outline btn-sm"><i class="fas fa-check-double"></i> Mark All Read</button>
    </form>
    @endif
</div>

<div class="card">
    @forelse($notifications as $notification)
    <div class="notification-item {{ !$notification->is_read ? 'unread' : '' }}">
        <div class="notification-icon {{ $notification->type }}">
            <i class="fas fa-{{ match($notification->type) {
                'event' => 'chalkboard-teacher',
                'participant' => 'clipboard-check',
                'certificate' => 'award',
                default => 'bell'
            } }}"></i>
        </div>
        <div class="notification-content">
            <div class="notification-title">{{ $notification->title }}</div>
            <div class="notification-message">{{ $notification->message }}</div>
            <div class="notification-time">{{ $notification->created_at->diffForHumans() }}</div>
        </div>
        @if(!$notification->is_read)
        <form method="POST" action="{{ route('notifications.markAsRead', $notification) }}">
            @csrf @method('PATCH')
            <button type="submit" class="btn btn-sm btn-outline" title="Mark as read"><i class="fas fa-check"></i></button>
        </form>
        @endif
    </div>
    @empty
    <div class="empty-state" style="padding:3rem;">
        <i class="fas fa-bell-slash"></i>
        <h3>No notifications</h3>
        <p>You're all caught up!</p>
    </div>
    @endforelse
</div>

<div class="pagination-wrapper">{{ $notifications->links() }}</div>
@endsection
