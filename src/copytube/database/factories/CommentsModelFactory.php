<?php

namespace Database\Factories;

use App\CommentsModel;
use App\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\VideosModel;

class CommentsModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = CommentsModel::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "comment" => $this->faker->name(),
            "author" => $this->faker->name(),
            "user_id" => UserModel::factory()->create()->id,
            "video_id" => VideosModel::factory()->create()->id,
            "date_posted" => $this->faker->date(),
        ];
    }
}
