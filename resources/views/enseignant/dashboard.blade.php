@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Tableau de Bord</h1>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        <!-- Séances à venir -->
        <div class="lg:col-span-2 bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Mes prochaines séances</h2>
            
            @if($seances->isEmpty())
            <p class="text-gray-500">Aucune séance prévue dans les 2 prochaines semaines</p>
            @else
            <div class="space-y-4">
                @foreach($seances as $seance)
                <div class="border-l-4 border-blue-500 pl-4 py-2">
                    <div class="flex justify-between items-start">
                        <div>
                            <p class="font-semibold">{{ $seance->module->nom }}</p>
                            <p class="text-gray-600">{{ $seance->classe }} • {{ $seance->date_debut->format('d/m/Y H:i') }}</p>
                            <p class="text-sm {{ $seance->type_cours === 'presentiel' ? 'text-blue-600' : 'text-purple-600' }}">
                                {{ ucfirst($seance->type_cours) }}
                            </p>
                        </div>
                        <div>
                            @if($seance->date_debut <= now() && $seance->date_fin >= now())
                            <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">En cours</span>
                            @elseif($seance->date_debut > now())
                            <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">À venir</span>
                            @endif
                        </div>
                    </div>
                    
                    @if($seance->date_debut <= now())
                    <div class="mt-2">
                        <a href="{{ route('presences.saisir', $seance) }}" class="text-blue-600 hover:text-blue-800 text-sm flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                            </svg>
                            Saisir les présences
                        </a>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>
        
        <!-- Statistiques rapides -->
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Statistiques par classe</h2>
            
            <div class="space-y-4">
                @foreach($stats as $classe => $stat)
                <div>
                    <p class="font-medium">{{ $classe }}</p>
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                        <div class="bg-blue-600 h-2.5 rounded-full" 
                             style="width: {{ $stat['taux'] }}%"></div>
                    </div>
                    <p class="text-sm text-gray-600 mt-1">
                        {{ $stat['present'] }} présents / {{ $stat['total'] }} • {{ $stat['taux'] }}%
                    </p>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Graphique -->
    <div class="bg-white rounded-lg shadow p-6 mb-8">
        <h2 class="text-xl font-semibold mb-4">Taux de présence par classe</h2>
        <div class="h-64">
            <canvas id="presenceChart"></canvas>
        </div>
    </div>

    <!-- Alertes étudiants à risque -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-xl font-semibold mb-4">Étudiants à risque</h2>
        
        @php
            $etudiantsRisque = [];
            foreach ($classes as $classe) {
                $etudiants = Etudiant::where('classe', $classe)->get();
                foreach ($etudiants as $etudiant) {
                    $presences = $etudiant->presences()->whereIn('seance_id', $enseignant->seances()->pluck('id'))->count();
                    $presents = $etudiant->presences()->whereIn('seance_id', $enseignant->seances()->pluck('id'))
                        ->where('statut', 'present')->count();
                    $taux = $presences > 0 ? ($presents / $presences) * 100 : 100;
                    
                    if ($taux < 40) {
                        $etudiantsRisque[] = [
                            'etudiant' => $etudiant,
                            'taux' => round($taux, 2),
                            'classe' => $classe
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
                        <th class="px-4 py-2 text-left">Étudiant</th>
                        <th class="px-4 py-2 text-left">Classe</th>
                        <th class="px-4 py-2 text-left">Taux</th>
                        <th class="px-4 py-2 text-left">Statut</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($etudiantsRisque as $item)
                    <tr class="border-b">
                        <td class="px-4 py-3">{{ $item['etudiant']->prenom }} {{ $item['etudiant']->nom }}</td>
                        <td class="px-4 py-3">{{ $item['classe'] }}</td>
                        <td class="px-4 py-3">{{ $item['taux'] }}%</td>
                        <td class="px-4 py-3">
                            @if($item['taux'] <= 25)
                            <span class="bg-red-100 text-red-800 px-2 py-1 rounded text-xs">Droppé</span>
                            @else
                            <span class="bg-orange-100 text-orange-800 px-2 py-1 rounded text-xs">À risque</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-gray-500">Aucun étudiant à risque identifié</p>
        @endif
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('presenceChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: @json($chartClasses),
                datasets: [{
                    label: 'Taux de présence (%)',
                    data: @json($chartData),
                    backgroundColor: @json(array_map(function($taux) {
                        if ($taux >= 80) return '#16a34a';
                        if ($taux >= 60) return '#22c55e';
                        if ($taux >= 40) return '#f97316';
                        return '#ef4444';
                    }, $chartData)),
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                },
                responsive: true,
                maintainAspectRatio: false
            }
        });
    });
</script>
@endpush
@endsection