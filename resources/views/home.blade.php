@extends('layouts.app')

@section('title', 'Tableau de bord - IFRAN Presence')

@section('page-header')
<div class="bg-gradient-to-r from-blue-600 to-purple-600 rounded-xl shadow-xl p-4 sm:p-6 lg:p-8 text-white">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
        <div>
            <h1 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-2">Tableau de bord</h1>
            <p class="text-blue-100 text-base sm:text-lg">Bienvenue, {{ Auth::user()->prenom ?? Auth::user()->name }} !</p>
            <div class="flex flex-col sm:flex-row sm:items-center mt-4 space-y-2 sm:space-y-0 sm:space-x-4">
                <div class="flex items-center space-x-2">
                    <div class="w-3 h-3 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-sm">En ligne</span>
                </div>
                <span class="hidden sm:inline text-sm text-blue-200">•</span>
                <span class="text-sm text-blue-200 capitalize">{{ Auth::user()->role }}</span>
            </div>
        </div>
        <div class="hidden sm:block">
            <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-full bg-white/20 flex items-center justify-center">
                <svg class="w-8 h-8 sm:w-10 sm:h-10 text-white" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
                </svg>
            </div>
        </div>
    </div>
</div>
@endsection

@section('content')
<!-- Messages de statut -->
        @if (session('status'))
    <div class="mb-4 sm:mb-6 p-3 sm:p-4 bg-green-100 border border-green-400 text-green-700 rounded-lg animate-fade-in-up">
        <div class="flex items-center">
            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
            </svg>
            <span class="text-sm sm:text-base">{{ session('status') }}</span>
        </div>
            </div>
        @endif

<!-- Cartes d'action selon le rôle -->
@php $user = Auth::user(); @endphp

        @if($user->isAdmin())
    <!-- Dashboard Admin -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="hover-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-blue-500 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-blue-600">12</span>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Emplois du temps</h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-4">Gérer les emplois du temps de tous les modules</p>
            <a href="{{ route('emplois.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium text-sm">
                Accéder
                <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="hover-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-green-500 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-green-600">85%</span>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Statistiques</h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-4">Analyser les données de présence</p>
            <a href="#" class="inline-flex items-center text-green-600 hover:text-green-700 font-medium text-sm">
                Voir les stats
                <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="hover-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-purple-500 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-purple-100 rounded-lg flex items-center justify-center group-hover:bg-purple-200 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-purple-600">150</span>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Utilisateurs</h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-4">Gérer tous les utilisateurs du système</p>
            <a href="#" class="inline-flex items-center text-purple-600 hover:text-purple-700 font-medium text-sm">
                Gérer
                <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>

@elseif($user->isEnseignant())
    <!-- Dashboard Enseignant -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="hover-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-blue-500 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-blue-600">8</span>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Mes séances</h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-4">Consulter vos séances de cours</p>
            <a href="{{ route('enseignant.dashboard') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium text-sm">
                Voir mes séances
                <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="hover-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-green-500 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-green-600">3</span>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Saisir présences</h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-4">Enregistrer les présences des étudiants</p>
            <a href="#" class="inline-flex items-center text-green-600 hover:text-green-700 font-medium text-sm">
                Commencer
                <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>

