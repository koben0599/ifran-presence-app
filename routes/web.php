<?php



use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\CoordinateurController;
use App\Http\Controllers\EnseignantController;
use App\Http\Controllers\EtudiantController;
use App\Http\Controllers\EmploiDuTempsController;
use App\Http\Controllers\ExportController;
use App\Http\Controllers\JustificationController;
use App\Http\Controllers\StatistiqueController;
use App\Http\Controllers\AlerteController;

// Authentification Laravel
Auth::routes(['register' => false]);

// Route d'inscription personnalisée
Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Redirection après login selon le rôle
Route::get('/home', function() {
    $user = auth()->user();
    if ($user->isAdmin()) {
        return redirect()->route('admin.emplois.index');
    } elseif ($user->isEnseignant()) {
        return redirect()->route('enseignant.dashboard');
    } elseif ($user->isCoordinateur()) {
        return redirect()->route('coordinateur.absences');
    } elseif ($user->isEtudiant()) {
        return redirect()->route('etudiant.absences');
    }
    return redirect('/');
})->name('home');

// Route racine avec redirection intelligente
Route::get('/', function () {
    if (auth()->check()) {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.emplois.index');
        } elseif ($user->isEnseignant()) {
            return redirect()->route('enseignant.dashboard');
        } elseif ($user->isCoordinateur()) {
            return redirect()->route('coordinateur.absences');
        } elseif ($user->isEtudiant()) {
            return redirect()->route('etudiant.absences');
        }
    }
    return redirect('/login');
});

// Routes Admin
Route::prefix('admin')->as('admin.')->middleware(['auth', 'admin'])->group(function () {
    Route::resource('emplois', EmploiDuTempsController::class)->except(['show']);
});

// Routes Coordinateur
Route::prefix('coordinateur')->middleware(['auth', 'coordinateur'])->group(function () {
    Route::get('absences', [JustificationController::class, 'index'])->name('coordinateur.absences');
    Route::get('absences/{presence}/justifier', [JustificationController::class, 'create'])
         ->name('coordinateur.justifications.create');
    Route::post('absences/{presence}/justifier', [JustificationController::class, 'store'])
         ->name('coordinateur.justifications.store');
    Route::get('justifications/{justification}', [JustificationController::class, 'show'])
         ->name('coordinateur.justifications.show');
    
    Route::get('alertes', [AlerteController::class, 'index'])->name('coordinateur.alertes');
});

// Routes Enseignant
Route::prefix('enseignant')->middleware(['auth', 'enseignant'])->group(function () {
    Route::get('/', [EnseignantController::class, 'dashboard'])->name('enseignant.dashboard');
    Route::get('seances/{seance}/presences', [EnseignantController::class, 'saisirPresence'])
         ->name('presences.saisir');
    Route::post('seances/{seance}/presences', [EnseignantController::class, 'enregistrerPresence'])
         ->name('presences.enregistrer');
});

// Routes Étudiant
Route::middleware(['auth', 'etudiant'])->group(function () {
    Route::get('/absences', [EtudiantController::class, 'mesAbsences'])->name('etudiant.absences');
    Route::get('/statistiques', [EtudiantController::class, 'statistiques'])->name('etudiant.statistiques');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');