@extends('layouts.app')

@section('content')
<div class="flex items-center justify-center min-h-screen bg-gray-100">
    <div class="w-full max-w-2xl bg-white rounded shadow p-8">
        <h1 class="text-2xl font-bold text-blue-600 mb-6 text-center">Tableau de bord</h1>

        @if (session('status'))
            <div class="mb-4 p-3 bg-green-100 text-green-800 rounded">
                {{ session('status') }}
            </div>
        @endif

        <p class="mb-6 text-gray-700 text-center">Bienvenue, {{ Auth::user()->prenom ?? Auth::user()->name }} !</p>

        {{-- Liens rapides selon le rôle --}}
        @php
            $user = Auth::user();
        @endphp

        @if($user->isAdmin())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('admin.emplois.index') }}" class="block bg-blue-600 text-white rounded p-4 text-center hover:bg-blue-700 transition">
                    Gérer les emplois du temps
                </a>
                <a href="#" class="block bg-green-600 text-white rounded p-4 text-center hover:bg-green-700 transition">
                    Statistiques & Graphiques
                </a>
                <a href="#" class="block bg-purple-600 text-white rounded p-4 text-center hover:bg-purple-700 transition">
                    Gestion des utilisateurs
                </a>
            </div>
        @elseif($user->isEnseignant())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('enseignant.dashboard') }}" class="block bg-blue-600 text-white rounded p-4 text-center hover:bg-blue-700 transition">
                    Mes séances de cours
                </a>
                <a href="#" class="block bg-green-600 text-white rounded p-4 text-center hover:bg-green-700 transition">
                    Saisir les présences
                </a>
            </div>
        @elseif($user->isCoordinateur())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('coordinateur.absences') }}" class="block bg-blue-600 text-white rounded p-4 text-center hover:bg-blue-700 transition">
                    Gérer les absences
                </a>
                <a href="#" class="block bg-green-600 text-white rounded p-4 text-center hover:bg-green-700 transition">
                    Justifier une absence
                </a>
                <a href="#" class="block bg-purple-600 text-white rounded p-4 text-center hover:bg-purple-700 transition">
                    Emploi du temps
                </a>
            </div>
        @elseif($user->isEtudiant())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <a href="{{ route('etudiant.absences') }}" class="block bg-blue-600 text-white rounded p-4 text-center hover:bg-blue-700 transition">
                    Mes absences
                </a>
                <a href="{{ route('etudiant.statistiques') }}" class="block bg-green-600 text-white rounded p-4 text-center hover:bg-green-700 transition">
                    Mes statistiques d’assiduité
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
