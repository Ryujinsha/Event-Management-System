@extends('layouts.app')
@section('title', 'Create Training')

@section('content')
<div style="margin-bottom:1.5rem;">
    <a href="{{ route('trainings.index') }}" class="link"><i class="fas fa-arrow-left"></i> Back to trainings</a>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-plus-circle" style="color:var(--primary-400);margin-right:0.5rem;"></i> Create New Training</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('trainings.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label" for="title">Training Title *</label>
                <input type="text" id="title" name="title" class="form-input" value="{{ old('title') }}" placeholder="e.g. Web Development Workshop" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description *</label>
                <textarea id="description" name="description" class="form-input" rows="5" placeholder="Describe the training objectives, prerequisites, and agenda..." required>{{ old('description') }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="start_date">Start Date & Time *</label>
                    <input type="datetime-local" id="start_date" name="start_date" class="form-input" value="{{ old('start_date') }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="end_date">End Date & Time *</label>
                    <input type="datetime-local" id="end_date" name="end_date" class="form-input" value="{{ old('end_date') }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="location">Location *</label>
                    <input type="text" id="location" name="location" class="form-input" value="{{ old('location') }}" placeholder="e.g. Room A-301, Building B" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="quota">Participant Quota *</label>
                    <input type="number" id="quota" name="quota" class="form-input" value="{{ old('quota', 30) }}" min="1" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status *</label>
                <select id="status" name="status" class="form-input">
                    <option value="draft" {{ old('status') === 'draft' ? 'selected' : '' }}>Draft (save without publishing)</option>
                    <option value="published" {{ old('status') === 'published' ? 'selected' : '' }}>Published (open for registration)</option>
                </select>
            </div>

            <div class="action-group mt-2">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Create Training</button>
                <a href="{{ route('trainings.index') }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
