@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Statistiques de Mon Enfant</h1>
                    <p class="text-gray-600">Suivi de l'assiduité de {{ $etudiant->nom ?? 'votre enfant' }}</p>
                </div>
            </div>
        </div>

        @if(isset($message))
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-8">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-yellow-700">{{ $message }}</p>
                    </div>
                </div>
            </div>
        @else
            <!-- Graphique 1: Présence par séance cette semaine -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Présence par Séance - Semaine Actuelle</h2>
                <div class="h-80">
                    <canvas id="presenceSemaine"></canvas>
                </div>
            </div>

            <!-- Graphique 2: Assiduité par trimestre -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Assiduité par Trimestre</h2>
                <div class="h-80">
                    <canvas id="assiduiteTrimestre"></canvas>
                </div>
            </div>

            <!-- Graphique 3: Assiduité de la classe par trimestre -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
                <h2 class="text-xl font-semibold text-gray-900 mb-4">Assiduité de la Classe par Trimestre</h2>
                <div class="h-80">
                    <canvas id="assiduiteClasseTrimestre"></canvas>
                </div>
            </div>

            <!-- Tableau détaillé de la semaine -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Détail des Séances de la Semaine</h3>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Heure</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Module</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($presenceSemaine as $seance)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $seance['date'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $seance['heure'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $seance['module'] }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($seance['statut'] === 'present')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                Présent
                                            </span>
                                        @elseif($seance['statut'] === 'absent')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                                Absent
                                            </span>
                                        @elseif($seance['statut'] === 'retard')
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                                Retard
                                            </span>
                                        @else
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                Non enregistré
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</div>

@if(!isset($message))
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Données PHP converties en JavaScript
const presenceSemaine = @json($presenceSemaine);
const assiduiteTrimestre = @json($assiduiteTrimestre);
const assiduiteClasseTrimestre = @json($assiduiteClasseTrimestre);

// Fonction pour déterminer la couleur selon le statut
function getColorByStatut(statut) {
    switch(statut) {
        case 'present': return '#10b981'; // Vert
        case 'absent': return '#ef4444'; // Rouge
        case 'retard': return '#f59e0b'; // Orange
        default: return '#6b7280'; // Gris
    }
}

// Graphique 1: Présence par séance cette semaine
const ctx1 = document.getElementById('presenceSemaine').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: presenceSemaine.map(item => `${item.date} ${item.heure}`),
        datasets: [{
            label: 'Statut de Présence',
            data: presenceSemaine.map(item => {
                switch(item.statut) {
                    case 'present': return 100;
                    case 'absent': return 0;
                    case 'retard': return 50;
                    default: return 25;
                }
            }),
            backgroundColor: presenceSemaine.map(item => getColorByStatut(item.statut)),
            borderColor: presenceSemaine.map(item => getColorByStatut(item.statut)),
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const index = context.dataIndex;
                        const seance = presenceSemaine[index];
                        return `${seance.module}: ${seance.statut}`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        if (value === 100) return 'Présent';
                        if (value === 50) return 'Retard';
                        if (value === 25) return 'Non enregistré';
                        return 'Absent';
                    }
                }
            },
            x: {
                ticks: {
                    maxRotation: 45,
                    minRotation: 45
                }
            }
        }
    }
});

// Graphique 2: Assiduité par trimestre
const ctx2 = document.getElementById('assiduiteTrimestre').getContext('2d');
new Chart(ctx2, {
    type: 'line',
    data: {
        labels: assiduiteTrimestre.map(item => item.trimestre),
        datasets: [{
            label: 'Taux d\'Assiduité (%)',
            data: assiduiteTrimestre.map(item => item.taux),
            borderColor: '#3b82f6',
            backgroundColor: 'rgba(59, 130, 246, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `Taux: ${context.parsed.y}%`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});

// Graphique 3: Assiduité de la classe par trimestre
const ctx3 = document.getElementById('assiduiteClasseTrimestre').getContext('2d');
new Chart(ctx3, {
    type: 'line',
    data: {
        labels: assiduiteClasseTrimestre.map(item => item.trimestre),
        datasets: [{
            label: 'Taux d\'Assiduité de la Classe (%)',
            data: assiduiteClasseTrimestre.map(item => item.taux),
            borderColor: '#10b981',
            backgroundColor: 'rgba(16, 185, 129, 0.1)',
            borderWidth: 3,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `Taux: ${context.parsed.y}%`;
                    }
                }
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: function(value) {
                        return value + '%';
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endif
@endsection 