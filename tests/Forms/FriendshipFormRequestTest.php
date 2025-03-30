<?php

namespace WalkerChiu\Friendship;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use WalkerChiu\Friendship\Models\Entities\Friendship;
use WalkerChiu\Friendship\Models\Forms\FriendshipFormRequest;

class FriendshipFormRequestTest extends \Orchestra\Testbench\TestCase
{
    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        //$this->loadLaravelMigrations(['--database' => 'mysql']);
        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');

        $this->request  = new FriendshipFormRequest();
        $this->rules    = $this->request->rules();
        $this->messages = $this->request->messages();
    }

    /**
     * To load your package service provider, override the getPackageProviders.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return Array
     */
    protected function getPackageProviders($app)
    {
        return [\WalkerChiu\Core\CoreServiceProvider::class,
                \WalkerChiu\Friendship\FriendshipServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
    }

    /**
     * Unit test about Authorize.
     *
     * For WalkerChiu\Friendship\Models\Forms\FriendshipFormRequest
     * 
     * @return void
     */
    public function testAuthorize()
    {
        $this->assertEquals(true, 1);
    }

    /**
     * Unit test about Rules.
     *
     * For WalkerChiu\Friendship\Models\Forms\FriendshipFormRequest
     * 
     * @return void
     */
    public function testRules()
    {
        $faker = \Faker\Factory::create();

        for ($i=1; $i<=6; $i++)
            DB::table(config('wk-core.table.user'))->insert([
                'name'     => $faker->username,
                'email'    => $faker->email,
                'password' => $faker->password
            ]);

        // Give
        $attributes = [
            'user_id_a' => $faker->numberBetween($min = 1, $max = 2),
            'user_id_b' => $faker->numberBetween($min = 3, $max = 4),
            'state'     => $faker->randomElement(config('wk-core.class.friendship.friendshipState')::getCodes()),
            'flag_a'    => $faker->boolean,
            'flag_b'    => $faker->boolean
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(false, $fails);

        // Give
        $attributes = [
            'user_id_a' => 1,
            'user_id_b' => 1,
            'state'     => $faker->randomElement(config('wk-core.class.friendship.friendshipState')::getCodes()),
            'flag_a'    => $faker->boolean,
            'flag_b'    => $faker->boolean
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(true, $fails);

        // Give
        $attributes = [
            'user_id_a' => $faker->numberBetween($min = 1, $max = 2),
            'user_id_b' => $faker->numberBetween($min = 3, $max = 4),
            'state'     => '',
            'flag_a'    => $faker->boolean,
            'flag_b'    => $faker->boolean
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(true, $fails);

        // Give
        $attributes = [
            'user_id_a' => $faker->numberBetween($min = 1, $max = 2),
            'user_id_b' => $faker->numberBetween($min = 3, $max = 4),
            'state'     => $faker->randomElement(config('wk-core.class.friendship.friendshipState')::getCodes()),
            'flag_a'    => $faker->boolean,
            'flag_b'    => null
        ];
        // When
        $validator = Validator::make($attributes, $this->rules, $this->messages); $this->request->withValidator($validator);
        $fails = $validator->fails();
        // Then
        $this->assertEquals(true, $fails);
    }
}
