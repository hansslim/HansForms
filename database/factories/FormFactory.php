<?php

namespace Database\Factories;

use App\Models\Form;
use DateTime;
use Illuminate\Database\Eloquent\Factories\Factory;

class FormFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Form::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'slug' => $this->faker->unique()->uuid(),
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->paragraph(),
            'has_private_token' => false,
            'user_id' => 1,
            'start_time' => new DateTime('2020-01-01 00:00:00'),
            'end_time' => new DateTime('3000-01-01 00:00:00')
        ];
    }
}
