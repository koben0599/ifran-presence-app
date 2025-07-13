@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- En-tête avec informations personnelles -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Tableau de Bord</h1>
                <p class="text-gray-600 mt-2">Bienvenue, {{ auth()->user()->nom }} {{ auth()->user()->prenom }}</p>
            </div>
            <div class="flex items-center space-x-4">
                <div class="text-right">
                    <p class="text-sm text-gray-600">Aujourd'hui</p>
                    <p class="text-lg font-semibold text-gray-900">{{ now()->format('d/m/Y') }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Cartes de statistiques rapides -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <!-- Séances aujourd'hui -->
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Séances aujourd'hui</p>
                    <p class="text-3xl font-bold">{{ $seances->where('date_debut', '>=', now()->startOfDay())->where('date_debut', '<=', now()->endOfDay())->count() }}</p>
                </div>
                <div class="bg-blue-400 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Séances cette semaine -->
        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm">Cette semaine</p>
                    <p class="text-3xl font-bold">{{ $seances->where('date_debut', '>=', now()->startOfWeek())->where('date_debut', '<=', now()->endOfWeek())->count() }}</p>
                </div>
                <div class="bg-green-400 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Taux de présence moyen -->
        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm">Taux de présence</p>
                    <p class="text-3xl font-bold">
                        @php
                            $totalPresences = 0;
                            $totalPresents = 0;
                            foreach($stats as $stat) {
                                $totalPresences += $stat['total'];
                                $totalPresents += $stat['present'];
                            }
                            $tauxMoyen = $totalPresences > 0 ? round(($totalPresents / $totalPresences) * 100) : 0;
                        @endphp
                        {{ $tauxMoyen }}%
                    </p>
                </div>
                <div class="bg-purple-400 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Actions rapides -->
        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-orange-100 text-sm">Actions rapides</p>
                    <p class="text-lg font-semibold">Saisir présences</p>
                </div>
                <div class="bg-orange-400 rounded-full p-3">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <a href="{{ route('enseignant.presences') }}" class="block mt-3 text-orange-100 hover:text-white text-sm font-medium">
                Voir mes séances →
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Séances à venir (simplifié) -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-xl font-semibold">Mes prochaines séances</h2>
                <a href="{{ route('enseignant.seances') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                    Voir toutes →
                </a>
            </div>
            
            @if($seances->isEmpty())
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-gray-500 mt-2">Aucune séance prévue dans les 2 prochaines semaines</p>
            </div>
            @else
            <div class="space-y-3">
                @foreach($seances->take(5) as $seance)
                <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg hover:bg-gray-100 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center">
                            @if($seance->type === 'presentiel')
                                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            @elseif($seance->type === 'elearning')
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                </svg>
                            @else
                                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                                </svg>
                            @endif
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">{{ $seance->module->nom ?? 'Module non défini' }}</p>
                            <p class="text-sm text-gray-600">{{ $seance->classe->nom ?? 'Classe non définie' }} • {{ \Carbon\Carbon::parse($seance->date_debut)->format('d/m H:i') }}</p>
                        </div>
                    </div>
                    <div class="flex items-center space-x-2">
                        @php
                            $dateDebut = \Carbon\Carbon::parse($seance->date_debut);
                            $now = \Carbon\Carbon::now();
                            $canSaisir = auth()->user()->isEnseignant() && $seance->type === 'presentiel' && $dateDebut <= $now;
                        @endphp
                        
                        @if($canSaisir)
                            <a href="{{ route('seances.saisie', $seance->id) }}" 
                               class="bg-blue-600 text-white text-xs px-3 py-1 rounded-lg hover:bg-blue-700 transition-colors">
                                Saisir
                            </a>
                        @endif
                        <a href="{{ route('seances.saisie', $seance->id) }}" 
                           class="text-gray-400 hover:text-gray-600">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
        
        <!-- Statistiques rapides -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Taux de présence par classe</h2>
            
            <div class="space-y-4">
                @foreach($stats as $classe => $stat)
                <div>
                    <div class="flex justify-between items-center mb-1">
                        <p class="font-medium text-sm">{{ $classe }}</p>
                        <p class="text-sm text-gray-600">{{ $stat['taux'] }}%</p>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300" 
                             style="width: {{ $stat['taux'] }}%"></div>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">
                        {{ $stat['present'] }} présents / {{ $stat['total'] }}
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Graphique -->
    @if(!empty($chartClasses) && !empty($chartData))
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Évolution des présences</h2>
        <div class="h-64">
            <canvas id="presenceChart"></canvas>
        </div>
    </div>
    @endif

    <!-- Alertes étudiants à risque -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Étudiants à risque</h2>
        
        @php
            $etudiantsRisque = [];
            $enseignant = auth()->user();
            
            // Récupérer les séances de l'enseignant
            $seancesIds = $enseignant->seancesEnseignant()->pluck('id');
            
            if ($seancesIds->isNotEmpty()) {
                // Récupérer les étudiants avec leur taux de présence
                $etudiants = \App\Models\User::where('role', 'etudiant')
                    ->whereHas('presencesEtudiant', function($query) use ($seancesIds) {
                        $query->whereIn('seance_id', $seancesIds);
                    })
                    ->with(['presencesEtudiant' => function($query) use ($seancesIds) {
                        $query->whereIn('seance_id', $seancesIds);
                    }, 'classe'])
                    ->get();
                
                foreach ($etudiants as $etudiant) {
                    $presences = $etudiant->presencesEtudiant->count();
                    $presents = $etudiant->presencesEtudiant->where('statut', 'present')->count();
                    $taux = $presences > 0 ? ($presents / $presences) * 100 : 100;
                    
                    if ($taux < 40) {
                        $etudiantsRisque[] = [
                            'etudiant' => $etudiant,
                            'taux' => round($taux, 2),
                            'classe' => $etudiant->classe->nom ?? 'Non définie'
                        ];
                    }
                }
            }
        @endphp
        
        @if(count($etudiantsRisque) > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full">
                <thead>
                    <tr class="bg-gray-100">
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Étudiant</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Classe</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Taux de présence</th>
                        <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($etudiantsRisque as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-8 w-8">
                                    <div class="h-8 w-8 rounded-full bg-red-100 flex items-center justify-center">
                                        <span class="text-sm font-medium text-red-800">
                                            {{ substr($item['etudiant']->prenom, 0, 1) }}{{ substr($item['etudiant']->nom, 0, 1) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-3">
                                    <div class="text-sm font-medium text-gray-900">
                                        {{ $item['etudiant']->prenom }} {{ $item['etudiant']->nom }}
                                    </div>
                                </div>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-900">
                            {{ $item['classe'] }}
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-red-500 h-2 rounded-full" style="width: {{ $item['taux'] }}%"></div>
                                </div>
                                <span class="text-sm text-gray-900">{{ $item['taux'] }}%</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-red-100 text-red-800">
                                À risque
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <div class="text-center py-8">
            <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            <p class="text-gray-500 mt-2">Aucun étudiant à risque détecté</p>
        </div>
        @endif
    </div>
</div>

@if(!empty($chartClasses) && !empty($chartData))
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('presenceChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: @json($chartClasses),
            datasets: [{
                label: 'Taux de présence (%)',
                data: @json($chartData),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });
});
</script>
@endif
@endsection