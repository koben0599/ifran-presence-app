@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-lg bg-white rounded shadow p-8">
        <h1 class="text-2xl font-bold text-blue-600 mb-6 text-center">Inscription</h1>
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf

            <div>
                <label for="name" class="block text-gray-700">Nom d'utilisateur</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-gray-700">Adresse email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('email') border-red-500 @enderror">
                @error('email')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password" class="block text-gray-700">Mot de passe</label>
                <input id="password" type="password" name="password" required
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('password') border-red-500 @enderror">
                @error('password')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="password-confirm" class="block text-gray-700">Confirmer le mot de passe</label>
                <input id="password-confirm" type="password" name="password_confirmation" required
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-200">
            </div>

            <div>
                <label for="nom" class="block text-gray-700">Nom</label>
                <input id="nom" type="text" name="nom" value="{{ old('nom') }}" required
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('nom') border-red-500 @enderror">
                @error('nom')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="prenom" class="block text-gray-700">Prénom</label>
                <input id="prenom" type="text" name="prenom" value="{{ old('prenom') }}" required
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('prenom') border-red-500 @enderror">
                @error('prenom')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="role" class="block text-gray-700">Rôle</label>
                <select id="role" name="role" required
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('role') border-red-500 @enderror">
                    <option value="">Sélectionnez un rôle</option>
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                    <option value="enseignant" {{ old('role') == 'enseignant' ? 'selected' : '' }}>Enseignant</option>
                    <option value="coordinateur" {{ old('role') == 'coordinateur' ? 'selected' : '' }}>Coordinateur</option>
                    <option value="etudiant" {{ old('role') == 'etudiant' ? 'selected' : '' }}>Étudiant</option>
                </select>
                @error('role')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div id="classe-field" style="display:none;">
                <label for="classe" class="block text-gray-700">Classe</label>
                <select id="classe" name="classe"
                    class="w-full border border-gray-300 rounded px-3 py-2 mt-1 focus:outline-none focus:ring-2 focus:ring-blue-200 @error('classe') border-red-500 @enderror">
                    <option value="">Sélectionnez une classe</option>
                    <option value="B3DEV" {{ old('classe') == 'B3DEV' ? 'selected' : '' }}>B3DEV</option>
                    <option value="B2DEV" {{ old('classe') == 'B2DEV' ? 'selected' : '' }}>B2DEV</option>
                    <option value="B3CREA" {{ old('classe') == 'B3CREA' ? 'selected' : '' }}>B3CREA</option>
                </select>
                @error('classe')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit"
                    class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700 transition">
                    S'inscrire
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        var roleSelect = document.getElementById('role');
        var classeField = document.getElementById('classe-field');
        function toggleClasseField() {
            classeField.style.display = roleSelect.value === 'etudiant' ? 'block' : 'none';
        }
        roleSelect.addEventListener('change', toggleClasseField);
        toggleClasseField(); // initial
    });
</script>
@endpush
