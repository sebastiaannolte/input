<?php

namespace Database\Factories;

use App\Models\Bet;
use Illuminate\Database\Eloquent\Factories\Factory;

class BetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Bet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            [
                'match_id' => rand(1, 100000),
                'event' => $this->faker->name(),
                'selection' => $this->faker->name(),
                'bookie' => $this->faker->name(),
                'stake' => rand(1,2),
                'odds' =>   rand(1,2),
                'tipster' => 'Charlie',
                'sport' => 'FOotball',
                'date' => $this->faker->dateTimeBetween('-100 days', '+0 days'),
                'user_id' => 1,
                'status' => 'won',
            ]
        ];
    }
}
