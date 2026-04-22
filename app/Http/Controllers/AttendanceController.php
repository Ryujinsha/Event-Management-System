<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Training;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class AttendanceController extends Controller
{
    public function generate(Training $training)
    {
        return view('attendance.generate', compact('training'));
    }

    public function generateQR(Request $request, Training $training)
    {
        $request->validate([
            'duration' => 'required|integer|min:5|max:480',
        ]);

        $token = Str::random(32);
        $training->update([
            'qr_token' => $token,
            'qr_expires_at' => now()->addMinutes($request->duration),
        ]);

        $qrUrl = route('attendance.checkin.form', ['token' => $token]);

        return back()->with([
            'qr_generated' => true,
            'qr_url' => $qrUrl,
            'qr_token' => $token,
            'expires_at' => $training->fresh()->qr_expires_at->toIso8601String(),
        ]);
    }

    public function showScanner()
    {
        return view('attendance.scan');
    }

    public function checkinForm(Request $request)
    {
        $token = $request->get('token');
        $training = Training::where('qr_token', $token)->first();

        if (!$training) {
            return view('attendance.result', ['success' => false, 'message' => 'Invalid QR code.']);
        }

        if (!$training->isQrValid()) {
            return view('attendance.result', ['success' => false, 'message' => 'QR code has expired.']);
        }

        return view('attendance.confirm', compact('training', 'token'));
    }

    public function checkIn(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        $user = auth()->user();
        $training = Training::where('qr_token', $request->token)->first();

        if (!$training) {
            return back()->with('error', 'Invalid QR code.');
        }

        if (!$training->isQrValid()) {
            return back()->with('error', 'QR code has expired.');
        }

        // Check if user is registered and accepted
        $isRegistered = Registration::where('user_id', $user->id)
            ->where('training_id', $training->id)
            ->where('status', 'accepted')
            ->exists();

        if (!$isRegistered) {
            return view('attendance.result', [
                'success' => false,
                'message' => 'You are not registered or not accepted for this training.',
            ]);
        }

        // Check duplicate
        $alreadyCheckedIn = Attendance::where('user_id', $user->id)
            ->where('training_id', $training->id)
            ->exists();

        if ($alreadyCheckedIn) {
            return view('attendance.result', [
                'success' => false,
                'message' => 'You have already checked in for this training.',
            ]);
        }

        Attendance::create([
            'user_id' => $user->id,
            'training_id' => $training->id,
            'checked_in_at' => now(),
            'ip_address' => $request->ip(),
        ]);

        return view('attendance.result', [
            'success' => true,
            'message' => 'Attendance recorded successfully!',
            'training' => $training,
        ]);
    }

    public function list(Training $training)
    {
        $attendances = Attendance::with('user')
            ->where('training_id', $training->id)
            ->orderBy('checked_in_at')
            ->get();

        return view('attendance.list', compact('training', 'attendances'));
    }
}
