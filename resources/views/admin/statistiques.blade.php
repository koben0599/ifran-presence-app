@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- En-tête -->
        <div class="mb-8">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 mb-2">Statistiques de Présence</h1>
                    <p class="text-gray-600">Tableau de bord pour l'équipe pédagogique</p>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="bg-white rounded-lg shadow-sm p-4">
                        <div class="text-sm text-gray-500">Période</div>
                        <div class="text-lg font-bold text-gray-900">{{ now()->format('d/m/Y') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Graphique 1: Taux de présence par étudiant -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Taux de Présence par Étudiant</h2>
            <div class="h-96">
                <canvas id="presenceEtudiants"></canvas>
            </div>
        </div>

        <!-- Graphique 2: Taux de présence par classe -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Taux de Présence par Classe</h2>
            <div class="h-80">
                <canvas id="presenceClasses"></canvas>
            </div>
        </div>

        <!-- Graphique 3: Volume de cours par type -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 mb-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-4">Volume de Cours par Type</h2>
            <div class="h-80">
                <canvas id="volumeCours"></canvas>
            </div>
        </div>

        <!-- Légende des couleurs -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Légende des Couleurs</h3>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-700 rounded mr-2"></div>
                    <span class="text-sm text-gray-700">≥ 85% (Excellent)</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-green-400 rounded mr-2"></div>
                    <span class="text-sm text-gray-700">70-84% (Bon)</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-orange-500 rounded mr-2"></div>
                    <span class="text-sm text-gray-700">50-69% (Moyen)</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 bg-red-500 rounded mr-2"></div>
                    <span class="text-sm text-gray-700">&lt; 50% (Faible)</span>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Données PHP converties en JavaScript
const presenceParEtudiant = @json($presenceParEtudiant);
const presenceParClasse = @json($presenceParClasse);
const volumeCours = @json($volumeCours);

// Fonction pour déterminer la couleur selon le taux
function getColorByTaux(taux) {
    if (taux >= 85) return '#15803d'; // Vert foncé
    if (taux >= 70) return '#4ade80'; // Vert clair
    if (taux >= 50) return '#f97316'; // Orange
    return '#ef4444'; // Rouge
}

// Graphique 1: Taux de présence par étudiant
const ctx1 = document.getElementById('presenceEtudiants').getContext('2d');
new Chart(ctx1, {
    type: 'bar',
    data: {
        labels: presenceParEtudiant.map(item => item.nom),
        datasets: [{
            label: 'Taux de Présence (%)',
            data: presenceParEtudiant.map(item => item.taux),
            backgroundColor: presenceParEtudiant.map(item => getColorByTaux(item.taux)),
            borderColor: presenceParEtudiant.map(item => getColorByTaux(item.taux)),
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

// Graphique 2: Taux de présence par classe
const ctx2 = document.getElementById('presenceClasses').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: presenceParClasse.map(item => item.classe),
        datasets: [{
            data: presenceParClasse.map(item => item.taux),
            backgroundColor: [
                '#3b82f6', '#8b5cf6', '#06b6d4', '#10b981', '#f59e0b', '#ef4444'
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return `${context.label}: ${context.parsed}%`;
                    }
                }
            }
        }
    }
});

// Graphique 3: Volume de cours par type
const ctx3 = document.getElementById('volumeCours').getContext('2d');
new Chart(ctx3, {
    type: 'pie',
    data: {
        labels: ['Présentiel', 'E-learning', 'Workshop'],
        datasets: [{
            data: [volumeCours.presentiel, volumeCours['e-learning'], volumeCours.workshop],
            backgroundColor: [
                '#3b82f6', // Bleu pour présentiel
                '#8b5cf6', // Violet pour e-learning
                '#10b981'  // Vert pour workshop
            ],
            borderWidth: 2,
            borderColor: '#ffffff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((context.parsed / total) * 100).toFixed(1);
                        return `${context.label}: ${context.parsed} cours (${percentage}%)`;
                    }
                }
            }
        }
    }
});
</script>
@endpush
@endsection 