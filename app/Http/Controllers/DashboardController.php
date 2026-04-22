<?php

namespace App\Http\Controllers;

use App\Models\Training;
use App\Models\User;
use App\Models\Registration;
use App\Models\Certificate;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data = [];

        if ($user->isAdmin()) {
            $data = [
                'totalTrainings' => Training::count(),
                'totalUsers' => User::count(),
                'pendingRegistrations' => Registration::where('status', 'pending')->count(),
                'completedTrainings' => Training::where('status', 'completed')->count(),
                'ongoingTrainings' => Training::where('status', 'ongoing')->count(),
                'recentTrainings' => Training::latest()->take(5)->get(),
                'recentRegistrations' => Registration::with(['user', 'training'])->latest()->take(5)->get(),
                'monthlyStats' => $this->getMonthlyStats(),
            ];
            return view('dashboard.admin', $data);
        }

        if ($user->isFaculty()) {
            $data = [
                'myTrainings' => Training::where('created_by', $user->id)->latest()->get(),
                'totalCreated' => Training::where('created_by', $user->id)->count(),
                'pendingApprovals' => Registration::whereHas('training', fn($q) => $q->where('created_by', $user->id))
                    ->where('status', 'pending')->count(),
            ];
            return view('dashboard.faculty', $data);
        }

        if ($user->isStudent()) {
            $data = [
                'myRegistrations' => Registration::with('training')->where('user_id', $user->id)->latest()->take(5)->get(),
                'upcomingTrainings' => Training::where('status', 'published')
                    ->where('start_date', '>', now())->latest()->take(5)->get(),
                'certificatesCount' => Certificate::where('user_id', $user->id)->where('status', 'available')->count(),
                'registeredCount' => Registration::where('user_id', $user->id)->count(),
            ];
            return view('dashboard.student', $data);
        }

        // Lecturer
        $data = [
            'availableTrainings' => Training::where('status', 'published')->latest()->take(10)->get(),
            'notifications' => $user->notifications()->latest()->take(10)->get(),
        ];
        return view('dashboard.lecturer', $data);
    }

    private function getMonthlyStats(): array
    {
        $stats = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $stats[] = [
                'month' => $date->format('M'),
                'trainings' => Training::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count(),
                'registrations' => Registration::whereMonth('created_at', $date->month)->whereYear('created_at', $date->year)->count(),
            ];
        }
        return $stats;
    }
}
