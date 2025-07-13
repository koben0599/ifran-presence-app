@extends('layouts.app')

@section('content')
<div class="container mx-auto py-8">
    <h1 class="text-2xl font-bold mb-6">Statistiques de présence - {{ $etudiant->nom }}</h1>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <!-- Graphique 1: Présence par module -->
        <div class="bg-white p-4 rounded shadow">
            <canvas id="moduleChart" height="200"></canvas>
        </div>

        <!-- Graphique 2: Évolution mensuelle -->
        <div class="bg-white p-4 rounded shadow">
            <canvas id="monthlyChart" height="200"></canvas>
        </div>
    </div>

    <!-- Graphique 3: Répartition des types -->
    <div class="bg-white p-4 rounded shadow max-w-md mx-auto">
        <canvas id="typeChart" height="250"></canvas>
    </div>
</div>

@section('scripts')
<script>
    // 1. Présence par module (Bar Chart)
    new Chart(document.getElementById('moduleChart'), {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($data['presenceParModule']->toArray())) !!},
            datasets: [{
                label: 'Taux de présence (%)',
                data: {!! json_encode(array_values($data['presenceParModule']->toArray())) !!},
                backgroundColor: 'rgba(54, 162, 235, 0.5)',
                borderColor: 'rgba(54, 162, 235, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100
                }
            }
        }
    });

    // 2. Évolution mensuelle (Line Chart)
    new Chart(document.getElementById('monthlyChart'), {
        type: 'line',
        data: {
            labels: {!! json_encode(array_keys($data['evolutionMensuelle']->toArray())) !!},
            datasets: [{
                label: 'Présences par mois',
                data: {!! json_encode(array_values($data['evolutionMensuelle']->toArray())) !!},
                fill: false,
                borderColor: 'rgb(75, 192, 192)',
                tension: 0.1
            }]
        }
    });

    // 3. Répartition des types (Doughnut Chart)
    new Chart(document.getElementById('typeChart'), {
        type: 'doughnut',
        data: {
            labels: ['Présent', 'Retard', 'Absent'],
            datasets: [{
                data: [
                    {{ $data['repartitionTypes']['present'] ?? 0 }},
                    {{ $data['repartitionTypes']['retard'] ?? 0 }},
                    {{ $data['repartitionTypes']['absent'] ?? 0 }}
                ],
                backgroundColor: [
                    'rgba(75, 192, 192, 0.7)',
                    'rgba(255, 206, 86, 0.7)',
                    'rgba(255, 99, 132, 0.7)'
                ]
            }]
        }
    });
</script>
@endsection
@endsection
