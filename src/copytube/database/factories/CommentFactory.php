<?php

namespace Database\Factories;

use App\Comment;
use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use App\Video;

class CommentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Comment::class;

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
            "user_id" => User::factory()->create()->id,
            "video_id" => Video::factory()->create()->id,
            "date_posted" => $this->faker->date(),
        ];
    }
}
