@extends('layouts.app')

@section('content')
<div class="bg-white rounded-lg shadow p-6">
    <h2 class="text-2xl font-bold mb-4">Saisie des présences - {{ $seance->module->nom }}</h2>
    <p class="mb-4">Classe: {{ $seance->classe }} | Date: {{ $seance->date_debut->format('d/m/Y H:i') }}</p>

    <form action="{{ route('presences.enregistrer', $seance) }}" method="POST">
        @csrf

        <div class="overflow-x-auto">
            <table class="min-w-full bg-white">
                <thead>
                    <tr class="bg-gray-200">
                        <th class="py-2 px-4">Étudiant</th>
                        <th class="py-2 px-4">Présent</th>
                        <th class="py-2 px-4">Retard</th>
                        <th class="py-2 px-4">Absent</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($etudiants as $etudiant)
                    <tr class="border-b">
                        <td class="py-3 px-4">
                            <div class="flex items-center">
                                <img src="{{ asset($etudiant->photo ?? 'images/default-profile.png') }}"
                                     alt="{{ $etudiant->prenom }} {{ $etudiant->nom }}"
                                     class="w-10 h-10 rounded-full mr-3">
                                <span>{{ $etudiant->prenom }} {{ $etudiant->nom }}</span>
                            </div>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <input type="radio" name="presences[{{ $etudiant->id }}]" value="present"
                                   {{ optional($etudiant->presences->where('seance_id', $seance->id)->first())->statut === 'present' ? 'checked' : '' }}>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <input type="radio" name="presences[{{ $etudiant->id }}]" value="retard"
                                   {{ optional($etudiant->presences->where('seance_id', $seance->id)->first())->statut === 'retard' ? 'checked' : '' }}>
                        </td>
                        <td class="py-3 px-4 text-center">
                            <input type="radio" name="presences[{{ $etudiant->id }}]" value="absent"
                                   {{ optional($etudiant->presences->where('seance_id', $seance->id)->first())->statut === 'absent' ? 'checked' : '' }}>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-6 flex justify-end">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Enregistrer les présences
            </button>
        </div>
    </form>
</div>
@endsection
