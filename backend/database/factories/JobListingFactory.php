<?php

namespace Database\Factories;

use App\Models\JobListing;
use App\Models\EmployerProfile;
use App\Models\Category;
use App\Enums\WorkType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobListing>
 */
class JobListingFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'employer_id' => EmployerProfile::inRandomOrder()->first()->id,
            'category_id' => Category::inRandomOrder()->first()->id,

            'title' => fake()->jobTitle(),
            'description' => fake()->paragraphs(3, true),
            'experience' => fake()->randomElement(['Junior', 'Mid-level', 'Senior', '2-3 Years']),
            'salary_min' => fake()->numberBetween(5000, 15000),
            'salary_max' => fake()->numberBetween(16000, 40000),
            'work_type' => fake()->randomElement(WorkType::cases()),
            'status' => 'published',
            'deadline' => fake()->dateTimeBetween('+1 month', '+3 months'),
        ];
    }
}
