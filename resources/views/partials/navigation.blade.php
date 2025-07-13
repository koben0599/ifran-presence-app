<nav class="bg-gradient-to-r from-blue-600 to-blue-800 shadow-lg fixed w-full z-20 top-0 left-0 backdrop-blur-sm">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <!-- Logo avec animation -->
        <a href="/" class="text-xl font-bold text-white hover:text-blue-200 transition-all duration-300 transform hover:scale-105 flex items-center space-x-2">
            <svg class="w-8 h-8 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10.394 2.08a1 1 0 00-.788 0l-7 3a1 1 0 000 1.84L5.25 8.051a.999.999 0 01.356-.257l4-1.714a1 1 0 11.788 1.838L7.667 9.088l1.94.831a1 1 0 00.787 0l7-3a1 1 0 000-1.838l-7-3zM3.31 9.397L5 10.12v4.102a8.969 8.969 0 00-1.05-.174 1 1 0 01-.89-.89 11.115 11.115 0 01.25-3.762zM9.3 16.573A9.026 9.026 0 007 14.935v-3.957l1.818.78a3 3 0 002.364 0l5.508-2.361a11.026 11.026 0 01.25 3.762 1 1 0 01-.89.89 8.968 8.968 0 00-5.35 2.524 1 1 0 01-1.4 0zM6 18a1 1 0 001-1v-2.065a8.935 8.935 0 00-2-.712V17a1 1 0 001 1z"/>
            </svg>
            <span>IFRAN</span>
        </a>

        <!-- Menu de navigation avec animations -->
        <div class="hidden md:flex items-center space-x-6">
            @php $user = Auth::user(); @endphp

            @if($user)
                {{-- ADMIN --}}
                @if(method_exists($user, 'isAdmin') && $user->isAdmin())
                    <a href="{{ route('emplois.index') }}" class="text-white hover:text-blue-200 transition-all duration-300 transform hover:scale-105 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Emplois du temps</span>
                    </a>
                    <a href="{{ route('admin.exports.index') }}" class="text-white hover:text-blue-200 transition-all duration-300 transform hover:scale-105 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span>Exports</span>
                    </a>
                    <a href="{{ route('statistiques.index') }}" class="text-white hover:text-blue-200 transition-all duration-300 transform hover:scale-105 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Statistiques</span>
                    </a>
                @endif

                {{-- ENSEIGNANT --}}
                @if(method_exists($user, 'isEnseignant') && $user->isEnseignant())
                    <a href="{{ route('seances.index') }}" class="text-white hover:text-blue-200 transition-all duration-300 transform hover:scale-105 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>Mes séances</span>
                    </a>
                    <a href="{{ route('statistiques.index') }}" class="text-white hover:text-blue-200 transition-all duration-300 transform hover:scale-105 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Statistiques</span>
                    </a>
                @endif

                {{-- COORDINATEUR --}}
                @if(method_exists($user, 'isCoordinateur') && $user->isCoordinateur())
                    <a href="{{ route('emplois.index') }}" class="text-white hover:text-blue-200 transition-all duration-300 transform hover:scale-105 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span>Emploi du temps</span>
                    </a>
                    <a href="{{ route('coordinateur.absences') }}" class="text-white hover:text-blue-200 transition-all duration-300 transform hover:scale-105 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Absences</span>
                    </a>
                    <a href="{{ route('statistiques.index') }}" class="text-white hover:text-blue-200 transition-all duration-300 transform hover:scale-105 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Statistiques</span>
                    </a>
                @endif

                {{-- ETUDIANT --}}
                @if(method_exists($user, 'isEtudiant') && $user->isEtudiant())
                    <a href="{{ route('etudiant.absences') }}" class="text-white hover:text-blue-200 transition-all duration-300 transform hover:scale-105 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span>Mes absences</span>
                    </a>
                    <a href="{{ route('etudiant.statistiques') }}" class="text-white hover:text-blue-200 transition-all duration-300 transform hover:scale-105 flex items-center space-x-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span>Statistiques</span>
                    </a>
                @endif
            @endif
        </div>

        <!-- Profil utilisateur avec photo/initiales et indicateur de connexion -->
        @if($user)
            <div class="flex items-center space-x-4">
                <!-- Bouton Notifications -->
                <button id="notification-btn" class="relative p-2 text-white hover:text-blue-200 focus:outline-none focus:ring-2 focus:ring-blue-500 rounded-lg transition-all duration-300 transform hover:scale-105">
                    <i class="fas fa-bell text-lg"></i>
                    <!-- Badge notifications non lues -->
                    <span id="notification-badge" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center hidden">
                        0
                    </span>
                </button>

                <!-- Indicateur de connexion -->
                <div class="flex items-center space-x-2">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse"></div>
                    <span class="text-white text-sm hidden sm:block">En ligne</span>
                </div>

                <!-- Photo/Initiales utilisateur -->
                <div class="relative group">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-lg shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 cursor-pointer">
                        @if($user->photo)
                            <img src="{{ asset('storage/' . $user->photo) }}" alt="Photo profil" class="w-full h-full rounded-full object-cover">
                        @else
                            {{ substr($user->prenom ?? $user->name, 0, 1) }}{{ substr($user->lastname ?? '', 0, 1) }}
                        @endif
                    </div>
                    
                    <!-- Dropdown menu -->
                    <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-300 transform scale-95 group-hover:scale-100">
                        <div class="px-4 py-2 border-b">
                            <p class="text-sm font-medium text-gray-900">{{ $user->prenom ?? $user->name }}</p>
                            <p class="text-xs text-gray-500 capitalize">{{ $user->role }}</p>
                        </div>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Profil</a>
                        <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Paramètres</a>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                            <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                </svg>
                    Déconnexion
                </button>
            </form>
        </div>
                </div>
            </div>
        @endif
    </div>
</nav>
