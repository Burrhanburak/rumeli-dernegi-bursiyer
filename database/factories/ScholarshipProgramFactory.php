<?php

namespace Database\Factories;

use App\Models\ScholarshipProgram;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ScholarshipProgram>
 */
class ScholarshipProgramFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ScholarshipProgram::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->unique()->words(3, true) . ' Scholarship',
            'description' => $this->faker->paragraph(),
            'default_amount' => $this->faker->randomFloat(2, 1000, 10000),
            'application_start_date' => $this->faker->dateTimeBetween('-1 month', 'now'),
            'application_end_date' => $this->faker->dateTimeBetween('now', '+3 months'),
            'program_start_date' => $this->faker->dateTimeBetween('+1 month', '+2 months'),
            'program_end_date' => $this->faker->dateTimeBetween('+1 year', '+4 years'),
            'is_active' => true,
            'max_recipients' => $this->faker->numberBetween(5, 50),
            'created_by' => User::factory()->create(['is_admin' => true])->id,
            'requirements' => $this->faker->paragraph(),
            'notes' => $this->faker->paragraph(),
            'created_at' => now(),
            'updated_at' => now(),
        ];
    }

    /**
     * Indicate that the program is inactive.
     */
    public function inactive()
    {
        return $this->state(function (array $attributes) {
            return [
                'is_active' => false,
            ];
        });
    }
}