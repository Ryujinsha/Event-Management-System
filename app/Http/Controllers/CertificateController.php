<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Registration;
use App\Models\Training;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    public function index()
    {
        $certificates = Certificate::with('training')
            ->where('user_id', auth()->id())
            ->latest()
            ->paginate(10);

        return view('certificates.index', compact('certificates'));
    }

    public function manage(Training $training)
    {
        $certificates = Certificate::with('user')
            ->where('training_id', $training->id)
            ->get();

        $acceptedRegistrations = Registration::with('user')
            ->where('training_id', $training->id)
            ->where('status', 'accepted')
            ->get();

        return view('certificates.manage', compact('training', 'certificates', 'acceptedRegistrations'));
    }

    public function activate(Training $training)
    {
        $acceptedRegistrations = Registration::with('user')
            ->where('training_id', $training->id)
            ->where('status', 'accepted')
            ->get();

        foreach ($acceptedRegistrations as $registration) {
            $existing = Certificate::where('user_id', $registration->user_id)
                ->where('training_id', $training->id)
                ->first();

            if (!$existing) {
                Certificate::create([
                    'certificate_number' => Certificate::generateCertificateNumber(),
                    'user_id' => $registration->user_id,
                    'training_id' => $training->id,
                    'status' => 'available',
                ]);

                NotificationService::notifyCertificateAvailable($registration->user, $training);
            }
        }

        return back()->with('success', 'Certificates activated and notifications sent!');
    }

    public function download(Certificate $certificate)
    {
        if ($certificate->user_id !== auth()->id() && !auth()->user()->isAdmin()) {
            abort(403);
        }

        $certificate->load(['user', 'training']);

        $pdf = Pdf::loadView('certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'landscape');

        return $pdf->download("certificate-{$certificate->certificate_number}.pdf");
    }
}
