<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\EmployerProfile;
use App\Enums\UserRole;
use App\Models\JobListing;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::factory()->create([
            'name' => 'Seif',
            'email' => 'seif@dev.com',
            'role' => UserRole::ADMIN,
        ]);

        $categories = ['Software Development', 'Marketing', 'Design', 'Sales'];
        foreach ($categories as $cat) {
            Category::create([
                'name' => $cat,
            ]);
        }

        User::factory(5)->create(['role' => UserRole::EMPLOYER])
            ->each(function ($user) {
                $user->employerProfile()->create([
                    'company_name' => fake()->company(),
                    'company_bio' => fake()->paragraph(),
                    'website' => fake()->url(),
                ]);
            });

        User::factory(10)->create(['role' => UserRole::CANDIDATE])
            ->each(function ($user) {
                $user->candidateProfile()->create([
                    'years_experience' => fake()->numberBetween(1, 10),
                    'skills' => ['PHP', 'Laravel', 'JavaScript'],
                ]);
            });

         JobListing::factory(20)->create();

    }
}
