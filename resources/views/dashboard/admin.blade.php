@extends('layouts.app')
@section('title', 'Admin Dashboard')

@section('content')
<!-- Stats -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon purple"><i class="fas fa-chalkboard-teacher"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalTrainings }}</div>
            <div class="stat-label">Total Trainings</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="fas fa-users"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalUsers }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="fas fa-clock"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $pendingRegistrations }}</div>
            <div class="stat-label">Pending Registrations</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="fas fa-check-circle"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $completedTrainings }}</div>
            <div class="stat-label">Completed Trainings</div>
        </div>
    </div>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:1.25rem;" class="dashboard-grid">
    <!-- Chart -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-chart-bar" style="color:var(--primary-400);margin-right:0.5rem;"></i> Monthly Overview</h3>
        </div>
        <div class="card-body">
            <div class="chart-container">
                <div class="chart-bars">
                    @foreach($monthlyStats as $stat)
                    <div class="chart-bar-group">
                        <div class="chart-bars-wrapper">
                            <div class="chart-bar primary" style="height: {{ max(4, ($stat['trainings'] * 15)) }}px;" title="{{ $stat['trainings'] }} trainings"></div>
                            <div class="chart-bar accent" style="height: {{ max(4, ($stat['registrations'] * 5)) }}px;" title="{{ $stat['registrations'] }} registrations"></div>
                        </div>
                        <span class="chart-label">{{ $stat['month'] }}</span>
                    </div>
                    @endforeach
                </div>
                <div class="chart-legend">
                    <span><span class="legend-dot primary"></span> Trainings</span>
                    <span><span class="legend-dot accent"></span> Registrations</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Registrations -->
    <div class="card">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-clipboard-list" style="color:var(--primary-400);margin-right:0.5rem;"></i> Recent Registrations</h3>
            <a href="{{ route('registrations.index') }}" class="btn btn-sm btn-outline">View All</a>
        </div>
        <div class="card-body">
            @forelse($recentRegistrations as $reg)
            <div style="display:flex;align-items:center;justify-content:space-between;padding:0.75rem 0;border-bottom:1px solid var(--border-color);">
                <div>
                    <div style="font-weight:600;font-size:0.9375rem;">{{ $reg->user->name }}</div>
                    <div style="font-size:0.8125rem;color:var(--text-muted);">{{ $reg->training->title }}</div>
                </div>
                <span class="badge-status badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span>
            </div>
            @empty
            <div class="empty-state"><p>No registrations yet</p></div>
            @endforelse
        </div>
    </div>
</div>

<!-- Recent Trainings -->
<div class="card mt-3">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list" style="color:var(--primary-400);margin-right:0.5rem;"></i> Recent Trainings</h3>
        <a href="{{ route('trainings.index') }}" class="btn btn-sm btn-outline">View All</a>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Registrations</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($recentTrainings as $training)
                <tr>
                    <td style="font-weight:600;color:var(--text-primary);">{{ $training->title }}</td>
                    <td>{{ $training->start_date->format('d M Y') }}</td>
                    <td><span class="badge-status badge-{{ $training->status }}">{{ ucfirst($training->status) }}</span></td>
                    <td>{{ $training->registrations_count ?? $training->registrations->count() }}</td>
                    <td>
                        <div class="action-group">
                            <a href="{{ route('trainings.show', $training) }}" class="btn btn-sm btn-outline"><i class="fas fa-eye"></i></a>
                            <a href="{{ route('attendance.generate', $training) }}" class="btn btn-sm btn-outline"><i class="fas fa-qrcode"></i></a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center" style="padding:2rem;">No trainings yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .dashboard-grid { grid-template-columns: 1fr !important; }
}
</style>
@endsection
