@extends('layouts.app')
@section('title', 'Trainings')

@section('content')
<div class="section-header">
    <h3 class="section-title">All Trainings</h3>
    @if(auth()->user()->isAdmin() || auth()->user()->isFaculty())
    <a href="{{ route('trainings.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create Training</a>
    @endif
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('trainings.index') }}" style="display:flex;gap:0.75rem;flex-wrap:wrap;width:100%;">
        <input type="text" name="search" class="form-input" placeholder="Search trainings..." value="{{ request('search') }}" style="max-width:300px;">
        <select name="status" class="form-input" style="max-width:180px;" onchange="this.form.submit()">
            <option value="">All Status</option>
            @foreach(['draft','published','ongoing','completed','cancelled'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button type="submit" class="btn btn-outline btn-sm"><i class="fas fa-search"></i> Search</button>
    </form>
</div>

<div class="training-grid">
    @forelse($trainings as $training)
    <a href="{{ route('trainings.show', $training) }}" class="training-card">
        <div class="training-card-header">
            <span class="badge-status badge-{{ $training->status }}">{{ ucfirst($training->status) }}</span>
        </div>
        <div class="training-card-body">
            <h3 class="training-title">{{ $training->title }}</h3>
            <p class="training-desc">{{ Str::limit($training->description, 120) }}</p>
            <div class="training-meta">
                <span><i class="fas fa-calendar"></i> {{ $training->start_date->format('d M Y') }}</span>
                <span><i class="fas fa-map-marker-alt"></i> {{ $training->location }}</span>
            </div>
        </div>
        <div class="training-card-footer">
            <span style="font-size:0.8125rem;color:var(--text-muted);"><i class="fas fa-users" style="margin-right:0.25rem;"></i> {{ $training->registrations_count }}/{{ $training->quota }}</span>
            <span style="font-size:0.8125rem;color:var(--text-muted);">By {{ $training->creator->name ?? 'N/A' }}</span>
        </div>
    </a>
    @empty
    <div class="empty-state" style="grid-column:1/-1;">
        <i class="fas fa-chalkboard-teacher"></i>
        <h3>No trainings found</h3>
        <p>Check back later for new training opportunities</p>
    </div>
    @endforelse
</div>

<div class="pagination-wrapper">{{ $trainings->withQueryString()->links() }}</div>
@endsection
