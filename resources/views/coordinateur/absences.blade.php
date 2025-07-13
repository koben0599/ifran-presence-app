@extends('layouts.app')

@section('title', 'Gestion des absences - IFRAN Presence')

@section('page-header')
<div class="bg-gradient-to-r from-red-600 to-orange-600 rounded-xl shadow-xl p-4 sm:p-6 lg:p-8 text-white">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">Gestion des absences</h1>
            <p class="text-red-100 text-base sm:text-lg">Suivi et justification des absences des étudiants</p>
            <div class="flex flex-col sm:flex-row sm:items-center mt-4 space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-sm">En ligne</span>
                </div>
                <span class="hidden sm:inline text-sm text-red-200">•</span>
                <span class="text-sm text-red-200">Coordinateur</span>
            </div>
        </div>
        <div class="hidden sm:block">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white/20 flex items-center justify-center">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<!-- Messages de statut -->
@if (session('success'))
    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg animate-fade-in-up">
        <div class="flex items-center">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm sm:text-base">{{ session('success') }}</span>
        </div>
    </div>
@endif

<!-- Statistiques des absences -->
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-6 mb-6 sm:mb-8">
    <div class="hover-card bg-white rounded-xl shadow-lg p-3 sm:p-6 border-l-4 border-red-500 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-600">Total absences</p>
                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $absences->count() }}</p>
            </div>
            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-red-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="hover-card bg-white rounded-xl shadow-lg p-3 sm:p-6 border-l-4 border-yellow-500 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-600">Non justifiées</p>
                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $absences->where('justifie', false)->count() }}</p>
            </div>
            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="hover-card bg-white rounded-xl shadow-lg p-3 sm:p-6 border-l-4 border-green-500 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-600">Justifiées</p>
                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $absences->where('justifie', true)->count() }}</p>
            </div>
            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
        </div>
    </div>

    <div class="hover-card bg-white rounded-xl shadow-lg p-3 sm:p-6 border-l-4 border-blue-500 transition-all duration-300">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm font-medium text-gray-600">Cette semaine</p>
                <p class="text-lg sm:text-2xl font-bold text-gray-900">{{ $absences->where('created_at', '>=', now()->startOfWeek())->count() }}</p>
            </div>
            <div class="w-8 h-8 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                <svg class="w-4 h-4 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
            </div>
        </div>
    </div>
</div>

<!-- Filtres et recherche -->
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
    <div class="flex flex-col space-y-4 sm:space-y-0 sm:flex-row sm:items-center sm:justify-between">
        <h2 class="text-lg sm:text-xl font-bold text-gray-800">Liste des absences</h2>
        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-4">
            <select class="border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Tous les statuts</option>
                <option value="non_justifie">Non justifiées</option>
                <option value="justifie">Justifiées</option>
            </select>
            <input type="text" placeholder="Rechercher un étudiant..." class="border border-gray-300 rounded-lg px-3 sm:px-4 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
        </div>
    </div>
</div>

