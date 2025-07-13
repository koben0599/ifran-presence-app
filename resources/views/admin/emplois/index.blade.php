@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestion des Emplois du Temps</h1>
                <p class="text-gray-600 mt-2">Planification hebdomadaire des cours par classe</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('emplois.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Nouvel emploi du temps
                </a>
                <button onclick="openGenererModal()" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Générer séances
                </button>
            </div>
        </div>
    </div>

    <!-- Messages de succès/erreur -->
    @if(session('success'))
    <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-green-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-green-800">{{ session('success') }}</p>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-red-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <p class="text-red-800">{{ session('error') }}</p>
        </div>
    </div>
    @endif

    <!-- Filtres -->
    <div class="mb-6 bg-white rounded-lg shadow p-4">
        <div class="flex items-center space-x-4">
            <label class="text-sm font-medium text-gray-700">Filtrer par classe :</label>
            <select id="classeFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Toutes les classes</option>
                @foreach($classes as $classe)
                <option value="{{ $classe->id }}">{{ $classe->nom }}</option>
                @endforeach
            </select>
            
            <label class="text-sm font-medium text-gray-700">Filtrer par jour :</label>
            <select id="jourFilter" class="border border-gray-300 rounded-lg px-3 py-2 text-sm">
                <option value="">Tous les jours</option>
                <option value="lundi">Lundi</option>
                <option value="mardi">Mardi</option>
                <option value="mercredi">Mercredi</option>
                <option value="jeudi">Jeudi</option>
                <option value="vendredi">Vendredi</option>
            </select>
        </div>
    </div>

    <!-- Liste des emplois du temps -->
    <div class="space-y-6">
        @foreach($emploisDuTemps as $classeId => $emplois)
        @php $classe = $classes->find($classeId) @endphp
        <div class="bg-white rounded-lg shadow-lg overflow-hidden classe-section" data-classe="{{ $classeId }}">
            <!-- En-tête de la classe -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-semibold">{{ $classe->nom }}</h2>
                        <p class="text-blue-100 text-sm">{{ $emplois->count() }} créneaux programmés</p>
                    </div>
                    <div class="flex items-center space-x-2">
                        <a href="{{ route('emplois.create') }}?classe_id={{ $classeId }}" class="inline-flex items-center px-3 py-1 bg-blue-700 text-white rounded text-sm hover:bg-blue-800 transition-colors">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Ajouter
                        </a>
                    </div>
                </div>
            </div>

            <!-- Tableau des emplois -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jour</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horaire</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Enseignant</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Salle</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($emplois as $emploi)
                        <tr class="emploi-row" data-jour="{{ $emploi->jour_semaine }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ ucfirst($emploi->jour_semaine) }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $emploi->heure_debut }} - {{ $emploi->heure_fin }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $emploi->module->nom }}</div>
                                <div class="text-sm text-gray-500">{{ $emploi->module->code }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $emploi->enseignant->name }}</div>
                                <div class="text-sm text-gray-500">{{ $emploi->enseignant->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($emploi->type === 'presentiel')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Présentiel
                                    </span>
                                @elseif($emploi->type === 'elearning')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        E-learning
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Workshop
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $emploi->salle }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <div class="flex items-center space-x-2">
                                    <a href="{{ route('emplois.edit', $emploi) }}" class="text-blue-600 hover:text-blue-900">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                    </a>
                                    <form method="POST" action="{{ route('emplois.destroy', $emploi) }}" class="inline" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet emploi du temps ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-900">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endforeach
    </div>
</div>

<!-- Modal pour générer les séances -->
<div id="genererModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Générer les séances</h3>
            <form method="POST" action="{{ route('emplois.generer-seances') }}">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Date de la semaine :</label>
                    <input type="date" name="date_semaine" value="{{ now()->format('Y-m-d') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <p class="text-xs text-gray-500 mt-1">Les séances seront générées pour la semaine contenant cette date</p>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeGenererModal()" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        Annuler
                    </button>
                    <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        Générer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openGenererModal() {
    document.getElementById('genererModal').classList.remove('hidden');
}

function closeGenererModal() {
    document.getElementById('genererModal').classList.add('hidden');
}

// Filtres
document.getElementById('classeFilter').addEventListener('change', function() {
    const classeId = this.value;
    const sections = document.querySelectorAll('.classe-section');
    
    sections.forEach(section => {
        if (!classeId || section.dataset.classe === classeId) {
            section.style.display = 'block';
        } else {
            section.style.display = 'none';
        }
    });
});

document.getElementById('jourFilter').addEventListener('change', function() {
    const jour = this.value;
    const rows = document.querySelectorAll('.emploi-row');
    
    rows.forEach(row => {
        if (!jour || row.dataset.jour === jour) {
            row.style.display = 'table-row';
        } else {
            row.style.display = 'none';
        }
    });
});
</script>
@endsection
