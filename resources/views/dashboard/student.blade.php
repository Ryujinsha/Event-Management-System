@extends('layouts.app')
@section('title', 'Student Dashboard')

@section('content')
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-clipboard-check"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $registeredCount }}</div>
            <div class="stat-label">Registered Trainings</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-award"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $certificatesCount }}</div>
            <div class="stat-label">Certificates Earned</div>
        </div>
    </div>
</div>

<!-- Upcoming Trainings -->
<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-calendar-alt" style="color:var(--primary-400);margin-right:0.5rem;"></i> Available Trainings</h3>
        <a href="{{ route('trainings.index') }}" class="btn btn-sm btn-outline">Browse All</a>
    </div>
    <div class="card-body">
        @forelse($upcomingTrainings as $training)
        <a href="{{ route('trainings.show', $training) }}" style="display:flex;align-items:center;justify-content:space-between;padding:0.875rem 0;border-bottom:1px solid var(--border-color);text-decoration:none;color:inherit;transition:opacity 0.2s;">
            <div>
                <div style="font-weight:600;font-size:0.9375rem;color:var(--text-primary);">{{ $training->title }}</div>
                <div style="font-size:0.8125rem;color:var(--text-muted);margin-top:0.25rem;">
                    <i class="fas fa-calendar" style="margin-right:0.25rem;"></i> {{ $training->start_date->format('d M Y, H:i') }}
                    <span style="margin:0 0.5rem;">•</span>
                    <i class="fas fa-map-marker-alt" style="margin-right:0.25rem;"></i> {{ $training->location }}
                </div>
            </div>
            <div style="text-align:right;">
                <span class="badge-status badge-published">Open</span>
                <div style="font-size:0.75rem;color:var(--text-muted);margin-top:0.375rem;">{{ $training->availableSlots() }} slots left</div>
            </div>
        </a>
        @empty
        <div class="empty-state">
            <i class="fas fa-calendar-times"></i>
            <p>No upcoming trainings available</p>
        </div>
        @endforelse
    </div>
</div>

<!-- My Recent Registrations -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-clipboard-list" style="color:var(--primary-400);margin-right:0.5rem;"></i> My Registrations</h3>
        <a href="{{ route('registrations.my') }}" class="btn btn-sm btn-outline">View All</a>
    </div>
    <div class="card-body">
        @forelse($myRegistrations as $reg)
        <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border-color);">
            <div>
                <div style="font-weight:600;font-size:0.9375rem;">{{ $reg->training->title }}</div>
                <div style="font-size:0.8125rem;color:var(--text-muted);">Reg #{{ $reg->registration_number }}</div>
            </div>
            <span class="badge-status badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span>
        </div>
        @empty
        <div class="empty-state">
            <i class="fas fa-clipboard"></i>
            <p>No registrations yet. Browse trainings to get started!</p>
        </div>
        @endforelse
    </div>
</div>
@endsection
