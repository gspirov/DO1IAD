<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use App\Models\UserProjectFavourite;
use Illuminate\Database\Seeder;

class UserProjectFavouriteSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $projects = Project::all();

        $totalProjectsCount = $projects->count();

        foreach ($users as $user) {
            $projects->random(rand(1, $totalProjectsCount))->each(function ($project) use ($user) {
                UserProjectFavourite::factory()->create([
                    'user_id' => $user->id,
                    'project_id' => $project->id,
                ]);
            });
        }
    }
}
