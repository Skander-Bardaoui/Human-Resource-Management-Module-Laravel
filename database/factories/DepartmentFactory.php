<?php

namespace Database\Factories;

use App\Models\Department;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Department>
 */
class DepartmentFactory extends Factory
{
    protected $model = Department::class;

    public function definition(): array
    {
        $name = $this->faker->unique()->company();

        return [
            'slug' => Str::slug($name).'-'.$this->faker->unique()->numberBetween(100, 99999),
            'name' => $name,
            'description' => $this->faker->sentence(),
            'manager_id' => null,
            'code' => 'CC-'.$this->faker->unique()->numerify('#####'),
            'monthly_budget' => $this->faker->randomFloat(3, 0, 50000),
        ];
    }
}
