<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectImage;
use Illuminate\Database\Seeder;

class ProjectImageSeeder extends Seeder
{
    public function run(): void
    {
        $projects = Project::all();

        foreach ($projects as $project) {
            ProjectImage::factory()
                        ->count(rand(1, 3))
                        ->create([
                            'project_id' => $project->id
                        ]);
        }
    }
}
