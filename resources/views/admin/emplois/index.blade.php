@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold">Gestion des Emplois du Temps</h1>
        <div class="flex items-center space-x-4">
            <a href="{{ route('admin.emplois.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Ajouter un Cours
            </a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700">
                    Déconnexion
                </button>
            </form>
        </div>
    </div>

    @foreach($emplois as $classe => $cours)
    <div class="mb-8">
        <h2 class="text-xl font-semibold mb-4 bg-gray-200 p-2 rounded">{{ $classe }}</h2>

        <div class="overflow-x-auto bg-white rounded-lg shadow">
            <table class="min-w-full">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2">Jour</th>
                        <th class="px-4 py-2">Heure</th>
                        <th class="px-4 py-2">Module</th>
                        <th class="px-4 py-2">Enseignant</th>
                        <th class="px-4 py-2">Type</th>
                        <th class="px-4 py-2">Salle</th>
                        <th class="px-4 py-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($cours as $cour)
                    <tr class="border-b hover:bg-gray-50">
                        <td class="px-4 py-3">{{ ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'][$cour->jour_semaine - 1] }}</td>
                        <td class="px-4 py-3">{{ $cour->heure_debut }} - {{ $cour->heure_fin }}</td>
                        <td class="px-4 py-3">{{ $cour->module->nom }}</td>
                        <td class="px-4 py-3">{{ $cour->enseignant->nom }}</td>
                        <td class="px-4 py-3">
                            <span class="px-2 py-1 text-xs rounded
                                @if($cour->type_cours === 'presentiel') bg-blue-100 text-blue-800
                                @elseif($cour->type_cours === 'e-learning') bg-purple-100 text-purple-800
                                @else bg-green-100 text-green-800 @endif">
                                {{ ucfirst($cour->type_cours) }}
                            </span>
                        </td>
                        <td class="px-4 py-3">{{ $cour->salle }}</td>
                        <td class="px-4 py-3 flex space-x-2">
                            <a href="{{ route('admin.emplois.edit', $cour) }}" class="text-yellow-600 hover:text-yellow-800">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"/>
                                </svg>
                            </a>
                            @if($cour->est_actif)
                            <form action="{{ route('admin.emplois.annuler', $cour) }}" method="POST">
                                @csrf
                                <button type="submit" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </form>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endforeach
</div>
@endsection
