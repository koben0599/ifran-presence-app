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
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TestController;

// Authentification Laravel
Auth::routes(['register' => false]);

// Route d'inscription personnalisée
Route::get('register', [App\Http\Controllers\Auth\RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('register', [App\Http\Controllers\Auth\RegisterController::class, 'register']);

// Redirection après login selon le rôle
Route::get('/home', function() {
    $user = auth()->user();
    if ($user->isAdmin()) {
        return redirect()->route('emplois.index');
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
            return redirect()->route('emplois.index');
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
    Route::get('exports', [ExportController::class, 'index'])->name('exports.index');
});

// Routes Coordinateur avec fonctionnalités avancées
Route::prefix('coordinateur')->as('coordinateur.')->middleware(['auth', 'coordinateur'])->group(function () {
    // Gestion des emplois du temps
    Route::resource('emplois', CoordinateurController::class);
    Route::post('emplois/{emploi}/annuler', [CoordinateurController::class, 'annuler'])->name('emplois.annuler');
    Route::post('emplois/{emploi}/reporter', [CoordinateurController::class, 'reporter'])->name('emplois.reporter');
    
    // Gestion des absences
    Route::get('absences', [CoordinateurController::class, 'absences'])->name('absences');
    Route::post('absences/{presence}/justifier', [CoordinateurController::class, 'justifierAbsence'])->name('absences.justifier');
    
    // Saisie des présences
    Route::get('presences/learning', [CoordinateurController::class, 'saisirPresencesLearning'])->name('presences.learning');
    Route::get('presences/workshop', [CoordinateurController::class, 'saisirPresencesWorkshop'])->name('presences.workshop');
    Route::post('presences/enregistrer', [CoordinateurController::class, 'enregistrerPresences'])->name('presences.enregistrer');
    
    // Statistiques
    Route::get('statistiques', [CoordinateurController::class, 'statistiques'])->name('statistiques');
    
    // Justifications
    Route::get('justifications/{justification}', [JustificationController::class, 'show'])->name('justifications.show');
    
    // Alertes
    Route::get('alertes', [AlerteController::class, 'index'])->name('alertes');
});

// Routes Enseignant
Route::prefix('enseignant')->middleware(['auth', 'enseignant'])->group(function () {
    Route::get('/', [EnseignantController::class, 'dashboard'])->name('enseignant.dashboard');
    Route::get('presences', [EnseignantController::class, 'presences'])->name('enseignant.presences');
    Route::get('seances', [EnseignantController::class, 'seances'])->name('enseignant.seances');
    Route::get('statistiques', [EnseignantController::class, 'statistiques'])->name('enseignant.statistiques');
    Route::get('emploi-du-temps', [EmploiDuTempsController::class, 'emploiDuTempsEnseignant'])->name('enseignant.emploi-du-temps');
    Route::get('planning-semaine', [EmploiDuTempsController::class, 'planningSemaine'])->name('enseignant.planning-semaine');
    Route::post('generer-seances', [EmploiDuTempsController::class, 'genererSeances'])->name('enseignant.generer-seances');
    Route::get('seances/{seance}/presences', [EnseignantController::class, 'saisirPresence'])
         ->name('presences.saisir');
    Route::post('seances/{seance}/presences', [EnseignantController::class, 'enregistrerPresence'])
         ->name('presences.enregistrer');
});

// Routes Étudiant
Route::prefix('etudiant')->as('etudiant.')->middleware(['auth', 'etudiant'])->group(function () {
    Route::get('absences', [EtudiantController::class, 'mesAbsences'])->name('absences');
    Route::get('statistiques', [StatistiqueController::class, 'index'])->name('statistiques');
    Route::get('statistiques-avancees', [StatistiqueController::class, 'statistiquesAvancees'])->name('statistiques-avancees');
});

// Routes pour les emplois du temps (Coordinateurs et Admins)
Route::prefix('emplois')->middleware(['auth', 'coordinateur'])->group(function () {
    Route::get('/', [EmploiDuTempsController::class, 'index'])->name('emplois.index');
    Route::get('/create', [EmploiDuTempsController::class, 'create'])->name('emplois.create');
    Route::post('/', [EmploiDuTempsController::class, 'store'])->name('emplois.store');
    Route::get('/{emploi}/edit', [EmploiDuTempsController::class, 'edit'])->name('emplois.edit');
    Route::put('/{emploi}', [EmploiDuTempsController::class, 'update'])->name('emplois.update');
    Route::delete('/{emploi}', [EmploiDuTempsController::class, 'destroy'])->name('emplois.destroy');
    Route::post('/generer-seances', [EmploiDuTempsController::class, 'genererSeancesSemaine'])->name('emplois.generer-seances');
    Route::post('/{emploi}/annuler', [EmploiDuTempsController::class, 'annuler'])->name('emplois.annuler');
    Route::post('/{emploi}/reporter', [EmploiDuTempsController::class, 'reporter'])->name('emplois.reporter');
});

// Route publique pour l'emploi du temps (étudiants)
Route::get('/emploi-du-temps/{classe?}', [EmploiDuTempsController::class, 'emploiDuTempsPublic'])->name('emplois.public');

// Saisie des présences (enseignant et coordinateur)
Route::middleware(['auth'])->group(function () {
    Route::get('seances', [\App\Http\Controllers\PresenceController::class, 'index'])->name('seances.index');
    Route::get('seances/{seance}/saisie', [\App\Http\Controllers\PresenceController::class, 'saisie'])->name('seances.saisie');
    Route::post('seances/{seance}/saisie', [\App\Http\Controllers\PresenceController::class, 'store'])->name('seances.saisie.store');
});

// Routes Statistiques
Route::get('/statistiques', [StatistiqueController::class, 'index'])->name('statistiques.index')->middleware('auth');
Route::get('/statistiques/etudiant/{etudiant}', [StatistiqueController::class, 'show'])
     ->name('statistiques.etudiant')
     ->middleware('auth');

Route::get('/statistiques/classe/{classe}', [StatistiqueController::class, 'parClasse'])
    ->name('statistiques.classe');

// Routes Justifications
Route::get('/justifications', [JustificationController::class, 'index'])->name('justifications.index');

// Routes Notifications
Route::middleware(['auth'])->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/mark-read', [NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/notifications/mark-all-read', [NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::delete('/notifications/{notification}', [NotificationController::class, 'destroy'])->name('notifications.destroy');
    Route::get('/notifications/unread-count', [NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
});

// Routes de Test (uniquement en développement)
if (app()->environment('local')) {
    Route::prefix('test')->middleware(['auth'])->group(function () {
        Route::get('/notifications', [TestController::class, 'testPage'])->name('test.notifications');
        Route::post('/create-notification', [TestController::class, 'createTestNotification'])->name('test.create-notification');
        Route::post('/create-multiple-notifications', [TestController::class, 'createMultipleNotifications'])->name('test.create-multiple-notifications');
    });
}