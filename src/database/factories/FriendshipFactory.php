<?php

/** @var \Illuminate\Database\Eloquent\Factory  $factory */

use Faker\Generator as Faker;
use WalkerChiu\Friendship\Models\Entities\Friendship;

$factory->define(Friendship::class, function (Faker $faker) {
    return [
        'user_id_a' => $faker->numberBetween($min = 1, $max = 2),
        'user_id_b' => $faker->numberBetween($min = 3, $max = 4),
        'state'     => $faker->randomElement(config('wk-core.class.friendship.friendshipState')::getCodes()),
        'flag_a'    => $faker->boolean,
        'flag_b'    => $faker->boolean
    ];
});
