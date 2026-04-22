<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Training;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = Registration::with(['user', 'training']);

        if ($trainingId = $request->get('training_id')) {
            $query->where('training_id', $trainingId);
        }

        if ($status = $request->get('status')) {
            $query->where('status', $status);
        }

        $registrations = $query->latest()->paginate(20);
        $trainings = Training::all();
        return view('registrations.index', compact('registrations', 'trainings'));
    }

    public function store(Request $request, Training $training)
    {
        $user = auth()->user();

        // Check if already registered
        $exists = Registration::where('user_id', $user->id)
            ->where('training_id', $training->id)
            ->exists();

        if ($exists) {
            return back()->with('error', 'You have already registered for this training.');
        }

        // Check quota
        if ($training->isFull()) {
            return back()->with('error', 'This training is full. No more slots available.');
        }

        // Check training status
        if ($training->status !== 'published') {
            return back()->with('error', 'This training is not open for registration.');
        }

        $registration = Registration::create([
            'registration_number' => Registration::generateRegistrationNumber(),
            'user_id' => $user->id,
            'training_id' => $training->id,
            'status' => 'pending',
        ]);

        return back()->with('success', "Registration successful! Your registration number is: {$registration->registration_number}");
    }

    public function updateStatus(Request $request, Registration $registration)
    {
        $request->validate([
            'status' => 'required|in:accepted,rejected',
        ]);

        $registration->update(['status' => $request->status]);

        NotificationService::notifyRegistrationStatus(
            $registration->user,
            $registration->training,
            $request->status
        );

        return back()->with('success', "Registration {$request->status} successfully!");
    }

    public function myRegistrations()
    {
        $registrations = Registration::with('training')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('registrations.my', compact('registrations'));
    }
}
