<?php

namespace App\Http\Controllers;

use App\Models\Report;
use App\Models\Training;
use App\Models\Registration;
use App\Models\Attendance;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function create(Training $training)
    {
        $totalParticipants = Registration::where('training_id', $training->id)
            ->where('status', 'accepted')->count();
        $totalAttended = Attendance::where('training_id', $training->id)->count();

        return view('reports.create', compact('training', 'totalParticipants', 'totalAttended'));
    }

    public function store(Request $request, Training $training)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'summary' => 'nullable|string',
        ]);

        $validated['training_id'] = $training->id;
        $validated['created_by'] = auth()->id();
        $validated['total_participants'] = Registration::where('training_id', $training->id)
            ->where('status', 'accepted')->count();
        $validated['total_attended'] = Attendance::where('training_id', $training->id)->count();

        $report = Report::create($validated);

        return redirect()->route('reports.show', $report)
            ->with('success', 'Report created successfully!');
    }

    public function show(Report $report)
    {
        $report->load(['training', 'creator']);
        return view('reports.show', compact('report'));
    }

    public function exportPdf(Report $report)
    {
        $report->load(['training.registrations.user', 'training.attendances.user', 'creator']);
        $attendances = Attendance::with('user')->where('training_id', $report->training_id)->get();
        $registrations = Registration::with('user')->where('training_id', $report->training_id)->get();

        $pdf = Pdf::loadView('reports.pdf', compact('report', 'attendances', 'registrations'));
        return $pdf->download("report-{$report->training->title}.pdf");
    }

    public function exportCsv(Report $report)
    {
        $report->load('training');
        $attendances = Attendance::with('user')->where('training_id', $report->training_id)->get();
        $registrations = Registration::with('user')->where('training_id', $report->training_id)->get();

        $filename = "report-{$report->training->title}.csv";
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($report, $attendances, $registrations) {
            $file = fopen('php://output', 'w');

            // Report info
            fputcsv($file, ['Training Report']);
            fputcsv($file, ['Title', $report->title]);
            fputcsv($file, ['Training', $report->training->title]);
            fputcsv($file, ['Date', $report->training->start_date->format('Y-m-d')]);
            fputcsv($file, ['Total Participants', $report->total_participants]);
            fputcsv($file, ['Total Attended', $report->total_attended]);
            fputcsv($file, []);

            // Registrations
            fputcsv($file, ['Registrations']);
            fputcsv($file, ['Name', 'Email', 'Registration Number', 'Status']);
            foreach ($registrations as $reg) {
                fputcsv($file, [$reg->user->name, $reg->user->email, $reg->registration_number, $reg->status]);
            }
            fputcsv($file, []);

            // Attendance
            fputcsv($file, ['Attendance']);
            fputcsv($file, ['Name', 'Email', 'Checked In At']);
            foreach ($attendances as $att) {
                fputcsv($file, [$att->user->name, $att->user->email, $att->checked_in_at->format('Y-m-d H:i:s')]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
