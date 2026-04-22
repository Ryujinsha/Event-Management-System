@extends('layouts.app')
@section('title', 'Faculty Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalCreated }}</div>
            <div class="stat-label">Trainings Created</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $pendingApprovals }}</div>
            <div class="stat-label">Pending Approvals</div>
        </div>
    </div>
</div>

<div class="section-header">
    <h3 class="section-title">My Trainings</h3>
    <a href="{{ route('trainings.create') }}" class="btn btn-primary"><i class="fas fa-plus"></i> Create Training</a>
</div>

<div class="training-grid">
    @forelse($myTrainings as $training)
    <a href="{{ route('trainings.show', $training) }}" class="training-card">
        <div class="training-card-header">
            <span class="badge-status badge-{{ $training->status }}">{{ ucfirst($training->status) }}</span>
        </div>
        <div class="training-card-body">
            <h3 class="training-title">{{ $training->title }}</h3>
            <p class="training-desc">{{ Str::limit($training->description, 100) }}</p>
            <div class="training-meta">
                <span><i class="fas fa-calendar"></i> {{ $training->start_date->format('d M Y') }}</span>
                <span><i class="fas fa-map-marker-alt"></i> {{ $training->location }}</span>
                <span><i class="fas fa-users"></i> {{ $training->quota }} slots</span>
            </div>
        </div>
    </a>
    @empty
    <div class="empty-state" style="grid-column:1/-1;">
        <i class="fas fa-chalkboard-teacher"></i>
        <h3>No trainings yet</h3>
        <p>Create your first training to get started</p>
        <a href="{{ route('trainings.create') }}" class="btn btn-primary mt-2"><i class="fas fa-plus"></i> Create Training</a>
    </div>
    @endforelse
</div>
@endsection
