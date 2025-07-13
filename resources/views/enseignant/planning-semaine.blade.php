@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Planning de la Semaine</h1>
                <p class="text-gray-600 mt-2">Du {{ $lundi->format('d/m/Y') }} au {{ $vendredi->format('d/m/Y') }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('enseignant.emploi-du-temps') }}" class="inline-flex items-center px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Emploi du temps
                </a>
                <form method="POST" action="{{ route('enseignant.generer-seances') }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Régénérer
                    </button>
                </form>
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

    <!-- Grille du planning -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-6">
        @php
            $joursFrancais = [
                'Monday' => 'Lundi',
                'Tuesday' => 'Mardi', 
                'Wednesday' => 'Mercredi',
                'Thursday' => 'Jeudi',
                'Friday' => 'Vendredi'
            ];
        @endphp

        @foreach($joursSemaine as $jour)
        <div class="bg-white rounded-lg shadow-lg overflow-hidden">
            <!-- En-tête du jour -->
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-4 py-3 text-white">
                <h3 class="text-lg font-semibold">{{ $joursFrancais[$jour] }}</h3>
                <p class="text-blue-100 text-sm">
                    {{ $lundi->copy()->addDays(array_search($jour, $joursSemaine))->format('d/m/Y') }}
                </p>
            </div>

            <!-- Séances du jour -->
            <div class="p-4">
                @php
                    $seancesJour = $planningOrganise[$jour] ?? collect();
                @endphp

                @if($seancesJour->isNotEmpty())
                <div class="space-y-3">
                    @foreach($seancesJour as $seance)
                    <div class="border border-gray-200 rounded-lg p-3 hover:shadow-md transition-shadow">
                        <div class="flex items-start justify-between mb-2">
                            <div class="flex-1">
                                <h4 class="text-sm font-semibold text-gray-900">
                                    {{ $seance->module->nom ?? 'Module non défini' }}
                                </h4>
                                <p class="text-xs text-gray-600 mt-1">
                                    {{ $seance->classe->nom ?? 'Classe non définie' }}
                                </p>
                            </div>
                            <div class="ml-2">
                                @if($seance->type === 'presentiel')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Présentiel
                                    </span>
                                @elseif($seance->type === 'elearning')
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        E-learning
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-orange-100 text-orange-800">
                                        Workshop
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="flex items-center text-xs text-gray-500 mb-2">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $seance->date_debut->format('H:i') }} - {{ $seance->date_fin->format('H:i') }}
                        </div>

                        @if($seance->salle)
                        <div class="flex items-center text-xs text-gray-500 mb-3">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                            {{ $seance->salle }}
                        </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex space-x-2">
                            @php
                                $now = now();
                                $canSaisir = auth()->user()->isEnseignant() && $seance->type === 'presentiel' && $seance->date_debut <= $now;
                            @endphp

                            @if($canSaisir)
                                <a href="{{ route('seances.saisie', $seance->id) }}" 
                                   class="flex-1 bg-blue-600 text-white text-center py-1 px-2 rounded text-xs hover:bg-blue-700 transition-colors">
                                    Saisir
                                </a>
                            @endif
                            
                            <a href="{{ route('seances.saisie', $seance->id) }}" 
                               class="bg-gray-200 text-gray-700 p-1 rounded text-xs hover:bg-gray-300 transition-colors"
                               title="Voir détails">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                </svg>
                            </a>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    <p class="text-gray-500 text-sm mt-2">Aucune séance</p>
                </div>
                @endif
            </div>
        </div>
        @endforeach
    </div>

    <!-- Statistiques de la semaine -->
    @php
        $totalSeances = 0;
        $seancesPresentiel = 0;
        $seancesElearning = 0;
        $seancesWorkshop = 0;
        
        foreach($planningOrganise as $seances) {
            $totalSeances += $seances->count();
            $seancesPresentiel += $seances->where('type', 'presentiel')->count();
            $seancesElearning += $seances->where('type', 'elearning')->count();
            $seancesWorkshop += $seances->where('type', 'workshop')->count();
        }
    @endphp

    <div class="mt-8 grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Total séances</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $totalSeances }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Présentiel</p>
                    <p class="text-2xl font-bold text-blue-600">{{ $seancesPresentiel }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">E-learning</p>
                    <p class="text-2xl font-bold text-purple-600">{{ $seancesElearning }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center">
                <div class="w-10 h-10 bg-orange-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600">Workshop</p>
                    <p class="text-2xl font-bold text-orange-600">{{ $seancesWorkshop }}</p>
                </div>
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

/* Responsive design */
@media (max-width: 768px) {
    .grid {
        grid-template-columns: 1fr;
    }
}
</style>
@endsection 