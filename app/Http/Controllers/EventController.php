<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class EventController extends Controller
{
    public function index(Request $request)
    {
        $query = Event::with('creator')->withCount('participants');

        if ($search = $request->get('search')) {
            $query->where('title', 'like', "%{$search}%");
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $events = $query->latest()->paginate(12);
        return view('events.index', compact('events'));
    }

    public function create()
    {
        return view('events.create');
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
            'status' => 'required|in:draft,pending_approval',
            'materials' => 'nullable|array',
            'materials.*.title' => 'nullable|string|max:255',
            'materials.*.description' => 'nullable|string',
        ]);

        $validated['created_by'] = auth()->id();
        $event = Event::create($validated);

        if ($request->has('materials')) {
            foreach ($request->materials as $material) {
                if (!empty($material['title'])) {
                    $event->materials()->create($material);
                }
            }
        }

        if ($event->status === 'pending_approval') {
            // Notify Head Department here (simplified)
            // NotificationService::notifyApprovalRequest($event);
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Event created successfully!');
    }

    public function show(Event $event)
    {
        $event->load(['creator', 'participants.user', 'attendances.user']);
        $userParticipant = null;
        if (auth()->check()) {
            $userParticipant = $event->participants()->where('user_id', auth()->id())->first();
        }
        return view('events.show', compact('event', 'userParticipant'));
    }

    public function edit(Event $event)
    {
        $this->authorizeEvent($event);
        return view('events.edit', compact('event'));
    }

    public function update(Request $request, Event $event)
    {
        $this->authorizeEvent($event);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'location' => 'required|string|max:255',
            'quota' => 'required|integer|min:1',
            'status' => 'required|in:draft,pending_approval,approved,published,ongoing,completed,cancelled',
        ]);

        $wasNotPublished = $event->status !== 'published';
        $event->update($validated);

        if ($wasNotPublished && $event->status === 'published') {
            NotificationService::notifyEventPublished($event);
        }

        return redirect()->route('events.show', $event)
            ->with('success', 'Event updated successfully!');
    }

    public function destroy(Event $event)
    {
        $this->authorizeEvent($event);
        $event->delete();
        return redirect()->route('events.index')
            ->with('success', 'Event deleted successfully!');
    }

    private function authorizeEvent(Event $event)
    {
        $user = auth()->user();
        if (!$user->isAdmin() && !$user->isCommittee() && !$user->isHeadDepartment()) {
            abort(403);
        }
        if ($user->isCommittee() && $event->created_by !== $user->id) {
            abort(403);
        }
    }
}
