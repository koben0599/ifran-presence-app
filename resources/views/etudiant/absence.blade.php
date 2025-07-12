@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Mes Absences</h1>

    <div class="mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <h2 class="text-xl font-semibold mb-4">Statistiques Globales</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div class="bg-blue-100 p-4 rounded-lg">
                    <p class="text-sm text-blue-800">Taux de présence</p>
                    <p class="text-2xl font-bold">{{ $tauxGlobal }}%</p>
                </div>
                <div class="bg-green-100 p-4 rounded-lg">
                    <p class="text-sm text-green-800">Cours suivis</p>
                    <p class="text-2xl font-bold">{{ $presents }}</p>
                </div>
                <div class="bg-red-100 p-4 rounded-lg">
                    <p class="text-sm text-red-800">Absences</p>
                    <p class="text-2xl font-bold">{{ $totalPresences - $presents }}</p>
                </div>
            </div>
        </div>
    </div>

    @foreach($absences as $mois => $presences)
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4">{{ Carbon\Carbon::parse($mois)->translatedFormat('F Y') }}</h2>

        <div class="space-y-4">
            @foreach($presences as $presence)
            <div class="bg-white rounded-lg shadow p-4 border-l-4 @if($presence->justifie) border-blue-500 @else border-red-500 @endif">
                <div class="flex justify-between items-start">
                    <div>
                        <p class="font-semibold">{{ $presence->seance->module->nom }}</p>
                        <p class="text-gray-600">{{ $presence->seance->date_debut->format('d/m/Y H:i') }}</p>
                        <p class="text-sm @if($presence->seance->type_cours === 'presentiel') text-blue-600
                                          @elseif($presence->seance->type_cours === 'e-learning') text-purple-600
                                          @else text-green-600 @endif">
                            {{ ucfirst($presence->seance->type_cours) }}
                        </p>
                    </div>
                    <span class="px-3 py-1 rounded-full text-xs
                        @if($presence->justifie) bg-blue-100 text-blue-800 @else bg-red-100 text-red-800 @endif">
                        @if($presence->justifie) Justifiée @else Non justifiée @endif
                    </span>
                </div>

                @if($presence->justification)
                <div class="mt-3 p-3 bg-gray-50 rounded">
                    <p class="font-medium">Justification:</p>
                    <p class="text-gray-700">{{ $presence->justification->raison }}</p>

                    @if($presence->justification->fichier_justificatif)
                    <div class="mt-2">
                        <a href="{{ Storage::url($presence->justification->fichier_justificatif) }}"
                           target="_blank"
                           class="text-blue-600 hover:text-blue-800 flex items-center">
                            <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                            </svg>
                            Voir le justificatif
                        </a>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
    </div>
    @endforeach
</div>
@endsection
