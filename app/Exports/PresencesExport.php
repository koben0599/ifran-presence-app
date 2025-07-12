<?php

namespace App\Exports;

use App\Models\Presence;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PresencesExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $classe;
    protected $moduleId;
    protected $dateDebut;
    protected $dateFin;

    public function __construct($classe, $moduleId, $dateDebut, $dateFin)
    {
        $this->classe = $classe;
        $this->moduleId = $moduleId;
        $this->dateDebut = $dateDebut;
        $this->dateFin = $dateFin;
    }

    public function collection()
    {
        $query = Presence::with(['etudiant', 'seance.module'])
            ->whereHas('etudiant', fn($q) => $q->where('classe', $this->classe))
            ->whereHas('seance', fn($q) => $q->whereBetween('date_debut', [$this->dateDebut, $this->dateFin]));
            
        if ($this->moduleId) {
            $query->whereHas('seance', fn($q) => $q->where('module_id', $this->moduleId));
        }
        
        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Étudiant',
            'Classe',
            'Module',
            'Date',
            'Heure',
            'Type',
            'Statut',
            'Justifiée'
        ];
    }

    public function map($presence): array
    {
        return [
            $presence->etudiant->prenom . ' ' . $presence->etudiant->nom,
            $presence->etudiant->classe,
            $presence->seance->module->nom,
            $presence->seance->date_debut->format('d/m/Y'),
            $presence->seance->date_debut->format('H:i') . ' - ' . $presence->seance->date_fin->format('H:i'),
            ucfirst($presence->seance->type_cours),
            ucfirst($presence->statut),
            $presence->justifie ? 'Oui' : 'Non'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
            'A:H' => ['alignment' => ['wrapText' => true]]
        ];
    }
}