<!-- Liste des absences -->
<div class="bg-white rounded-xl shadow-lg overflow-hidden">
    @if($absences->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden sm:table-cell">Module</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider hidden md:table-cell">Date</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-3 sm:px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($absences as $absence)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-8 h-8 sm:w-10 sm:h-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-xs sm:text-sm mr-2 sm:mr-3">
                                    @if($absence->etudiant->photo)
                                        <img src="{{ asset('storage/' . $absence->etudiant->photo) }}" alt="Photo" class="w-full h-full rounded-full object-cover">
                                    @else
                                        {{ $absence->etudiant->initials }}
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <div class="text-xs sm:text-sm font-medium text-gray-900 truncate">{{ $absence->etudiant->display_name }}</div>
                                    <div class="text-xs sm:text-sm text-gray-500 truncate hidden sm:block">{{ $absence->etudiant->email }}</div>
                                    <div class="text-xs text-gray-500 sm:hidden">{{ $absence->seance->module->nom ?? 'N/A' }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden sm:table-cell">
                            <div class="text-xs sm:text-sm text-gray-900">{{ $absence->seance->module->nom ?? 'N/A' }}</div>
                            <div class="text-xs sm:text-sm text-gray-500">{{ $absence->seance->emploiDuTemps->type ?? 'N/A' }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap hidden md:table-cell">
                            <div class="text-xs sm:text-sm text-gray-900">{{ $absence->seance->date ? \Carbon\Carbon::parse($absence->seance->date)->format('d/m/Y') : 'N/A' }}</div>
                            <div class="text-xs sm:text-sm text-gray-500">{{ $absence->seance->heure_debut ?? 'N/A' }} - {{ $absence->seance->heure_fin ?? 'N/A' }}</div>
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap">
                            @if($absence->justifie)
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="hidden sm:inline">Justifiée</span>
                                    <span class="sm:hidden">✓</span>
                                </span>
                            @else
                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                    <span class="hidden sm:inline">Non justifiée</span>
                                    <span class="sm:hidden">✗</span>
                                </span>
                            @endif
                        </td>
                        <td class="px-3 sm:px-6 py-4 whitespace-nowrap text-xs sm:text-sm font-medium">
                            <div class="flex flex-col sm:flex-row space-y-1 sm:space-y-0 sm:space-x-2">
                                @if(!$absence->justifie)
                                    <button onclick="openJustificationModal({{ $absence->id }})" class="text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 px-2 sm:px-3 py-1 rounded-lg transition-colors text-xs sm:text-sm">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                        </svg>
                                        <span class="hidden sm:inline">Justifier</span>
                                        <span class="sm:hidden">Justifier</span>
                                    </button>
                                @else
                                    <span class="text-green-600 bg-green-50 px-2 sm:px-3 py-1 rounded-lg text-xs sm:text-sm">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4 inline mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                        <span class="hidden sm:inline">Justifiée</span>
                                        <span class="sm:hidden">✓</span>
                                    </span>
                                @endif
                                <button class="text-gray-600 hover:text-gray-900 bg-gray-50 hover:bg-gray-100 px-2 sm:px-3 py-1 rounded-lg transition-colors text-xs sm:text-sm">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="text-center py-8 sm:py-12">
            <svg class="mx-auto h-8 w-8 sm:h-12 sm:w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune absence</h3>
            <p class="mt-1 text-xs sm:text-sm text-gray-500">Tous les étudiants sont présents !</p>
        </div>
    @endif
</div>

<!-- Modal de justification -->
<div id="justificationModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-10 sm:top-20 mx-auto p-4 sm:p-5 border w-11/12 sm:w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-base sm:text-lg font-medium text-gray-900">Justifier l'absence</h3>
                <button onclick="closeJustificationModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            
            <form id="justificationForm" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="mb-4">
                    <label for="motif" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Motif de l'absence</label>
                    <textarea id="motif" name="motif" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Décrivez le motif de l'absence..." required></textarea>
                </div>
                
                <div class="mb-4">
                    <label for="piece_jointe" class="block text-xs sm:text-sm font-medium text-gray-700 mb-2">Pièce jointe (optionnel)</label>
                    <input type="file" id="piece_jointe" name="piece_jointe" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-xs sm:text-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                    <p class="text-xs text-gray-500 mt-1">Formats acceptés: PDF, DOC, DOCX, JPG, PNG (max 2MB)</p>
                </div>
                
                <div class="flex flex-col sm:flex-row justify-end space-y-2 sm:space-y-0 sm:space-x-3">
                    <button type="button" onclick="closeJustificationModal()" class="px-3 sm:px-4 py-2 text-gray-600 bg-gray-100 rounded-lg hover:bg-gray-200 transition-colors text-sm">
                        Annuler
                    </button>
                    <button type="submit" class="px-3 sm:px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                        Justifier
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openJustificationModal(absenceId) {
    const modal = document.getElementById('justificationModal');
    const form = document.getElementById('justificationForm');
    
    // Mettre à jour l'action du formulaire
    form.action = `/coordinateur/absences/${absenceId}/justifier`;
    
    // Afficher le modal
    modal.classList.remove('hidden');
}

function closeJustificationModal() {
    const modal = document.getElementById('justificationModal');
    modal.classList.add('hidden');
    
    // Réinitialiser le formulaire
    document.getElementById('justificationForm').reset();
}

// Fermer le modal en cliquant à l'extérieur
document.getElementById('justificationModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeJustificationModal();
    }
});

// Fermer le modal avec la touche Escape
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeJustificationModal();
    }
});
</script>
@endsection 