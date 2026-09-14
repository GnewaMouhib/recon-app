<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Target;
use App\Models\Scan;
use App\Models\ScanResult;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $projectQuery = Project::query();

        if (!$user->isAdmin()) {
            $projectQuery->where('user_id', $user->id);
        }

        $projectIds = $projectQuery->pluck('id');
        $targetIds = Target::whereIn('project_id', $projectIds)->pluck('id');
        $scanIds = Scan::whereIn('target_id', $targetIds)->pluck('id');

        return view('dashboard', [
            'projectsCount' => $projectIds->count(),
            'targetsCount' => $targetIds->count(),
            'scansCount' => $scanIds->count(),
            'criticalCount' => ScanResult::whereIn('scan_id', $scanIds)
                ->where('severity', 'critical')
                ->count(),
            'highCount' => ScanResult::whereIn('scan_id', $scanIds)
                ->where('severity', 'high')
                ->count(),
            'latestProjects' => Project::whereIn('id', $projectIds)
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}