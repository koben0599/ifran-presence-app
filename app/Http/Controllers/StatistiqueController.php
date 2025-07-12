<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class StatistiqueController extends Controller
{
    public function index()
    {
        // Logique pour afficher les statistiques
        return view('admin.statistiques.index');
    }
}
