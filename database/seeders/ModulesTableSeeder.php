<?php

namespace Database\Seeders;

use App\Models\Module;
use Illuminate\Database\Seeder;

class ModulesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $modules = [
            ['nom' => 'Développement Web', 'code' => 'DEVWEB', 'description' => 'HTML, CSS, JavaScript, PHP'],
            ['nom' => 'Développement Mobile', 'code' => 'DEVMOB', 'description' => 'React Native, Flutter'],
            ['nom' => 'Base de données', 'code' => 'BDD', 'description' => 'MySQL, PostgreSQL, MongoDB'],
            ['nom' => 'Design UI/UX', 'code' => 'DESIGN', 'description' => 'Figma, Adobe XD, Prototypage'],
            ['nom' => 'Marketing Digital', 'code' => 'MARKET', 'description' => 'SEO, SEM, Réseaux sociaux'],
            ['nom' => 'Gestion de projet', 'code' => 'GESTPROJ', 'description' => 'Agile, Scrum, Méthodologies'],
            ['nom' => 'Infographie', 'code' => 'INFOGRAPH', 'description' => 'Photoshop, Illustrator, InDesign'],
            ['nom' => 'Vidéo et Animation', 'code' => 'VIDEO', 'description' => 'After Effects, Premiere Pro'],
            ['nom' => 'Programmation avancée', 'code' => 'PROGAV', 'description' => 'Java, Python, C++'],
            ['nom' => 'Architecture logicielle', 'code' => 'ARCHLOG', 'description' => 'Patterns, Clean Code'],
        ];

        foreach ($modules as $module) {
            Module::create($module);
        }

        $this->command->info('Modules créés avec succès !');
    }
}
