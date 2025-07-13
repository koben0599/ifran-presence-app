@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- En-tête -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Mon Emploi du Temps</h1>
                <p class="text-gray-600 mt-2">Planning hebdomadaire de vos cours</p>
            </div>
            <div class="flex items-center space-x-4">
                <form method="POST" action="{{ route('enseignant.generer-seances') }}" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Générer les séances
                    </button>
                </form>
                <a href="{{ route('enseignant.planning-semaine') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Voir le planning
                </a>
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

    <!-- Grille de l'emploi du temps -->
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- En-tête du tableau -->
        <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Planning hebdomadaire</h2>
            <p class="text-sm text-gray-600 mt-1">Cliquez sur "Générer les séances" pour créer les séances de la semaine courante</p>
        </div>

        <!-- Tableau de l'emploi du temps -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horaire</th>
                        @foreach($joursSemaine as $jour)
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ ucfirst($jour) }}
                        </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @php
                        $horaires = [
                            '08:00' => '08:00-10:00',
                            '10:00' => '10:00-12:00',
                            '14:00' => '14:00-16:00',
                            '16:00' => '16:00-18:00'
                        ];
                    @endphp

                    @foreach($horaires as $heure => $plage)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $plage }}
                        </td>
                        @foreach($joursSemaine as $jour)
                        <td class="px-6 py-4 whitespace-nowrap">
                            @php
                                $emplois = $emploiOrganise[$jour] ?? collect();
                                $emploi = $emplois->where('heure_debut', $heure)->first();
                            @endphp

                            @if($emploi)
                            <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <h4 class="text-sm font-semibold text-blue-900">
                                            {{ $emploi->module->nom ?? 'Module non défini' }}
                                        </h4>
                                        <p class="text-xs text-blue-700 mt-1">
                                            {{ $emploi->classe->nom ?? 'Classe non définie' }}
                                        </p>
                                        <p class="text-xs text-blue-600 mt-1">
                                            {{ $emploi->salle }}
                                        </p>
                                    </div>
                                    <div class="ml-2">
                                        @if($emploi->type === 'presentiel')
                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                Présentiel
                                            </span>
                                        @elseif($emploi->type === 'elearning')
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
                            </div>
                            @else
                            <div class="text-gray-400 text-sm">-</div>
                            @endif
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Informations -->
    <div class="mt-8 grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-blue-900">Cours Présentiel</h3>
                    <p class="text-sm text-blue-700">En salle de classe</p>
                </div>
            </div>
        </div>

        <div class="bg-purple-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-purple-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-purple-900">E-Learning</h3>
                    <p class="text-sm text-purple-700">Cours en ligne</p>
                </div>
            </div>
        </div>

        <div class="bg-orange-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="w-8 h-8 bg-orange-100 rounded-full flex items-center justify-center mr-3">
                    <svg class="w-4 h-4 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="font-semibold text-orange-900">Workshop</h3>
                    <p class="text-sm text-orange-700">Exercices pratiques</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions rapides -->
    <div class="mt-8 bg-gray-50 border border-gray-200 rounded-lg p-4">
        <div class="flex items-center justify-between">
            <div>
                <h3 class="text-sm font-medium text-gray-800">Actions rapides</h3>
                <p class="text-sm text-gray-600 mt-1">
                    Accédez rapidement à vos séances et à la saisie des présences
                </p>
            </div>
            <div class="flex items-center space-x-4">
                <a href="{{ route('enseignant.seances') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                    Mes séances
                </a>
                <a href="{{ route('enseignant.presences') }}" class="inline-flex items-center px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    Saisir présences
                </a>
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