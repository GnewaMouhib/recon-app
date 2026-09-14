<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    {{ $project->name }}
                </h2>
                <span class="inline-block mt-1 px-2 py-0.5 text-xs rounded font-semibold {{ $project->status === 'active' ? 'bg-green-100 text-green-800' : 'bg-gray-100 text-gray-800' }}">
                    {{ ucfirst($project->status) }}
                </span>
            </div>
            <div class="space-x-2">
                <a href="{{ route('projects.edit', $project) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">Modifier</a>
                <a href="{{ route('projects.index') }}" class="px-4 py-2 text-gray-600 hover:underline">Retour</a>
            </div>
        </div>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if(session('success'))
                <div class="p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            @if($project->description)
                <div class="bg-white shadow rounded p-6">
                    <h3 class="text-sm font-medium text-gray-500 uppercase tracking-wider mb-2">Description</h3>
                    <p class="text-gray-800 whitespace-pre-line">{{ $project->description }}</p>
                </div>
            @endif

            <!-- Formulaire d'ajout de cible -->
            <div class="bg-white shadow rounded p-6">
                <h3 class="text-lg font-bold mb-4">Ajouter une Cible</h3>
                <form action="{{ route('targets.store', $project) }}" method="POST">
                    @csrf

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        <div>
                            <label for="domain" class="block text-sm font-medium text-gray-700 mb-1">Domaine * (ex: example.com)</label>
                            <input type="text" name="domain" id="domain" value="{{ old('domain') }}" required placeholder="example.com"
                                class="w-full border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('domain')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="url" class="block text-sm font-medium text-gray-700 mb-1">URL Complète</label>
                            <input type="url" name="url" id="url" value="{{ old('url') }}" placeholder="https://example.com"
                                class="w-full border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            @error('url')
                                <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="authorization_note" class="block text-sm font-medium text-gray-700 mb-1">Note d'autorisation</label>
                        <textarea name="authorization_note" id="authorization_note" rows="2" placeholder="Référence du mandat ou accord..."
                            class="w-full border-gray-300 rounded shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('authorization_note') }}</textarea>
                        @error('authorization_note')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mb-4">
                        <label class="flex items-center">
                            <input type="checkbox" name="is_authorized" value="1" {{ old('is_authorized') ? 'checked' : '' }}
                                class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Je certifie avoir l'autorisation de scanner cette cible. *</span>
                        </label>
                        @error('is_authorized')
                            <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700">Ajouter la cible</button>
                </form>
            </div>

            <!-- Liste des cibles -->
            <div class="bg-white shadow rounded overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-bold">Cibles associées ({{ $project->targets->count() }})</h3>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Domaine</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Autorisé</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Scans Récents</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($project->targets as $target)
                            <tr>
                                <td class="px-6 py-4 font-semibold text-gray-900">{{ $target->domain }}</td>
                                <td class="px-6 py-4 text-sm text-gray-500">{{ $target->url ?? '—' }}</td>
                                <td class="px-6 py-4 text-sm">
                                    <span class="px-2 py-1 text-xs rounded font-semibold {{ $target->is_authorized ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $target->is_authorized ? 'Oui' : 'Non' }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    <div class="flex flex-wrap gap-1">
                                        @forelse($target->scans->take(3) as $s)
                                            <a href="{{ route('scans.show', $s) }}" class="px-2 py-0.5 text-xs rounded border hover:bg-gray-100">
                                                {{ $s->type }} ({{ $s->status }})
                                            </a>
                                        @empty
                                            <span class="text-xs text-gray-400">Aucun scan</span>
                                        @endforelse
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-right text-sm">
                                    <div class="flex items-center justify-end space-x-2">
                                        <!-- Formulaire de lancement de Scan (Sprint 2) -->
                                        <form action="{{ route('scans.store', $target) }}" method="POST" class="inline-flex space-x-1">
                                            @csrf
                                            <select name="type" class="text-xs border-gray-300 rounded shadow-sm py-1">
                                                <option value="technology">Technologies (HTTPX)</option>
                                                <option value="subdomain">Sous-domaines (Subfinder)</option>
                                                <option value="port">Ports (Nmap)</option>
                                                <option value="directory">Répertoires (Gobuster)</option>
                                                <option value="vulnerability">Vulnérabilités (Nuclei)</option>
                                                <option value="wordpress">WordPress (WPScan)</option>
                                            </select>
                                            <button type="submit" class="px-2 py-1 bg-indigo-600 text-white text-xs font-semibold rounded hover:bg-indigo-700">
                                                Scanner
                                            </button>
                                        </form>

                                        <!-- Bouton Suppression Cible -->
                                        <form action="{{ route('targets.destroy', $target) }}" method="POST" class="inline" onsubmit="return confirm('Supprimer cette cible ?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:underline text-xs">Supprimer</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                                    Aucune cible ajoutée pour ce projet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>