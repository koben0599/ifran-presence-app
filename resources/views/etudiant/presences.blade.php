@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto px-4">
    <h2 class="text-2xl font-bold text-blue-700 mb-4">Mes présences</h2>
    <div class="overflow-x-auto bg-white rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Date</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Module</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Enseignant</th>
                    <th class="px-4 py-2 text-left text-xs font-semibold text-gray-600">Statut</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($presences as $presence)
                <tr class="hover:bg-blue-50">
                    <td class="px-4 py-3">{{ $presence->seance->date }}</td>
                    <td class="px-4 py-3">{{ $presence->seance->module->nom }}</td>
                    <td class="px-4 py-3">{{ $presence->seance->enseignant->nom }}</td>
                    <td class="px-4 py-3">
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
</div>
@endsection