<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ParentController extends Controller
{
    public function enfants()
    {
        $enfants = auth()->user()->enfants()->with([
            'presencesEtudiant.seance.module',
            'classe'
        ])->get();
        return view('parent.enfants', compact('enfants'));
    }
} 