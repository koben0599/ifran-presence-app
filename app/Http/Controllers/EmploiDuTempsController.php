<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Rules\CreneauDisponible;

namespace App\Http\Controllers;

use App\Models\EmploiDuTemps;
use App\Models\Module;
use App\Models\Enseignant;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class EmploiDuTempsController extends Controller
{
    public function index()
    {
        $emplois = EmploiDuTemps::with(['module', 'enseignant'])
            ->orderBy('jour_semaine')
            ->orderBy('heure_debut')
            ->get()
            ->groupBy('classe');

        return view('admin.emplois.index', compact('emplois'));
    }

    public function create()
    {
        $modules = Module::all();
        $enseignants = User::where('role', 'enseignant')->get();
        return view('admin.emplois.create', compact('modules', 'enseignants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classe' => 'required|string',
            'module_id' => 'required|exists:modules,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'jour_semaine' => 'required|integer|between:1,5',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type_cours' => 'required|in:presentiel,e-learning,workshop',
            'salle' => [
                'nullable',
                'string',
                new CreneauDisponible(
                    $request->salle,
                    $request->jour_semaine
                )
            ]
        ]);

        EmploiDuTemps::create($validated);

        return redirect()->route('admin.emplois.index')
            ->with('success', 'Cours ajouté à l\'emploi du temps');
    }

    public function edit(EmploiDuTemps $emploi)
    {
        $modules = Module::all();
        $enseignants = Enseignant::all();
        return view('admin.emplois.edit', compact('emploi', 'modules', 'enseignants'));
    }

    public function update(Request $request, EmploiDuTemps $emploi)
    {
        $validated = $request->validate([
            'module_id' => 'required|exists:modules,id',
            'enseignant_id' => 'required|exists:enseignants,id',
            'jour_semaine' => 'required|integer|between:1,5',
            'heure_debut' => 'required|date_format:H:i',
            'heure_fin' => 'required|date_format:H:i|after:heure_debut',
            'type_cours' => 'required|in:presentiel,e-learning,workshop',
            'salle' => [
                'nullable',
                'string',
                new CreneauDisponible(
                    $request->salle,
                    $request->jour_semaine,
                    $emploi->id
                )
            ],
            'est_actif' => 'boolean'
        ]);

        $emploi->update($validated);

        return redirect()->route('admin.emplois.index')
            ->with('success', 'Cours mis à jour');
    }

    public function annuler(EmploiDuTemps $emploi)
    {
        $emploi->update(['est_actif' => false]);

        return back()->with('success', 'Cours désactivé');
    }
}
