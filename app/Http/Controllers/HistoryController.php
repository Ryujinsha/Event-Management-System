<?php

namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\Certificate;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        $registrations = Registration::with(['training'])
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->latest()
            ->get()
            ->map(function ($reg) use ($user) {
                $certificate = Certificate::where('user_id', $user->id)
                    ->where('training_id', $reg->training_id)
                    ->where('status', 'available')
                    ->first();

                $reg->certificate = $certificate;
                $reg->training_status = match (true) {
                    $reg->training->status === 'completed' && $certificate => 'certificate_available',
                    $reg->training->status === 'completed' => 'completed',
                    $reg->training->status === 'ongoing' => 'ongoing',
                    default => $reg->training->status,
                };

                return $reg;
            });

        return view('history.index', compact('registrations'));
    }
}
