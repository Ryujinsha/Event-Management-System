<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Participant;
use App\Models\Event;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('event')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('certificates.index', compact('certificates'));
    }

    public function manage(Event $event)
    {
        $certificates = Certificate::with('user')
            ->where('event_id', $event->id)
            ->get();

        $acceptedParticipants = Participant::with('user')
            ->where('event_id', $event->id)
            ->where('status', 'accepted')
            ->get();

        return view('certificates.manage', compact('event', 'certificates', 'acceptedParticipants'));
    }

    public function activate(Event $event)
    {
        if ($event->status !== 'completed') {
            return back()->with('error', 'Certificates can only be generated after the event is completed.');
        }

        $acceptedParticipants = Participant::with('user')
            ->where('event_id', $event->id)
            ->where('status', 'accepted')
            ->get();

        foreach ($acceptedParticipants as $participant) {
            $existing = Certificate::where('user_id', $participant->user_id)
                ->where('event_id', $event->id)
                ->first();

            if (!$existing) {
                Certificate::create([
                    'certificate_number' => Certificate::generateCertificateNumber(),
                    'user_id' => $participant->user_id,
                    'event_id' => $event->id,
                    'status' => 'available',
                ]);

                NotificationService::notifyCertificateAvailable($participant->user, $event);
            }
        }

        return back()->with('success', 'Certificates activated and notifications sent!');
    }

    public function download(Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $certificate->load(['user', 'event']);

        $pdf = Pdf::loadView('certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("certificate-{$certificate->certificate_number}.pdf");
    }
}
