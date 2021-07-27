<?php

namespace Database\Factories;

use App\VideosModel;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideosModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = VideosModel::class;

    public $timestamps = false;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "title" => $this->faker->name(),
            "description" => $this->faker->name(),
            "src" => "videos/_test.mp4",
            "poster" => "img/_test.jpg",
        ];
    }
}
