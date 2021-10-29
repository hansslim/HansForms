<?php

namespace Database\Factories;

use App\Models\SelectInputChoice;
use Illuminate\Database\Eloquent\Factories\Factory;

class SelectInputChoiceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = SelectInputChoice::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'text' => $this->faker->sentence(),
            'description' =>  $this->faker->sentence(5),
            'hidden_label' => -1,
            'order' => -1,
            'select_input_id' => 1
        ];
    }
}
