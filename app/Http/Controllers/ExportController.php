<?php

namespace App\Http\Controllers;

use App\Models\Classe;
use App\Models\Module;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\PresencesExport;

class ExportController extends Controller
{
    public function index()
    {
        $classes = Classe::distinct()->pluck('nom');
        $modules = Module::all();
        
        return view('admin.exports.index', compact('classes', 'modules'));
    }

    public function export(Request $request)
    {
        $validated = $request->validate([
            'classe' => 'required|string',
            'module_id' => 'nullable|exists:modules,id',
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
            'format' => 'required|in:excel,csv,pdf'
        ]);

        return Excel::download(
            new PresencesExport(
                $validated['classe'],
                $validated['module_id'],
                $validated['date_debut'],
                $validated['date_fin']
            ),
            'presences-' . now()->format('Y-m-d') . '.' . $validated['format'],
            \Maatwebsite\Excel\Excel::XLSX
        );
    }
}