<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <div>
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Scan {{ ucfirst($scan->type) }} — {{ $scan->target->domain }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">
                    Projet : <a href="{{ route('projects.show', $scan->target->project) }}" class="text-indigo-600 hover:underline">{{ $scan->target->project->name }}</a>
                </p>
            </div>
            <div>
                <a href="{{ route('projects.show', $scan->target->project) }}" class="px-4 py-2 bg-gray-100 text-gray-700 rounded hover:bg-gray-200">
                    Retour au projet
                </a>
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

            <!-- Détails du Scan -->
            <div class="bg-white shadow rounded p-6 grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <h4 class="text-xs font-medium text-gray-500 uppercase">Statut</h4>
                    <span class="inline-block mt-1 px-2 py-1 text-xs rounded font-bold
                        {{ $scan->status === 'completed' ? 'bg-green-100 text-green-800' : '' }}
                        {{ $scan->status === 'running' ? 'bg-blue-100 text-blue-800' : '' }}
                        {{ $scan->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                        {{ $scan->status === 'failed' ? 'bg-red-100 text-red-800' : '' }}">
                        {{ ucfirst($scan->status) }}
                    </span>
                </div>

                <div>
                    <h4 class="text-xs font-medium text-gray-500 uppercase">Débuté à</h4>
                    <p class="mt-1 text-sm text-gray-800">{{ $scan->started_at ? $scan->started_at->format('d/m/Y H:i:s') : '—' }}</p>
                </div>

                <div>
                    <h4 class="text-xs font-medium text-gray-500 uppercase">Terminé à</h4>
                    <p class="mt-1 text-sm text-gray-800">{{ $scan->finished_at ? $scan->finished_at->format('d/m/Y H:i:s') : '—' }}</p>
                </div>

                <div>
                    <h4 class="text-xs font-medium text-gray-500 uppercase">Résultats trouvés</h4>
                    <p class="mt-1 text-2xl font-bold text-indigo-600">{{ $scan->results->count() }}</p>
                </div>
            </div>

            @if($scan->error_message)
                <div class="bg-red-50 border-l-4 border-red-400 p-4">
                    <h4 class="text-red-800 font-bold">Erreur rencontrée :</h4>
                    <p class="text-red-700 text-sm mt-1 whitespace-pre-wrap">{{ $scan->error_message }}</p>
                </div>
            @endif

            <!-- Liste des Résultats -->
            <div class="bg-white shadow rounded overflow-hidden">
                <div class="px-6 py-4 border-b">
                    <h3 class="text-lg font-bold">Découvertes & Vulnérabilités</h3>
                </div>

                <div class="divide-y divide-gray-200">
                    @forelse($scan->results as $result)
                        <div class="p-6">
                            <div class="flex items-center justify-between">
                                <h4 class="text-base font-semibold text-gray-900">{{ $result->title }}</h4>
                                <span class="px-2 py-1 text-xs rounded font-bold uppercase
                                    {{ $result->severity === 'critical' ? 'bg-red-600 text-white' : '' }}
                                    {{ $result->severity === 'high' ? 'bg-orange-500 text-white' : '' }}
                                    {{ $result->severity === 'medium' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $result->severity === 'low' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $result->severity === 'info' ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ $result->severity }}
                                </span>
                            </div>

                            @if($result->description)
                                <p class="text-sm text-gray-600 mt-2">{{ $result->description }}</p>
                            @endif

                            @if($result->recommendation)
                                <div class="mt-3 p-3 bg-gray-50 rounded text-xs text-gray-700">
                                    <span class="font-bold">Recommandation :</span> {{ $result->recommendation }}
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="p-6 text-center text-gray-500">
                            @if($scan->status === 'running' || $scan->status === 'pending')
                                Le scan est en cours de traitement. Rafraîchissez la page dans quelques instants.
                            @else
                                Aucun résultat trouvé ou le scan n'a rien détecté.
                            @endif
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>