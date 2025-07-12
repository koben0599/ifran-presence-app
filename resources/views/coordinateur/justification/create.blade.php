@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-2xl font-bold mb-4">Justifier une absence</h2>

        <div class="mb-6 p-4 bg-gray-100 rounded">
            <p><strong>Étudiant:</strong> {{ $presence->etudiant->prenom }} {{ $presence->etudiant->nom }}</p>
            <p><strong>Date:</strong> {{ $presence->seance->date_debut->format('d/m/Y H:i') }}</p>
            <p><strong>Module:</strong> {{ $presence->seance->module->nom }}</p>
        </div>

        <form action="{{ route('coordinateur.justifications.store', $presence) }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="mb-4">
                <label for="raison" class="block text-gray-700 mb-2">Raison de l'absence</label>
                <textarea name="raison" id="raison" rows="4" class="w-full px-3 py-2 border rounded" required></textarea>
            </div>

            <div class="mb-4">
                <label for="fichier_justificatif" class="block text-gray-700 mb-2">Fichier justificatif (optionnel)</label>
                <input type="file" name="fichier_justificatif" id="fichier_justificatif" class="border rounded p-2">
                <p class="text-sm text-gray-500 mt-1">Formats acceptés: PDF, JPG, PNG (max 2MB)</p>
            </div>

            <div class="flex justify-end space-x-4">
                <a href="{{ route('coordinateur.absences') }}" class="bg-gray-500 text-white px-4 py-2 rounded hover:bg-gray-600">
                    Annuler
                </a>
                <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    Enregistrer la justification
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
