<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Task;
use App\Models\User;

class TasksSeeder extends Seeder
{
    public function run()
    {
        $user = User::where('email', 'user@example.com')->first();
        $admin = User::where('email', 'admin@example.com')->first();

        Task::firstOrCreate([
            'user_id' => $user->id,
            'title' => 'Tarea usuario 1',
            'completed' => false
        ]);

        Task::firstOrCreate([
            'user_id' => $admin->id,
            'title' => 'Tarea admin 1',
            'completed' => true
        ]);
    }
}
