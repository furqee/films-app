<?php

namespace Database\Factories;

use App\Models\Film;
use Illuminate\Database\Eloquent\Factories\Factory;

class FilmFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Film::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition(): array
    {
        return [
            'country_id' => $this->faker->numberBetween(1, 5),
            'name' => $this->faker->company,
            'slug' => $this->faker->slug,
            'description' => $this->faker->realText(),
            'release_date' => $this->faker->dateTime(),
            'rating' => $this->faker->numberBetween(1, 5),
            'price' => $this->faker->randomNumber(2),
            'photo' => 'placeholder.png',
        ];
    }
}
