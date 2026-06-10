@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="fade-in">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h4 class="fw-bold mb-1">Notifications</h4><p class="text-muted mb-0">Stay updated with your activities</p></div>
        <form method="POST" action="{{ route('notifications.markAllRead') }}">@csrf
            <button type="submit" class="btn btn-outline-primary btn-sm"><i class="bi bi-check-all"></i> Mark All Read</button>
        </form>
    </div>

    <div class="card stat-card">
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($notifications as $notification)
                    <div class="list-group-item d-flex align-items-start gap-3 py-3 {{ $notification->status === 'unread' ? 'bg-primary bg-opacity-10' : '' }}">
                        <div class="mt-1">
                            <i class="bi bi-{{ $notification->type === 'enrollment' ? 'journal-check' : ($notification->type === 'assignment' ? 'file-text' : ($notification->type === 'quiz' ? 'pencil-square' : 'bell')) }} fs-4 text-primary"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <p class="mb-0 fw-medium">{{ $notification->title }}</p>
                                <div class="d-flex align-items-center gap-2">
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                    @if($notification->status === 'unread')
                                        <form method="POST" action="{{ route('notifications.markRead', $notification) }}" class="d-inline">@csrf
                                            <button type="submit" class="btn btn-sm btn-link text-decoration-none p-0"><small>Mark read</small></button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                            @if($notification->message)
                                <p class="mb-0 text-muted small">{{ $notification->message }}</p>
                            @endif
                            @if($notification->status === 'unread')
                                <span class="badge bg-primary badge-pulse mt-1">New</span>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5 text-muted">
                        <i class="bi bi-bell-slash fs-1 d-block mb-3"></i>
                        <p>No notifications yet</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
    {{ $notifications->links() }}
</div>
@endsection
