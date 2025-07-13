<aside class="bg-white shadow-lg w-64 min-h-screen fixed left-0 top-16 z-10 transform transition-transform duration-300 ease-in-out" id="sidebar">
    <div class="p-4 sm:p-6">
        <!-- Profil utilisateur -->
        <div class="flex items-center space-x-3 mb-6 sm:mb-8 p-3 sm:p-4 bg-gradient-to-r from-blue-50 to-purple-50 rounded-xl">
            <div class="w-10 h-10 sm:w-12 sm:h-12 rounded-full bg-gradient-to-r from-blue-400 to-purple-500 flex items-center justify-center text-white font-bold text-sm sm:text-lg shadow-lg">
                @if(Auth::user()->photo)
                    <img src="{{ asset('storage/' . Auth::user()->photo) }}" alt="Photo profil" class="w-full h-full rounded-full object-cover">
                @else
                    {{ Auth::user()->initials }}
                @endif
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm sm:text-base font-semibold text-gray-800 truncate">{{ Auth::user()->display_name }}</p>
                <p class="text-xs sm:text-sm text-gray-500 capitalize">{{ Auth::user()->role }}</p>
                <div class="flex items-center mt-1">
                    <div class="w-2 h-2 bg-green-400 rounded-full animate-pulse mr-2"></div>
                    <span class="text-xs text-green-600">En ligne</span>
                </div>
            </div>
    </div>

        <!-- Menu de navigation -->
        <nav class="space-y-2">
        @php $user = Auth::user(); @endphp

            <!-- Accueil -->
            <a href="{{ route('home') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span class="text-sm sm:text-base font-medium">Accueil</span>
            </a>

        @if($user->isAdmin())
                <!-- Menu Admin -->
                <div class="space-y-2">
                    <div class="px-3 sm:px-4 py-2">
                        <h3 class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wider">Administration</h3>
                    </div>
                    
                    <a href="{{ route('emplois.index') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm sm:text-base">Emplois du temps</span>
                    </a>
                    
                    <a href="{{ route('admin.exports.index') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        <span class="text-sm sm:text-base">Exports</span>
                    </a>
                </div>
            @endif

            @if($user->isEnseignant())
                <!-- Menu Enseignant -->
                <div class="space-y-2">
                    <div class="px-3 sm:px-4 py-2">
                        <h3 class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wider">Enseignement</h3>
                    </div>
                    
                    <a href="{{ route('enseignant.seances') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="text-sm sm:text-base">Mes séances</span>
                    </a>
                    
                    <a href="{{ route('enseignant.presences') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm sm:text-base">Saisir présences</span>
                    </a>
                    
                    <a href="{{ route('enseignant.emploi-du-temps') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm sm:text-base">Emploi du temps</span>
                    </a>
                    
                    <a href="{{ route('enseignant.planning-semaine') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        <span class="text-sm sm:text-base">Planning semaine</span>
                    </a>
                    
                    <a href="{{ route('enseignant.statistiques') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-sm sm:text-base">Statistiques</span>
                    </a>
                </div>
            @endif

            @if($user->isCoordinateur())
                <!-- Menu Coordinateur -->
                <div class="space-y-2">
                    <div class="px-3 sm:px-4 py-2">
                        <h3 class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wider">Coordination</h3>
                    </div>
                    
                    <a href="{{ route('emplois.index') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm sm:text-base">Emploi du temps</span>
                    </a>
                    
                    <a href="{{ route('coordinateur.absences') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm sm:text-base">Absences</span>
                    </a>
                    
                    <a href="#" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-sm sm:text-base">Statistiques</span>
                    </a>
                </div>
            @endif

            @if($user->isEtudiant())
                <!-- Menu Étudiant -->
                <div class="space-y-2">
                    <div class="px-3 sm:px-4 py-2">
                        <h3 class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wider">Étudiant</h3>
                    </div>
                    
                    <a href="{{ route('emplois.public') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm sm:text-base">Emploi du temps</span>
                    </a>
                    
                    <a href="{{ route('etudiant.absences') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <span class="text-sm sm:text-base">Mes absences</span>
                    </a>
                    
                    <a href="{{ route('etudiant.statistiques') }}" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                        <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-sm sm:text-base">Statistiques</span>
                    </a>
                </div>
        @endif

            <!-- Paramètres -->
            <div class="pt-4 sm:pt-6 border-t border-gray-200">
                <div class="px-3 sm:px-4 py-2">
                    <h3 class="text-xs sm:text-sm font-semibold text-gray-500 uppercase tracking-wider">Paramètres</h3>
                </div>
                
                <a href="#" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                    <span class="text-sm sm:text-base">Profil</span>
                </a>
                
                <a href="#" class="flex items-center space-x-3 p-3 sm:p-4 text-gray-700 hover:bg-blue-50 hover:text-blue-600 rounded-lg transition-all duration-300 group">
                    <svg class="w-5 h-5 sm:w-6 sm:h-6 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    <span class="text-sm sm:text-base">Paramètres</span>
                </a>
            </div>
    </nav>
    </div>
</aside>

<!-- Bouton toggle sidebar pour mobile -->
<button id="sidebarToggle" class="fixed top-20 left-4 z-20 bg-white p-2 rounded-lg shadow-lg md:hidden">
    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
    </svg>
</button>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const mainContent = document.querySelector('main');
    
    // Masquer la sidebar par défaut sur mobile
    if (window.innerWidth < 768) {
        sidebar.classList.add('-translate-x-full');
    }
    
    sidebarToggle.addEventListener('click', function() {
        sidebar.classList.toggle('-translate-x-full');
    });
    
    // Fermer la sidebar en cliquant à l'extérieur sur mobile
    document.addEventListener('click', function(event) {
        if (window.innerWidth < 768) {
            if (!sidebar.contains(event.target) && !sidebarToggle.contains(event.target)) {
                sidebar.classList.add('-translate-x-full');
            }
        }
    });
    
    // Gérer le redimensionnement de la fenêtre
    window.addEventListener('resize', function() {
        if (window.innerWidth >= 768) {
            sidebar.classList.remove('-translate-x-full');
        } else {
            sidebar.classList.add('-translate-x-full');
        }
    });
});
</script>
