<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\ProjectComment;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $projects = Project::all();

        foreach ($projects as $project) {
            ProjectComment::factory()
                          ->count(rand(5, 20))
                          ->create([
                              'project_id' => $project->id,
                              'user_id' => $users->random()->id,
                          ]);
        }
    }
}
