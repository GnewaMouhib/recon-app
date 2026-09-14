<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Target;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TargetController extends Controller
{
    public function store(Request $request, Project $project)
    {
        $this->authorizeProject($project);

        $validated = $request->validate([
            'domain' => [
                'required',
                'string',
                'max:255',
                'regex:/^([a-zA-Z0-9-]+\.)+[a-zA-Z]{2,}$/'
            ],
            'url' => ['nullable', 'url', 'max:255'],
            'is_authorized' => ['accepted'],
            'authorization_note' => ['nullable', 'string', 'max:1000'],
        ], [
            'domain.regex' => 'Le domaine doit être valide, exemple : example.com',
            'is_authorized.accepted' => 'Vous devez confirmer que vous avez l\'autorisation de tester cette cible.',
        ]);

        Target::create([
            'project_id' => $project->id,
            'domain' => $validated['domain'],
            'url' => $validated['url'] ?? null,
            'is_authorized' => true,
            'authorization_note' => $validated['authorization_note'] ?? null,
        ]);

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Cible ajoutée avec succès.');
    }

    public function destroy(Target $target)
    {
        $project = $target->project;
        $this->authorizeProject($project);
        $target->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Cible supprimée.');
    }

    private function authorizeProject(Project $project): void
    {
        $user = Auth::user();

        if (!$user->isAdmin() && $project->user_id !== $user->id) {
            abort(403, 'Accès refusé.');
        }
    }
}