@elseif($user->isCoordinateur())
    <!-- Dashboard Coordinateur avec fonctionnalités avancées -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="hover-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-blue-500 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-blue-600">25</span>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Absences</h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-4">Gérer les absences des étudiants</p>
            <a href="{{ route('coordinateur.absences') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium text-sm">
                Gérer
                <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="hover-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-orange-500 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-orange-100 rounded-lg flex items-center justify-center group-hover:bg-orange-200 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-orange-600">12</span>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Modifier EDT</h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-4">Modifier les emplois du temps</p>
            <a href="#" class="inline-flex items-center text-orange-600 hover:text-orange-700 font-medium text-sm">
                Modifier
                <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="hover-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-indigo-500 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-indigo-100 rounded-lg flex items-center justify-center group-hover:bg-indigo-200 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-indigo-600">5</span>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Nouvelles séances</h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-4">Créer de nouvelles séances</p>
            <a href="#" class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium text-sm">
                Créer
                <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
    </div>

    <!-- Section fonctionnalités avancées -->
    <div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 mb-6 sm:mb-8">
        <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">Fonctionnalités avancées</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
            <div class="p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-blue-300 transition-colors">
                <h3 class="font-semibold text-gray-800 mb-2 text-sm sm:text-base">Saisie présences Learning</h3>
                <p class="text-gray-600 text-xs sm:text-sm mb-3">Saisir les présences pour les cours en Learning</p>
                <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-medium">
                    Commencer la saisie
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            <div class="p-3 sm:p-4 border border-gray-200 rounded-lg hover:border-blue-300 transition-colors">
                <h3 class="font-semibold text-gray-800 mb-2 text-sm sm:text-base">Saisie présences Workshop</h3>
                <p class="text-gray-600 text-xs sm:text-sm mb-3">Saisir les présences pour les ateliers</p>
                <a href="#" class="inline-flex items-center text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-medium">
                    Commencer la saisie
                    <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>

        @elseif($user->isEtudiant())
    <!-- Dashboard Étudiant -->
    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6 mb-6 sm:mb-8">
        <div class="hover-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-blue-500 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-blue-100 rounded-lg flex items-center justify-center group-hover:bg-blue-200 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-blue-600">3</span>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Mes absences</h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-4">Consulter vos absences</p>
            <a href="{{ route('etudiant.absences') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 font-medium text-sm">
                Voir mes absences
                <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="hover-card bg-white rounded-xl shadow-lg p-4 sm:p-6 border-l-4 border-green-500 transition-all duration-300 cursor-pointer group">
            <div class="flex items-center justify-between mb-4">
                <div class="w-10 h-10 sm:w-12 sm:h-12 bg-green-100 rounded-lg flex items-center justify-center group-hover:bg-green-200 transition-colors">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <span class="text-xl sm:text-2xl font-bold text-green-600">92%</span>
            </div>
            <h3 class="text-base sm:text-lg font-semibold text-gray-800 mb-2">Assiduité</h3>
            <p class="text-gray-600 text-xs sm:text-sm mb-4">Voir vos statistiques d'assiduité</p>
            <a href="{{ route('etudiant.statistiques') }}" class="inline-flex items-center text-green-600 hover:text-green-700 font-medium text-sm">
                Voir les stats
                <svg class="w-3 h-3 sm:w-4 sm:h-4 ml-1 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>
            </div>
        @endif

<!-- Section statistiques rapides -->
<div class="bg-white rounded-xl shadow-lg p-4 sm:p-6 scroll-animate">
    <h2 class="text-xl sm:text-2xl font-bold text-gray-800 mb-4 sm:mb-6">Statistiques rapides</h2>
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="text-center p-3 sm:p-4 bg-blue-50 rounded-lg">
            <div class="text-lg sm:text-2xl font-bold text-blue-600">85%</div>
            <div class="text-xs sm:text-sm text-gray-600">Taux de présence</div>
        </div>
        <div class="text-center p-3 sm:p-4 bg-green-50 rounded-lg">
            <div class="text-lg sm:text-2xl font-bold text-green-600">12</div>
            <div class="text-xs sm:text-sm text-gray-600">Séances cette semaine</div>
        </div>
        <div class="text-center p-3 sm:p-4 bg-yellow-50 rounded-lg">
            <div class="text-lg sm:text-2xl font-bold text-yellow-600">3</div>
            <div class="text-xs sm:text-sm text-gray-600">Absences</div>
        </div>
        <div class="text-center p-3 sm:p-4 bg-purple-50 rounded-lg">
            <div class="text-lg sm:text-2xl font-bold text-purple-600">92%</div>
            <div class="text-xs sm:text-sm text-gray-600">Satisfaction</div>
        </div>
    </div>
</div>
@endsection
