<?php

namespace Database\Factories;

use App\Video;
use Illuminate\Database\Eloquent\Factories\Factory;

class VideoFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Video::class;

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
