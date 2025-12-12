<?php

namespace Database\Factories;

use App\Models\City;
use App\Models\Governorate;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\House>
 */
class HouseFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [

            'user_id'=>User::inRandomOrder()->first()->id,
            'title'=>fake()->sentence,
            'descreption'=>fake()->sentence,
            'category'=>fake()->randomElement(['house','hotel','apartment']),
            'bedrooms'=>fake()->randomElement(['2','3','5','4']),
            'bathrooms'=>fake()->randomElement(['2','3','5','4']),
            'livingrooms'=>fake()->randomElement(['2','3','5','4']),
            'area'=>fake()->randomElement(['200','300','500','400']),
            'day_price'=>fake()->randomElement(['250','350','375','400']),
            'mainImage' => fake()->imageUrl(640, 480, 'apartment', true),
            'city_id'=>City::inRandomOrder()->first()->id,
             'governorate_id'=>Governorate::inRandomOrder()->first()->id,

        ];
    }
}
