<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();

        foreach ($users as $user) {
            Project::factory(rand(2, 4))
                   ->create([
                       'user_id' => $user->id
                   ]);
        }
    }
}
