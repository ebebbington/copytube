<?php

namespace Database\Factories;

use App\UserModel;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash as FacadesHash;

class UserModelFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = UserModel::class;

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
            "password" => UserModel::generateHash("Welcome1"),
            "logged_in" => 1,
            "login_attempts" => 3,
            "profile_picture" => null,
            "recover_token" => null,
        ];
    }
}
