<?php

namespace Database\Factories;

use App\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = User::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "username" => $this->faker->name(),
            "email_address" => $this->faker->unique()->safeEmail(),
            "password" => User::generateHash("Welcome1"),
            "logged_in" => 1,
            "login_attempts" => 3,
            "profile_picture" => null,
            "recover_token" => null,
        ];
    }
}
