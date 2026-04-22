@extends('layouts.app')
@section('title', 'Edit Training')

@section('content')
<div style="margin-bottom:1.5rem;">
    <a href="{{ route('trainings.show', $training) }}" class="link"><i class="fas fa-arrow-left"></i> Back to training</a>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-header">
        <h3 class="card-title"><i class="fas fa-edit" style="color:var(--primary-400);margin-right:0.5rem;"></i> Edit Training</h3>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('trainings.update', $training) }}">
            @csrf @method('PUT')
            <div class="form-group">
                <label class="form-label" for="title">Training Title *</label>
                <input type="text" id="title" name="title" class="form-input" value="{{ old('title', $training->title) }}" required>
            </div>

            <div class="form-group">
                <label class="form-label" for="description">Description *</label>
                <textarea id="description" name="description" class="form-input" rows="5" required>{{ old('description', $training->description) }}</textarea>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="start_date">Start Date & Time *</label>
                    <input type="datetime-local" id="start_date" name="start_date" class="form-input" value="{{ old('start_date', $training->start_date->format('Y-m-d\TH:i')) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="end_date">End Date & Time *</label>
                    <input type="datetime-local" id="end_date" name="end_date" class="form-input" value="{{ old('end_date', $training->end_date->format('Y-m-d\TH:i')) }}" required>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label class="form-label" for="location">Location *</label>
                    <input type="text" id="location" name="location" class="form-input" value="{{ old('location', $training->location) }}" required>
                </div>
                <div class="form-group">
                    <label class="form-label" for="quota">Quota *</label>
                    <input type="number" id="quota" name="quota" class="form-input" value="{{ old('quota', $training->quota) }}" min="1" required>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="status">Status *</label>
                <select id="status" name="status" class="form-input">
                    @foreach(['draft','published','ongoing','completed','cancelled'] as $s)
                    <option value="{{ $s }}" {{ old('status', $training->status) === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="action-group mt-2">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-save"></i> Update Training</button>
                <a href="{{ route('trainings.show', $training) }}" class="btn btn-outline">Cancel</a>
            </div>
        </form>

        <div style="margin-top:2rem;padding-top:1.5rem;border-top:1px solid var(--border-color);">
            <form method="POST" action="{{ route('trainings.destroy', $training) }}" onsubmit="return confirm('Are you sure you want to delete this training?')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger"><i class="fas fa-trash"></i> Delete Training</button>
            </form>
        </div>
    </div>
</div>
@endsection
