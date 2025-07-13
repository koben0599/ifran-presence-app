@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4">
    <h2 class="text-2xl font-bold text-blue-700 mb-4">Mes enfants</h2>
    @foreach($enfants as $enfant)
        <div class="mb-6 p-4 bg-white rounded shadow">
            <h3 class="text-lg font-semibold mb-2">{{ $enfant->prenom }} {{ $enfant->nom }} (Classe : {{ $enfant->classe->nom ?? '-' }})</h3>
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Date</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Module</th>
                        <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Statut</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($enfant->presencesEtudiant as $presence)
                        <tr>
                            <td class="px-4 py-2">{{ $presence->seance->date_debut->format('d/m/Y') }}</td>
                            <td class="px-4 py-2">{{ $presence->seance->module->nom ?? '-' }}</td>
                            <td class="px-4 py-2">
                                @if($presence->statut == 'present')
                                    <span class="inline-block px-3 py-1 rounded-full bg-green-100 text-green-800 text-xs font-semibold">Présent</span>
                                @elseif($presence->statut == 'retard')
                                    <span class="inline-block px-3 py-1 rounded-full bg-yellow-100 text-yellow-800 text-xs font-semibold">Retard</span>
                                @else
                                    <span class="inline-block px-3 py-1 rounded-full bg-red-100 text-red-800 text-xs font-semibold">Absent</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endforeach
</div>
@endsection 