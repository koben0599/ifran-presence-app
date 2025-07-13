<?php

namespace App\Http\Controllers;

use App\Models\Presence;
use Illuminate\Http\Request;
use App\Models\Seance;
use App\Models\User;

class PresenceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        if ($user->isEnseignant()) {
            // L'enseignant voit ses séances à venir ou passées
            $seances = $user->seancesEnseignant()
                ->with(['module', 'classe'])
                ->orderBy('date_debut', 'desc')
                ->get();
        } elseif ($user->isCoordinateur()) {
            // Le coordinateur voit les séances e-learning/workshop à superviser
            $seances = Seance::whereIn('type', ['elearning', 'workshop'])
                ->with(['module', 'classe'])
                ->orderBy('date_debut', 'desc')
                ->get();
        } else {
            $seances = collect();
        }
        return view('enseignant.seances', compact('seances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, $seanceId)
    {
        $seance = Seance::findOrFail($seanceId);
        
        // Vérifier que l'utilisateur a le droit de saisir les présences
        $user = auth()->user();
        if (!$user->isEnseignant() && !$user->isCoordinateur()) {
            return back()->with('error', 'Accès non autorisé.');
        }
        
        if ($user->isEnseignant() && $seance->enseignant_id !== $user->id) {
            return back()->with('error', 'Vous ne pouvez saisir que vos propres séances.');
        }
        
        $now = now();
        $dateDebut = \Carbon\Carbon::parse($seance->date_debut);
        if ($now->diffInDays($dateDebut) > 14) {
            return back()->with('error', 'La saisie n\'est plus modifiable.');
        }
        
        foreach ($request->presences as $etudiant_id => $statut) {
            Presence::updateOrCreate(
                ['etudiant_id' => $etudiant_id, 'seance_id' => $seance->id],
                ['statut' => $statut]
            );
        }
        return back()->with('success', 'Présences enregistrées avec succès');
    }

    /**
     * Display the specified resource.
     */
    public function show(Presence $presence)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Presence $presence)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Presence $presence)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Presence $presence)
    {
        //
    }

    public function saisie($seanceId)
    {
        $seance = Seance::with(['module', 'classe'])->findOrFail($seanceId);
        
        // Vérifier que l'utilisateur a le droit de saisir les présences
        $user = auth()->user();
        if (!$user->isEnseignant() && !$user->isCoordinateur()) {
            return redirect()->route('home')->with('error', 'Accès non autorisé.');
        }
        
        if ($user->isEnseignant() && $seance->enseignant_id !== $user->id) {
            return redirect()->route('enseignant.dashboard')->with('error', 'Vous ne pouvez saisir que vos propres séances.');
        }
        
        $etudiants = User::where('role', 'etudiant')
            ->where('classe_id', $seance->classe_id)
            ->get();
            
        $now = now();
        $dateDebut = \Carbon\Carbon::parse($seance->date_debut);
        $modifiable = $now->diffInDays($dateDebut) <= 14;
        
        return view('enseignant.saisie-presences', compact('seance', 'etudiants', 'modifiable'));
    }
}
