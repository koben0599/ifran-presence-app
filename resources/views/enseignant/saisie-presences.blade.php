@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête avec informations de la séance -->
    <div class="mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold mb-2">Saisie des Présences</h1>
                    <div class="space-y-1">
                        <p class="text-blue-100">
                            <span class="font-semibold">Module :</span> {{ $seance->module->nom ?? 'Module non défini' }}
                        </p>
                        <p class="text-blue-100">
                            <span class="font-semibold">Classe :</span> {{ $seance->classe->nom ?? 'Classe non définie' }}
                        </p>
                        <p class="text-blue-100">
                            <span class="font-semibold">Date :</span> {{ \Carbon\Carbon::parse($seance->date_debut)->format('d/m/Y') }}
                        </p>
                        <p class="text-blue-100">
                            <span class="font-semibold">Horaire :</span> 
                            {{ \Carbon\Carbon::parse($seance->date_debut)->format('H:i') }} - {{ \Carbon\Carbon::parse($seance->date_fin)->format('H:i') }}
                        </p>
                        @if($seance->salle)
                        <p class="text-blue-100">
                            <span class="font-semibold">Salle :</span> {{ $seance->salle }}
                        </p>
                        @endif
                    </div>
                </div>
                <div class="text-right">
                    <div class="bg-blue-400 rounded-full p-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <p class="text-blue-100 text-sm mt-2">Cours Présentiel</p>
                </div>
            </div>
        </div>
    </div>

    @if($modifiable)
    <!-- Formulaire de saisie -->
    <form method="POST" action="{{ route('seances.saisie.store', $seance->id) }}" class="bg-white rounded-lg shadow-lg">
        @csrf
        
        <!-- En-tête du tableau -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900">Liste des étudiants</h2>
                <div class="flex items-center space-x-4">
                    <span class="text-sm text-gray-600">{{ $etudiants->count() }} étudiants</span>
                    <div class="flex items-center space-x-2">
                        <button type="button" id="selectAll" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Tout sélectionner
                        </button>
                        <span class="text-gray-400">|</span>
                        <button type="button" id="deselectAll" class="text-sm text-gray-600 hover:text-gray-800 font-medium">
                            Tout désélectionner
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tableau des présences -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <input type="checkbox" id="masterCheckbox" class="rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Étudiant
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut de présence
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions rapides
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($etudiants as $etudiant)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <input type="checkbox" class="student-checkbox rounded border-gray-300 text-blue-600 focus:ring-blue-500" 
                                   data-student-id="{{ $etudiant->id }}">
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full bg-blue-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-blue-800">
                                            {{ substr($etudiant->prenom, 0, 1) }}{{ substr($etudiant->nom, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $etudiant->prenom }} {{ $etudiant->nom }}
                                    </div>
                                    <div class="text-sm text-gray-500">
                                        {{ $etudiant->email }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <select name="presences[{{ $etudiant->id }}]" 
                                    class="presence-select rounded-md border-gray-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                                <option value="present" class="text-green-600">✅ Présent</option>
                                <option value="retard" class="text-yellow-600">⏰ Retard</option>
                                <option value="absent" class="text-red-600">❌ Absent</option>
                            </select>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex items-center space-x-2">
                                <button type="button" 
                                        class="quick-present text-green-600 hover:text-green-900 font-medium"
                                        data-student-id="{{ $etudiant->id }}">
                                    Présent
                                </button>
                                <button type="button" 
                                        class="quick-retard text-yellow-600 hover:text-yellow-900 font-medium"
                                        data-student-id="{{ $etudiant->id }}">
                                    Retard
                                </button>
                                <button type="button" 
                                        class="quick-absent text-red-600 hover:text-red-900 font-medium"
                                        data-student-id="{{ $etudiant->id }}">
                                    Absent
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Actions du formulaire -->
        <div class="bg-gray-50 px-6 py-4 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-6">
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-green-100 rounded-full flex items-center justify-center">
                                <span class="text-green-600 text-xs">✅</span>
                            </div>
                            <span class="text-sm text-gray-600">Présent</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-yellow-100 rounded-full flex items-center justify-center">
                                <span class="text-yellow-600 text-xs">⏰</span>
                            </div>
                            <span class="text-sm text-gray-600">Retard</span>
                        </div>
                        <div class="flex items-center space-x-2">
                            <div class="w-4 h-4 bg-red-100 rounded-full flex items-center justify-center">
                                <span class="text-red-600 text-xs">❌</span>
                            </div>
                            <span class="text-sm text-gray-600">Absent</span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('enseignant.presences') }}" 
                       class="px-4 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        Annuler
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-blue-600 text-white rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Enregistrer les présences
                    </button>
                </div>
            </div>
        </div>
    </form>
    @else
    <!-- Message si non modifiable -->
    <div class="bg-red-50 border border-red-200 rounded-lg p-6">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="h-8 w-8 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-lg font-medium text-red-800">Saisie non autorisée</h3>
                <p class="text-red-700 mt-1">
                    La saisie des présences n'est plus modifiable car le délai de 2 semaines après la séance a été dépassé.
                </p>
            </div>
        </div>
        <div class="mt-4">
            <a href="{{ route('enseignant.presences') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-red-700 bg-red-100 hover:bg-red-200">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Retour aux séances
            </a>
        </div>
    </div>
    @endif

    <!-- Informations supplémentaires -->
    <div class="mt-8 bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-blue-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-blue-800">Conseils de saisie</h3>
                <ul class="text-sm text-blue-700 mt-1 space-y-1">
                    <li>• Utilisez les boutons d'action rapide pour une saisie plus efficace</li>
                    <li>• Vous pouvez sélectionner plusieurs étudiants et modifier leur statut en masse</li>
                    <li>• Les présences sont automatiquement sauvegardées lors de la soumission</li>
                    <li>• Vous pouvez revenir modifier les présences dans un délai de 2 semaines</li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion de la case à cocher maître
    const masterCheckbox = document.getElementById('masterCheckbox');
    const studentCheckboxes = document.querySelectorAll('.student-checkbox');
    
    masterCheckbox.addEventListener('change', function() {
        studentCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
    });
    
    // Gestion des boutons "Tout sélectionner" et "Tout désélectionner"
    document.getElementById('selectAll').addEventListener('click', function() {
        studentCheckboxes.forEach(checkbox => {
            checkbox.checked = true;
        });
        masterCheckbox.checked = true;
    });
    
    document.getElementById('deselectAll').addEventListener('click', function() {
        studentCheckboxes.forEach(checkbox => {
            checkbox.checked = false;
        });
        masterCheckbox.checked = false;
    });
    
    // Gestion des actions rapides
    document.querySelectorAll('.quick-present').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            const select = document.querySelector(`select[name="presences[${studentId}]"]`);
            select.value = 'present';
        });
    });
    
    document.querySelectorAll('.quick-retard').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            const select = document.querySelector(`select[name="presences[${studentId}]"]`);
            select.value = 'retard';
        });
    });
    
    document.querySelectorAll('.quick-absent').forEach(button => {
        button.addEventListener('click', function() {
            const studentId = this.getAttribute('data-student-id');
            const select = document.querySelector(`select[name="presences[${studentId}]"]`);
            select.value = 'absent';
        });
    });
    
    // Mise à jour de la case à cocher maître quand les cases individuelles changent
    studentCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
            const someChecked = Array.from(studentCheckboxes).some(cb => cb.checked);
            
            masterCheckbox.checked = allChecked;
            masterCheckbox.indeterminate = someChecked && !allChecked;
        });
    });
});
</script>

<style>
/* Animations personnalisées */
.presence-select {
    transition: all 0.2s ease;
}

.presence-select:focus {
    transform: scale(1.02);
}

.quick-present:hover,
.quick-retard:hover,
.quick-absent:hover {
    transform: translateY(-1px);
}

/* Responsive design */
@media (max-width: 768px) {
    .container {
        padding-left: 1rem;
        padding-right: 1rem;
    }
    
    table {
        font-size: 0.875rem;
    }
}
</style>
@endsection
