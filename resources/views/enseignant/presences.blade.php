@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête avec titre et actions -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Saisie des Présences</h1>
                <p class="text-gray-600 mt-2">Saisissez les présences pour vos cours présentiel</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="bg-blue-100 rounded-lg p-4">
                    <div class="text-2xl font-bold text-blue-600">{{ $seances->where('date_debut', '<=', now())->count() }}</div>
                    <div class="text-sm text-blue-800">À saisir</div>
                </div>
                <div class="bg-green-100 rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-600">{{ $seances->where('date_debut', '>=', now()->subDays(7))->count() }}</div>
                    <div class="text-sm text-green-800">Cette semaine</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Message d'information -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <div class="flex items-center">
            <svg class="w-5 h-5 text-blue-600 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-blue-800">Règles de saisie</h3>
                <p class="text-sm text-blue-700 mt-1">
                    Vous pouvez saisir les présences uniquement pour vos cours présentiel. Les cours e-learning et workshop sont gérés par les coordinateurs.
                </p>
            </div>
        </div>
    </div>

    <!-- Filtres simples -->
    <div class="bg-white rounded-lg shadow-sm border p-4 mb-6">
        <div class="flex flex-wrap items-center gap-4">
            <div class="flex-1 min-w-64">
                <input type="text" placeholder="Rechercher par module..." class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
            </div>
            <select class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                <option value="">Toutes les classes</option>
                @foreach($seances->pluck('classe.nom')->unique() as $classe)
                    <option value="{{ $classe }}">{{ $classe }}</option>
                @endforeach
            </select>
            <button class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                Filtrer
            </button>
        </div>
    </div>

    <!-- Grille des séances à saisir -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($seances as $seance)
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
            <!-- En-tête de la carte -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 p-4 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold">{{ $seance->module->nom ?? 'Module non défini' }}</h3>
                        <p class="text-blue-100 text-sm">{{ $seance->classe->nom ?? 'Classe non définie' }}</p>
                    </div>
                    <div class="bg-blue-400 rounded-full p-2">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Contenu de la carte -->
            <div class="p-6">
                <!-- Horaires -->
                @php
                    $dateDebut = \Carbon\Carbon::parse($seance->date_debut);
                    $dateFin = \Carbon\Carbon::parse($seance->date_fin);
                    $now = \Carbon\Carbon::now();
                @endphp
                
                <div class="flex items-center text-gray-600 text-sm mb-3">
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

                <!-- Statut de la séance -->
                <div class="mb-4">
                    @if($dateDebut <= $now && $dateFin >= $now)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            En cours
                        </span>
                    @elseif($dateDebut > $now)
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                            </svg>
                            À venir
                        </span>
                    @else
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            Terminée
                        </span>
                    @endif
                </div>

                <!-- Actions principales -->
                <div class="space-y-3">
                    @if($dateDebut <= $now)
                        <a href="{{ route('seances.saisie', $seance->id) }}" 
                           class="w-full bg-blue-600 text-white text-center py-3 px-4 rounded-lg hover:bg-blue-700 transition-colors duration-200 flex items-center justify-center font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Saisir présence
                        </a>
                    @else
                        <button disabled class="w-full bg-gray-300 text-gray-500 text-center py-3 px-4 rounded-lg cursor-not-allowed flex items-center justify-center font-medium">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Pas encore commencé
                        </button>
                    @endif
                    
                    <!-- Bouton Voir détails -->
                    <a href="{{ route('seances.saisie', $seance->id) }}" 
                       class="w-full bg-gray-100 text-gray-700 text-center py-2 px-4 rounded-lg hover:bg-gray-200 transition-colors duration-200 flex items-center justify-center text-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        Voir détails
                    </a>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full">
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune séance à saisir</h3>
                <p class="mt-1 text-sm text-gray-500">Vous n'avez pas de cours présentiel nécessitant une saisie de présences.</p>
                <div class="mt-6">
                    <a href="{{ route('enseignant.seances') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Voir toutes mes séances
                    </a>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Informations supplémentaires -->
    <div class="mt-8 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="flex items-start">
            <svg class="w-5 h-5 text-yellow-600 mr-2 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
            </svg>
            <div>
                <h3 class="text-sm font-medium text-yellow-800">Rappel important</h3>
                <p class="text-sm text-yellow-700 mt-1">
                    La saisie des présences doit être effectuée dans un délai de 2 semaines après la séance. 
                    Passé ce délai, les présences ne seront plus modifiables.
                </p>
            </div>
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