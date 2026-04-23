@extends('layouts.app')
@section('title', 'Events')

@section('content')
<div class="section-header">
    <h3 class="section-title">All Events</h3>
    @if(auth()->user()->isAdmin() || auth()->user()->isCommittee())
    <a href="{{ route('events.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create Event</a>
    @endif
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('events.index') }}" style="display:flex;gap:0.75rem;flex-wrap:wrap;width:100%;">
        <input type="text" name="search" class="form-input" placeholder="Search events..." value="{{ request('search') }}" style="max-width:300px;">
        <select name="status" class="form-input" style="max-width:180px;" onchange="this.form.submit()">
            <option value="">All Status</option>
            @foreach(['draft','pending_approval','approved','published','ongoing','completed','cancelled'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst(str_replace('_', ' ', $s)) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-outline btn-sm"><i class="fas fa-search"></i> Search</button>
    </form>
</div>

<div class="event-grid">
    @forelse($events as $event)
    <a href="{{ route('events.show', $event) }}" class="event-card">
        <div class="event-card-header">
            <span class="badge-status badge-{{ $event->status }}">{{ ucfirst($event->status) }}</span>
        </div>
        <div class="event-card-body">
            <h3 class="event-title">{{ $event->title }}</h3>
            <p class="event-desc">{{ Str::limit($event->description, 120) }}</p>
            <div class="event-meta">
                <span><i class="fas fa-calendar"></i> {{ $event->start_date->format('d M Y') }}</span>
                <span><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</span>
            </div>
        </div>
        <div class="event-card-footer">
            <span style="font-size:0.8125rem;color:var(--text-muted);"><i class="fas fa-users" style="margin-right:0.25rem;"></i> {{ $event->participants_count }}/{{ $event->quota }}</span>
            <span style="font-size:0.8125rem;color:var(--text-muted);">By {{ $event->creator->name ?? 'N/A' }}</span>
        </div>
    </a>
    @empty
    <div class="empty-state" style="grid-column:1/-1;">
        <i class="fas fa-chalkboard-teacher"></i>
        <h3>No events found</h3>
        <p>Check back later for new event opportunities</p>
    </div>
    @endforelse
</div>

<div class="pagination-wrapper">{{ $events->withQueryString()->links() }}</div>
@endsection
