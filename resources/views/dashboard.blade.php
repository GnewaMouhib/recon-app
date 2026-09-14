<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Recon Pentest
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if(session('success'))
                <div class="mb-4 p-4 bg-green-100 text-green-800 rounded">
                    {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                <div class="p-5 bg-white shadow rounded">
                    <h3 class="text-sm text-gray-500">Projets</h3>
                    <p class="text-3xl font-bold">{{ $projectsCount }}</p>
                </div>

                <div class="p-5 bg-white shadow rounded">
                    <h3 class="text-sm text-gray-500">Cibles</h3>
                    <p class="text-3xl font-bold">{{ $targetsCount }}</p>
                </div>

                <div class="p-5 bg-white shadow rounded">
                    <h3 class="text-sm text-gray-500">Scans</h3>
                    <p class="text-3xl font-bold">{{ $scansCount }}</p>
                </div>

                <div class="p-5 bg-white shadow rounded">
                    <h3 class="text-sm text-gray-500">High</h3>
                    <p class="text-3xl font-bold text-orange-600">{{ $highCount }}</p>
                </div>

                <div class="p-5 bg-white shadow rounded">
                    <h3 class="text-sm text-gray-500">Critical</h3>
                    <p class="text-3xl font-bold text-red-600">{{ $criticalCount }}</p>
                </div>
            </div>

            <div class="mb-6">
                <a href="{{ route('projects.create') }}" class="inline-block px-4 py-2 bg-indigo-600 text-white font-semibold rounded hover:bg-indigo-700">
                    Nouveau projet
                </a>
            </div>

            <div class="bg-white shadow rounded p-6">
                <h3 class="text-lg font-bold mb-4">Derniers projets</h3>
                @forelse ($latestProjects as $project)
                    <div class="border-b py-3 flex justify-between">
                        <div>
                            <p class="font-semibold">{{ $project->name }}</p>
                            <p class="text-sm text-gray-500">
                                {{ $project->description ?? 'Aucune description' }}
                            </p>
                        </div>
                        <a href="{{ route('projects.show', $project) }}" class="text-indigo-600 hover:underline">
                            Voir
                        </a>
                    </div>
                @empty
                    <p class="text-gray-500">Aucun projet pour le moment.</p>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>