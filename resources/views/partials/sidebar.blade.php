<aside class="w-64 h-screen bg-white shadow-lg fixed top-16 left-0 flex flex-col z-10 hidden md:flex">
    <div class="p-6 border-b">
        <span class="text-lg font-bold text-blue-600">Menu</span>
        <p class="mt-2 text-gray-600">{{ Auth::user()->prenom ?? Auth::user()->name }}</p>
        <p class="text-xs text-gray-400 capitalize">{{ Auth::user()->role }}</p>
    </div>
    <nav class="flex-1 p-4 space-y-2">
        @php $user = Auth::user(); @endphp

        @if($user->isAdmin())
            <a href="{{ route('admin.emplois.index') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Emplois du temps</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-green-100">Statistiques & Graphiques</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-purple-100">Gestion des utilisateurs</a>
        @elseif($user->isEnseignant())
            <a href="{{ route('enseignant.dashboard') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Mes séances</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-green-100">Saisir les présences</a>
        @elseif($user->isCoordinateur())
            <a href="{{ route('coordinateur.absences') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Absences</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-green-100">Justifier une absence</a>
            <a href="#" class="block px-4 py-2 rounded hover:bg-purple-100">Emploi du temps</a>
        @elseif($user->isEtudiant())
            <a href="{{ route('etudiant.absences') }}" class="block px-4 py-2 rounded hover:bg-blue-100">Mes absences</a>
            <a href="{{ route('etudiant.statistiques') }}" class="block px-4 py-2 rounded hover:bg-green-100">Statistiques d’assiduité</a>
        @endif
    </nav>
</aside>
