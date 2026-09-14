<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProjectController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $projects = Project::query()
            ->when(!$user->isAdmin(), function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
        ]);

        Project::create([
            'user_id' => Auth::id(),
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'status' => 'active',
        ]);

        return redirect()
            ->route('projects.index')
            ->with('success', 'Projet créé avec succès.');
    }

    public function show(Project $project)
    {
        $this->authorizeProject($project);
        $project->load('targets.scans.results');

        return view('projects.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorizeProject($project);

        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'description' => ['nullable', 'string'],
            'status' => ['required', 'in:active,archived'],
        ]);

        $project->update($validated);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Projet mis à jour.');
    }

    public function destroy(Project $project)
    {
        $this->authorizeProject($project);
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('success', 'Projet supprimé.');
    }

    private function authorizeProject(Project $project): void
    {
        $user = Auth::user();

        if (!$user->isAdmin() && $project->user_id !== $user->id) {
            abort(403, 'Accès refusé.');
        }
    }
}