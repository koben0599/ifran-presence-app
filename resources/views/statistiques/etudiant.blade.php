@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Statistiques de présence pour {{ $etudiant->nom }}</h1>
    
    <!-- Filtres -->
    <div class="mb-6 flex flex-col sm:flex-row space-y-4 sm:space-y-0 sm:space-x-4">
        <div class="w-full sm:w-auto">
            <select id="periodeFilter" class="w-full border rounded px-4 py-2">
                <option value="all">Toutes périodes</option>
                <option value="week">Cette semaine</option>
                <option value="month">Ce mois</option>
                <option value="year">Cette année</option>
            </select>
        </div>
        
        <div class="w-full sm:w-auto">
            <select id="moduleFilter" class="w-full border rounded px-4 py-2">
                <option value="all">Tous les modules</option>
                @foreach($modules as $module)
                    <option value="{{ $module->id }}">{{ $module->nom }}</option>
                @endforeach
            </select>
        </div>
        
        <button id="filterButton" class="w-full sm:w-auto bg-blue-600 text-white px-6 py-2 rounded hover:bg-blue-700">
            Appliquer
        </button>
    </div>

    <!-- Graphiques -->
    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <canvas id="presenceChart" height="100"></canvas>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow">
            <canvas id="monthlyChart" height="200"></canvas>
        </div>
        <div class="bg-white p-6 rounded-lg shadow">
            <canvas id="typeChart" height="200"></canvas>
        </div>
    </div>
</div>

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Initialisation des graphiques
    const presenceChart = new Chart(
        document.getElementById('presenceChart'),
        { type: 'bar', data: @json($data), options: chartOptions('Taux de présence par module') }
    );

    const monthlyChart = new Chart(
        document.getElementById('monthlyChart'), 
        { type: 'line', data: @json($monthlyData), options: chartOptions('Évolution mensuelle') }
    );

    const typeChart = new Chart(
        document.getElementById('typeChart'),
        { 
            type: 'doughnut', 
            data: {
                labels: ['Présent', 'Retard', 'Absent'],
                datasets: [{
                    data: @json($presenceTypes),
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.8)',
                        'rgba(255, 206, 86, 0.8)',
                        'rgba(255, 99, 132, 0.8)'
                    ]
                }]
            }
        }
    );

    // Options communes
    function chartOptions(title) {
        return {
            responsive: true,
            plugins: {
                title: {
                    display: true,
                    text: title
                },
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    title: { display: true, text: 'Pourcentage (%)' }
                }
            }
        };
    }

    // Gestion des filtres
    document.getElementById('filterButton').addEventListener('click', function() {
        const periode = document.getElementById('periodeFilter').value;
        const moduleId = document.getElementById('moduleFilter').value;
        
        fetch(`/api/statistiques/{{ $etudiant->id }}?periode=${periode}&module_id=${moduleId}`)
            .then(response => response.json())
            .then(data => {
                // Mise à jour des graphiques
                presenceChart.data.datasets[0].data = data.presenceData;
                monthlyChart.data.datasets[0].data = data.monthlyData;
                typeChart.data.datasets[0].data = data.presenceTypes;
                
                presenceChart.update();
                monthlyChart.update();
                typeChart.update();
            });
    });
</script>
@endsection
@endsection
