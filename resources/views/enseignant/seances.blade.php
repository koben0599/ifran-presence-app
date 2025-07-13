@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête avec titre et statistiques -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mes Séances</h1>
                <p class="text-gray-600 mt-2">Gérez vos cours et saisissez les présences</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600">{{ $seances->count() }}</div>
                    <div class="text-sm text-blue-800">Séances totales</div>
                </div>
                <div class="bg-green-100 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600">{{ $seances->where('date_debut', '>', now())->count() }}</div>
                    <div class="text-sm text-green-800">À venir</div>
                </div>
                <div class="bg-purple-100 rounded-lg p-4">
                    <div class="text-2xl font-bold text-purple-600">{{ $seances->where('type', 'presentiel')->count() }}</div>
                    <div class="text-sm text-purple-800">Présentiel</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres avancés -->
    <div class="bg-white rounded-lg shadow-sm border p-6 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" placeholder="Rechercher par module..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Tous les types</option>
                <option value="presentiel">Présentiel</option>
                <option value="elearning">E-learning</option>
                <option value="workshop">Workshop</option>
            </select>
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Toutes les classes</option>
                @foreach($seances->pluck('classe.nom')->unique() as $classe)
                    <option value="{{ $classe }}">{{ $classe }}</option>
                @endforeach
            </select>
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Tous les statuts</option>
                <option value="a_venir">À venir</option>
                <option value="en_cours">En cours</option>
                <option value="terminee">Terminée</option>
            </select>
            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Filtrer
            </button>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-4 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <a href="{{ route('enseignant.presences') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Saisir présences
                </a>
                <a href="{{ route('enseignant.statistiques') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                    Voir statistiques
                </a>
            </div>
            <div class="text-sm text-gray-600">
                <span class="font-medium">{{ $seances->where('date_debut', '<=', now())->where('type', 'presentiel')->count() }}</span> séances présentiel à saisir
            </div>
        </div>
    </div>

    <!-- Grille des séances -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($seances as $seance)
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <!-- Image dynamique selon le type de cours -->
            <div class="h-48 bg-gradient-to-br relative overflow-hidden">
                @if($seance->type === 'presentiel')
                    <!-- Image pour cours présentiel -->
                    <div class="absolute inset-0 bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center">
                        <div class="text-center text-white">
                            <svg class="w-16 h-16 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                            </svg>
                            <h3 class="text-xl font-bold">Cours Présentiel</h3>
                            <p class="text-blue-100">En salle de classe</p>
                        </div>
                    </div>
                @elseif($seance->type === 'elearning')
                    <!-- Image pour e-learning -->
                    <div class="absolute inset-0 bg-gradient-to-br from-purple-500 to-purple-700 flex items-center justify-center">
                        <div class="text-center text-white">
                            <svg class="w-16 h-16 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M21 3H3c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h5l-1 1v2h8v-2l-1-1h5c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm0 12H3V5h18v10z"/>
                            </svg>
                            <h3 class="text-xl font-bold">E-Learning</h3>
                            <p class="text-purple-100">Cours en ligne</p>
                        </div>
                    </div>
                @else
                    <!-- Image pour workshop -->
                    <div class="absolute inset-0 bg-gradient-to-br from-orange-500 to-orange-700 flex items-center justify-center">
                        <div class="text-center text-white">
                            <svg class="w-16 h-16 mx-auto mb-2" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-5 14H7v-2h7v2zm3-4H7v-2h10v2zm0-4H7V7h10v2z"/>
                            </svg>
                            <h3 class="text-xl font-bold">Workshop</h3>
                            <p class="text-orange-100">Exercices pratiques</p>
                        </div>
                    </div>
                @endif
                
                <!-- Badge de statut sur l'image -->
                <div class="absolute top-4 right-4">
                    @php
                        $dateDebut = \Carbon\Carbon::parse($seance->date_debut);
                        $dateFin = \Carbon\Carbon::parse($seance->date_fin);
                        $now = \Carbon\Carbon::now();
                    @endphp
                    
                    @if($dateDebut <= $now && $dateFin >= $now)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 shadow-lg">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            En cours
                        </span>
                    @elseif($dateDebut > $now)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 shadow-lg">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            À venir
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 shadow-lg">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Terminée
                        </span>
                    @endif
                </div>
            </div>

            <!-- Contenu de la carte -->
            <div class="p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 mb-1">
                            {{ $seance->module->nom ?? 'Module non défini' }}
                        </h3>
                        <p class="text-sm text-gray-600">
                            {{ $seance->classe->nom ?? 'Classe non définie' }}
                        </p>
                    </div>
                </div>

                <!-- Type de cours avec icône -->
                <div class="flex items-center mb-3">
                    @if($seance->type === 'presentiel')
                        <div class="flex items-center text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                            <span class="text-sm font-medium">Présentiel</span>
                        </div>
                    @elseif($seance->type === 'elearning')
                        <div class="flex items-center text-purple-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span class="text-sm font-medium">E-learning</span>
                        </div>
                    @else
                        <div class="flex items-center text-orange-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                            </svg>
                            <span class="text-sm font-medium">Workshop</span>
                        </div>
                    @endif
                </div>

                <!-- Horaires -->
                <div class="flex items-center text-gray-600 text-sm mb-2">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>{{ $dateDebut->format('d/m/Y') }}</span>
                    <span class="mx-2">•</span>
                    <span>{{ $dateDebut->format('H:i') }} - {{ $dateFin->format('H:i') }}</span>
                </div>

                @if($seance->salle)
                <div class="flex items-center text-gray-600 text-sm mb-4">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    <span>{{ $seance->salle }}</span>
                </div>
                @endif

                <!-- Actions -->
                <div class="flex space-x-3">
                    @php
                        $user = auth()->user();
                        $canSaisir = false;
                        
                        // Règles métier : Enseignant peut saisir seulement les cours présentiel
                        if ($user->isEnseignant() && $seance->type === 'presentiel' && $dateDebut <= $now) {
                            $canSaisir = true;
                        }
                        
                        // Coordinateur peut saisir e-learning et workshop
                        if ($user->isCoordinateur() && in_array($seance->type, ['elearning', 'workshop']) && $dateDebut <= $now) {
                            $canSaisir = true;
                        }
                    @endphp

                    @if($canSaisir)
                        <a href="{{ route('seances.saisie', $seance->id) }}" 
                           class="flex-1 bg-blue-600 text-white text-center py-2 px-4 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Saisir présence
                        </a>
                    @elseif($dateDebut > $now)
                        <button disabled class="flex-1 bg-gray-300 text-gray-500 text-center py-2 px-4 rounded-lg cursor-not-allowed flex items-center justify-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pas encore commencé
                        </button>
                    @else
                        <span class="flex-1 bg-yellow-100 text-yellow-800 text-center py-2 px-4 rounded-lg flex items-center justify-center text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            Non autorisé
                        </span>
                    @endif
                    
                    <!-- Bouton Voir -->
                    <a href="{{ route('seances.saisie', $seance->id) }}" 
                       class="bg-gray-200 text-gray-700 p-2 rounded-lg hover:bg-gray-300 transition-colors duration-200"
                       title="Voir les détails">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                    </a>
                </div>

                <!-- Message d'information sur les permissions -->
                @if($dateDebut <= $now && !$canSaisir)
                    <div class="mt-3 p-2 bg-yellow-50 border border-yellow-200 rounded-lg">
                        <p class="text-xs text-yellow-800">
                            <strong>Info :</strong> 
                            @if($user->isEnseignant())
                                Seuls les coordinateurs peuvent saisir les présences pour les cours {{ $seance->type }}.
                            @else
                                Vous ne pouvez saisir que les cours e-learning et workshop.
                            @endif
                        </p>
                    </div>
                @endif
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune séance trouvée</h3>
                <p class="mt-1 text-sm text-gray-500">Vous n'avez pas encore de séances programmées.</p>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($seances->hasPages())
    <div class="mt-8">
        {{ $seances->links() }}
    </div>
    @endif

    <!-- Informations sur les types de cours -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-blue-900">Cours Présentiel</h3>
            </div>
            <p class="text-sm text-blue-700">Cours en salle de classe. Vous pouvez saisir les présences directement.</p>
        </div>

        <div class="bg-purple-50 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-purple-900">E-Learning</h3>
            </div>
            <p class="text-sm text-purple-700">Cours en ligne. Géré par les coordinateurs.</p>
        </div>

        <div class="bg-orange-50 rounded-lg p-4">
            <div class="flex items-center mb-2">
                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <h3 class="font-semibold text-orange-900">Workshop</h3>
            </div>
            <p class="text-sm text-orange-700">Exercices pratiques. Géré par les coordinateurs.</p>
        </div>
    </div>
</div>

<style>
/* Animations personnalisées */
.card-hover {
    transition: all 0.3s ease;
}

.card-hover:hover {
    transform: translateY(-2px);
}

/* Animation pour les boutons */
.btn-primary {
    transition: all 0.2s ease;
}

.btn-primary:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.3);
}

/* Responsive design */
@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection 