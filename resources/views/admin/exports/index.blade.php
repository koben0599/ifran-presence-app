@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <h1 class="text-2xl font-bold mb-6">Export des données de présence</h1>
    
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('admin.exports.export') }}" method="POST">
            @csrf
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <label for="classe" class="block text-gray-700 mb-2">Classe</label>
                    <select name="classe" id="classe" class="w-full border rounded px-3 py-2" required>
                        <option value="">Sélectionnez une classe</option>
                        @foreach($classes as $classe)
                        <option value="{{ $classe }}">{{ $classe }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="module_id" class="block text-gray-700 mb-2">Module (optionnel)</label>
                    <select name="module_id" id="module_id" class="w-full border rounded px-3 py-2">
                        <option value="">Tous les modules</option>
                        @foreach($modules as $module)
                        <option value="{{ $module->id }}">{{ $module->nom }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div>
                    <label for="date_debut" class="block text-gray-700 mb-2">Date de début</label>
                    <input type="date" name="date_debut" id="date_debut" class="w-full border rounded px-3 py-2" required>
                </div>
                
                <div>
                    <label for="date_fin" class="block text-gray-700 mb-2">Date de fin</label>
                    <input type="date" name="date_fin" id="date_fin" class="w-full border rounded px-3 py-2" required>
                </div>
            </div>
            
            <div class="mb-6">
                <label class="block text-gray-700 mb-2">Format d'export</label>
                <div class="flex space-x-4">
                    <label class="inline-flex items-center">
                        <input type="radio" name="format" value="excel" class="form-radio" checked>
                        <span class="ml-2">Excel</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="format" value="csv" class="form-radio">
                        <span class="ml-2">CSV</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="radio" name="format" value="pdf" class="form-radio">
                        <span class="ml-2">PDF</span>
                    </label>
                </div>
            </div>
            
            <div class="flex justify-end">
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M3 17a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zm3.293-7.707a1 1 0 011.414 0L9 10.586V3a1 1 0 112 0v7.586l1.293-1.293a1 1 0 111.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd"/>
                    </svg>
                    Exporter les données
                </button>
            </div>
        </form>
    </div>
</div>
@endsection