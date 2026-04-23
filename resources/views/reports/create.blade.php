@extends('layouts.app')
@section('title', 'Create Report')

@section('content')
<div style="margin-bottom:1.5rem;">
    <a href="{{ route('events.show', $event) }}" class="link"><i class="fas fa-arrow-left"></i> Back to {{ $event->title }}</a>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-file-alt" style="color:var(--primary-400);margin-right:0.5rem;"></i> Create Report</h3>
    </div>
    <div class="card-body">
        <div class="detail-grid mb-3">
            <div class="detail-item">
                <div class="detail-label">Event</div>
                <div class="detail-value">{{ $event->title }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Total Registered</div>
                <div class="detail-value">{{ $totalParticipants }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Total Attended</div>
                <div class="detail-value">{{ $totalAttended }}</div>
            </div>
            <div class="detail-item">
                <div class="detail-label">Attendance Rate</div>
                <div class="detail-value">{{ $totalParticipants > 0 ? round(($totalAttended / $totalParticipants) * 100) : 0 }}%</div>
            </div>
        </div>

        <form method="POST" action="{{ route('reports.store', $event) }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Report Title *</label>
                <input type="text" name="title" class="form-input" value="{{ old('title', 'Event Report — ' . $event->title) }}" required>
            </div>
            <div class="form-group">
                <label class="form-label">Report Content *</label>
                <textarea name="content" class="form-input" rows="8" placeholder="Write your detailed report..." required>{{ old('content') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Summary (optional)</label>
                <textarea name="summary" class="form-input" rows="3" placeholder="Brief summary...">{{ old('summary') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Save Report</button>
        </form>
    </div>
</div>
@endsection
