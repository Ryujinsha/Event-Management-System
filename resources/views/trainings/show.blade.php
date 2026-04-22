@extends('layouts.app')
@section('title', $training->title)

@section('content')
<div style="margin-bottom:1.5rem;">
    <a href="{{ route('trainings.index') }}" class="link"><i class="fas fa-arrow-left"></i> Back to trainings</a>
</div>

<div class="card mb-3">
    <div class="card-header">
        <h3 class="card-title">{{ $training->title }}</h3>
        <span class="badge-status badge-{{ $training->status }}">{{ ucfirst($training->status) }}</span>
    </div>
    <div class="card-body">
        <div class="detail-grid mb-3">
            <div class="detail-item">
                <div class="detail-label">Start Date</div>
                <div class="detail-value">{{ $training->start_date->format('d M Y, H:i') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">End Date</div>
                <div class="detail-value">{{ $training->end_date->format('d M Y, H:i') }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Location</div>
                <div class="detail-value">{{ $training->location }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Quota</div>
                <div class="detail-value">{{ $training->acceptedRegistrations()->count() }} / {{ $training->quota }} slots</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Created By</div>
                <div class="detail-value">{{ $training->creator->name ?? 'N/A' }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Available Slots</div>
                <div class="detail-value" style="color:{{ $training->availableSlots() > 0 ? '#6ee7b7' : '#fca5a5' }};">
                    {{ $training->availableSlots() > 0 ? $training->availableSlots() . ' remaining' : 'Full' }}
                </div>
            </div>
        </div>

        <div style="margin-bottom:1.5rem;">
            <div class="detail-label" style="margin-bottom:0.75rem;">Description</div>
            <div style="color:var(--text-secondary);line-height:1.8;white-space:pre-line;">{{ $training->description }}</div>
        </div>

        <!-- Actions -->
        <div class="action-group">
            @if(auth()->user()->isStudent() && $training->status === 'published')
                @if($userRegistration)
                    <div class="alert alert-info" style="margin:0;">
                        <i class="fas fa-info-circle"></i>
                        You are already registered. Status: <strong>{{ ucfirst($userRegistration->status) }}</strong>
                        &mdash; Reg #{{ $userRegistration->registration_number }}
                    </div>
                @else
                    <form method="POST" action="{{ route('registrations.store', $training) }}">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-lg" {{ $training->isFull() ? 'disabled' : '' }}>
                            <i class="fas fa-clipboard-check"></i> {{ $training->isFull() ? 'Training Full' : 'Register Now' }}
                        </button>
                    </form>
                @endif
            @endif

            @if(auth()->user()->isAdmin() || (auth()->user()->isFaculty() && $training->created_by === auth()->id()))
                <a href="{{ route('trainings.edit', $training) }}" class="btn btn-outline"><i class="fas fa-edit"></i> Edit</a>
                <a href="{{ route('attendance.generate', $training) }}" class="btn btn-outline"><i class="fas fa-qrcode"></i> QR Attendance</a>
                <a href="{{ route('attendance.list', $training) }}" class="btn btn-outline"><i class="fas fa-list-check"></i> Attendance List</a>
                <a href="{{ route('reports.create', $training) }}" class="btn btn-outline"><i class="fas fa-file-alt"></i> Create Report</a>
                <a href="{{ route('certificates.manage', $training) }}" class="btn btn-outline"><i class="fas fa-award"></i> Certificates</a>
            @endif
        </div>
    </div>
</div>

@if(auth()->user()->isAdmin() || auth()->user()->isFaculty())
<!-- Registrations Table -->
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Registrations ({{ $training->registrations->count() }})</h3>
    </div>
    <div class="table-container">
        <table class="table">
            <thead>
                <tr><th>Name</th><th>Email</th><th>Reg #</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($training->registrations as $reg)
                <tr>
                    <td style="font-weight:600;color:var(--text-primary);">{{ $reg->user->name }}</td>
                    <td>{{ $reg->user->email }}</td>
                    <td><code style="color:var(--primary-400);">{{ $reg->registration_number }}</code></td>
                    <td><span class="badge-status badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span></td>
                    <td>
                        @if($reg->status === 'pending')
                        <div class="action-group">
                            <form method="POST" action="{{ route('registrations.updateStatus', $reg) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="accepted">
                                <button class="btn btn-sm btn-success"><i class="fas fa-check"></i></button>
                            </form>
                            <form method="POST" action="{{ route('registrations.updateStatus', $reg) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button class="btn btn-sm btn-danger"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                        @else
                        <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center" style="padding:2rem;color:var(--text-muted);">No registrations yet</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endif
@endsection
