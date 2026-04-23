@extends('layouts.app')
@section('title', 'Attendance List')

@section('content')
<div style="margin-bottom:1.5rem;">
    <a href="{{ route('events.show', $event) }}" class="link"><i class="fas fa-arrow-left"></i> Back to {{ $event->title }}</a>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-list-check" style="color:var(--primary-400);margin-right:0.5rem;"></i> Attendance — {{ $event->title }}</h3>
        <span class="badge-status badge-{{ $event->status }}">{{ $attendances->count() }} checked in</span>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr><th>#</th><th>Name</th><th>Email</th><th>Checked In At</th><th>IP Address</th></tr>
            </thead>
            <tbody>
                @forelse($attendances as $i => $att)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td style="font-weight:600;color:var(--text-primary);">{{ $att->user->name }}</td>
                    <td>{{ $att->user->email }}</td>
                    <td>{{ $att->checked_in_at->format('d M Y, H:i:s') }}</td>
                    <td style="color:var(--text-muted);">{{ $att->ip_address ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center" style="padding:2rem;color:var(--text-muted);">No attendance records yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
