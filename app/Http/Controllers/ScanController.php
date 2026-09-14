<?php

namespace App\Http\Controllers;

use App\Jobs\RunReconScanJob;
use App\Models\Scan;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScanController extends Controller
{
    public function store(Request $request, Target $target)
    {
        $this->authorizeProject($target->project);

        $validated = $request->validate([
            'type' => [
                'required',
                'in:subdomain,port,directory,vulnerability,technology,wordpress',
            ],
        ]);

        $scan = Scan::create([
            'target_id' => $target->id,
            'type'      => $validated['type'],
            'status'    => 'pending',
        ]);

        RunReconScanJob::dispatch($scan);

        return redirect()
            ->route('scans.show', $scan)
            ->with('success', 'Scan planifié et ajouté à la file d\'attente.');
    }

    public function show(Scan $scan)
    {
        $this->authorizeProject($scan->target->project);
        $scan->load(['target.project', 'results']);

        return view('scans.show', compact('scan'));
    }

    private function authorizeProject($project): void
    {
        $user = Auth::user();

        if (!$user->isAdmin() && $project->user_id !== $user->id) {
            abort(403, 'Accès refusé.');
        }
    }
}