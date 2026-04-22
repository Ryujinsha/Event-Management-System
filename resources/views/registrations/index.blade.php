@extends('layouts.app')
@section('title', 'Manage Registrations')

@section('content')
<div class="section-header">
    <h3 class="section-title">Manage Registrations</h3>
</div>

<div class="filter-bar">
    <form method="GET" action="{{ route('registrations.index') }}" style="display:flex;gap:0.75rem;flex-wrap:wrap;">
        <select name="training_id" class="form-input" style="max-width:300px;" onchange="this.form.submit()">
            <option value="">All Trainings</option>
            @foreach($trainings as $t)
            <option value="{{ $t->id }}" {{ request('training_id') == $t->id ? 'selected' : '' }}>{{ $t->title }}</option>
            @endforeach
        </select>
        <select name="status" class="form-input" style="max-width:180px;" onchange="this.form.submit()">
            <option value="">All Status</option>
            @foreach(['pending','accepted','rejected'] as $s)
            <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
    </form>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr><th>Student</th><th>Training</th><th>Reg Number</th><th>Date</th><th>Status</th><th>Actions</th></tr>
            </thead>
            <tbody>
                @forelse($registrations as $reg)
                <tr>
                    <td>
                        <div style="font-weight:600;color:var(--text-primary);">{{ $reg->user->name }}</div>
                        <div style="font-size:0.75rem;color:var(--text-muted);">{{ $reg->user->email }}</div>
                    </td>
                    <td>{{ $reg->training->title }}</td>
                    <td><code style="color:var(--primary-400);">{{ $reg->registration_number }}</code></td>
                    <td>{{ $reg->created_at->format('d M Y') }}</td>
                    <td><span class="badge-status badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span></td>
                    <td>
                        @if($reg->status === 'pending')
                        <div class="action-group">
                            <form method="POST" action="{{ route('registrations.updateStatus', $reg) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="accepted">
                                <button class="btn btn-sm btn-success" title="Accept"><i class="fas fa-check"></i></button>
                            </form>
                            <form method="POST" action="{{ route('registrations.updateStatus', $reg) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button class="btn btn-sm btn-danger" title="Reject"><i class="fas fa-times"></i></button>
                            </form>
                        </div>
                        @else
                        <span style="color:var(--text-muted);">—</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center" style="padding:2rem;color:var(--text-muted);">No registrations found</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">{{ $registrations->withQueryString()->links() }}</div>
@endsection
