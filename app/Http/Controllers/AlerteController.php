<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AlerteController extends Controller
{
    public function index()
    {
        // Logique pour afficher les alertes
        return view('coordinateur.alertes.index');
    }
}
