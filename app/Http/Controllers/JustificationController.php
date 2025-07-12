<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

namespace App\Http\Controllers;

use App\Models\Presence;
use App\Models\Justification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class JustificationController extends Controller
{
    public function create(Presence $presence)
    {
        return view('coordinateur.justifications.create', compact('presence'));
    }

    public function store(Request $request, Presence $presence)
    {
        $validated = $request->validate([
            'raison' => 'required|string|max:500',
            'fichier_justificatif' => 'nullable|file|mimes:pdf,jpg,png|max:2048'
        ]);

        $justificationData = [
            'presence_id' => $presence->id,
            'coordinateur_id' => auth()->id(),
            'raison' => $validated['raison']
        ];

        if ($request->hasFile('fichier_justificatif')) {
            $justificationData['fichier_justificatif'] = $request->file('fichier_justificatif')
                ->store('justifications', 'public');
        }

        Justification::create($justificationData);
        $presence->update(['justifie' => true]);

        return redirect()->route('coordinateur.absences')
            ->with('success', 'Absence justifiée avec succès');
    }

    public function show(Justification $justification)
    {
        return view('coordinateur.justifications.show', compact('justification'));
    }
}
