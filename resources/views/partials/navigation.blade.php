<nav class="bg-white shadow fixed w-full z-20 top-0 left-0">
    <div class="container mx-auto px-4 py-3 flex justify-between items-center">
        <a href="/" class="text-xl font-bold text-blue-600">IFRAN</a>
        <div class="flex items-center space-x-6">
            @php $user = Auth::user(); @endphp
            @if($user->isAdmin())
                <a href="{{ route('admin.emplois.index') }}" class="text-gray-700 hover:text-blue-600">Emplois du temps</a>
                <a href="#" class="text-gray-700 hover:text-blue-600">Statistiques</a>
                <a href="#" class="text-gray-700 hover:text-blue-600">Utilisateurs</a>
            @elseif($user->isEnseignant())
                <a href="{{ route('enseignant.dashboard') }}" class="text-gray-700 hover:text-blue-600">Mes séances</a>
                <a href="#" class="text-gray-700 hover:text-blue-600">Présences</a>
            @elseif($user->isCoordinateur())
                <a href="{{ route('coordinateur.absences') }}" class="text-gray-700 hover:text-blue-600">Absences</a>
                <a href="#" class="text-gray-700 hover:text-blue-600">Justifications</a>
                <a href="#" class="text-gray-700 hover:text-blue-600">Emploi du temps</a>
            @elseif($user->isEtudiant())
                <a href="{{ route('etudiant.absences') }}" class="text-gray-700 hover:text-blue-600">Mes absences</a>
                <a href="{{ route('etudiant.statistiques') }}" class="text-gray-700 hover:text-blue-600">Statistiques</a>
            @endif
            <span class="text-gray-700 font-semibold">{{ $user->prenom ?? $user->name }}</span>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                    Déconnexion
                </button>
            </form>
        </div>
    </div>
</nav>
