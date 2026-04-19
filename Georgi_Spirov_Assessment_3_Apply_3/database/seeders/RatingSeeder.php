<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectRating;
use App\Models\User;
use Illuminate\Database\Seeder;

class RatingSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $projects = Project::all();

        foreach ($projects as $project) {
            $raters = $users->random(rand(2, min(6, $users->count())));

            foreach ($raters as $user) {
                ProjectRating::factory()->create([
                    'project_id' => $project->id,
                    'user_id' => $user->id,
                ]);
            }
        }
    }
}
