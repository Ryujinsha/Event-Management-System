<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class TrainingController extends Controller
{
    public function index(Request $request)
    {
        $query = Training::with('creator')->withCount('registrations');

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $trainings = $query->latest()->paginate(12);
        return view('trainings.index', compact('trainings'));
    }

    public function create()
    {
        return view('trainings.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date|after:now',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'status' => 'required|in:draft,published',
        ]);

        $validated['created_by'] = auth()->id();
        $training = Training::create($validated);

        if ($training->status === 'published') {
            NotificationService::notifyTrainingPublished($training);
        }

        return redirect()->route('trainings.show', $training)
            ->with('success', 'Training created successfully!');
    }

    public function show(Training $training)
    {
        $training->load(['creator', 'registrations.user', 'attendances.user']);
        $userRegistration = null;
        if (auth()->check()) {
            $userRegistration = $training->registrations()->where('user_id', auth()->id())->first();
        }
        return view('trainings.show', compact('training', 'userRegistration'));
    }

    public function edit(Training $training)
    {
        $this->authorizeTraining($training);
        return view('trainings.edit', compact('training'));
    }

    public function update(Request $request, Training $training)
    {
        $this->authorizeTraining($training);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'status' => 'required|in:draft,published,ongoing,completed,cancelled',
        ]);

        $wasNotPublished = $training->status !== 'published';
        $training->update($validated);

        if ($wasNotPublished && $training->status === 'published') {
            NotificationService::notifyTrainingPublished($training);
        }

        return redirect()->route('trainings.show', $training)
            ->with('success', 'Training updated successfully!');
    }

    public function destroy(Training $training)
    {
        $this->authorizeTraining($training);
        $training->delete();
        return redirect()->route('trainings.index')
            ->with('success', 'Training deleted successfully!');
    }

    private function authorizeTraining(Training $training)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isFaculty()) {
            abort(403);
        }
        if ($user->isFaculty() && $training->created_by !== $user->id) {
            abort(403);
        }
    }
}
