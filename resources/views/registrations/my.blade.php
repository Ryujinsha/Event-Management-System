@extends('layouts.app')
@section('title', 'My Registrations')

@section('content')
<div class="section-header">
    <h3 class="section-title">My Registrations</h3>
</div>

<div class="card">
    <div class="table-container">
        <table class="table">
            <thead>
                <tr><th>Training</th><th>Reg Number</th><th>Date</th><th>Training Date</th><th>Status</th></tr>
            </thead>
            <tbody>
                @forelse($registrations as $reg)
                <tr>
                    <td>
                        <a href="{{ route('trainings.show', $reg->training) }}" class="link" style="font-weight:600;">
                            {{ $reg->training->title }}
                        </a>
                    </td>
                    <td><code style="color:var(--primary-400);">{{ $reg->registration_number }}</code></td>
                    <td>{{ $reg->created_at->format('d M Y') }}</td>
                    <td>{{ $reg->training->start_date->format('d M Y, H:i') }}</td>
                    <td><span class="badge-status badge-{{ $reg->status }}">{{ ucfirst($reg->status) }}</span></td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center" style="padding:3rem;">
                        <div class="empty-state">
                            <i class="fas fa-clipboard"></i>
                            <h3>No registrations yet</h3>
                            <p>Browse available trainings to register</p>
                            <a href="{{ route('trainings.index') }}" class="btn btn-primary mt-2"><i class="fas fa-search"></i> Browse Trainings</a>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="pagination-wrapper">{{ $registrations->links() }}</div>
@endsection
