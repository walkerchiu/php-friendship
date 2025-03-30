<?php

namespace WalkerChiu\Friendship;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use WalkerChiu\Friendship\Models\Entities\Friendship;
use WalkerChiu\Friendship\Tests\Entities\User;

require_once __DIR__.'/User.php';

class FriendshipTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    /**
     * Setup the test environment.
     *
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ .'/../migrations');
        $this->withFactories(__DIR__ .'/../../src/database/factories');
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
     * A basic functional test on Friendship.
     *
     * For WalkerChiu\Friendship\Models\Entities\Friendship
     * 
     * @return void
     */
    public function testFriendship()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-friendship.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-friendship.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-friendship.soft_delete', 1);

        $faker = \Faker\Factory::create();

        DB::table(config('wk-core.table.user'))->insert([
            [
                'id'       => 1,
                'name'     => $faker->username,
                'email'    => $faker->email,
                'password' => $faker->password
            ],[
                'id'       => 2,
                'name'     => $faker->username,
                'email'    => $faker->email,
                'password' => $faker->password
            ],[
                'id'       => 3,
                'name'     => $faker->username,
                'email'    => $faker->email,
                'password' => $faker->password
            ],[
                'id'       => 4,
                'name'     => $faker->username,
                'email'    => $faker->email,
                'password' => $faker->password
            ]
        ]);

        // Give
        for ($i=1; $i<=3; $i++)
            factory(Friendship::class)->create([
                'user_id_a' => $i,
                'user_id_b' => 4,
                'state'     => $faker->randomElement(config('wk-core.class.friendship.friendshipState')::getCodes()),
                'flag_a'    => $faker->boolean,
                'flag_b'    => $faker->boolean
            ]);

        // Get records after creation
            // When
            $records = Friendship::all();
            // Then
            $this->assertCount(3, $records);
    }

    /**
     * Unit test about FormTrait on User.
     *
     * For WalkerChiu\Friendship\Models\Entities\UserTrait
     *
     * @return void
     */
    public function testFormTrait()
    {
        // Config
        Config::set('wk-core.onoff.core-lang_core', 0);
        Config::set('wk-friendship.onoff.core-lang_core', 0);
        Config::set('wk-core.lang_log', 1);
        Config::set('wk-friendship.lang_log', 1);
        Config::set('wk-core.soft_delete', 1);
        Config::set('wk-friendship.soft_delete', 1);

        // Give
        $faker = \Faker\Factory::create();
        for ($i=1; $i<=3; $i++)
            DB::table(config('wk-core.table.user'))->insert([
                'name'     => $faker->username,
                'email'    => $faker->email,
                'password' => $faker->password
            ]);

        $userA = User::find(1);
        $userB = User::find(2);

        $this->assertEquals(false, $userA->hasFriendship($userB));
        $this->assertEquals(false, $userA->isPendingFriend($userB));
        $this->assertEquals(false, $userA->hasAcceptedFriend($userB));
        $this->assertEquals(false, $userA->hasRejectedFriend($userB));
        $this->assertEquals(false, $userA->hasBlockedFriend($userB));
        $this->assertEquals(false, $userA->hasMarkedFriend($userB));

            // When
            $friendship = $userA->pendingFriend($userB);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(true, $userA->hasFriendship($userB));
            $this->assertEquals(true, $userA->hasFriendship($userB, 0));
            $this->assertEquals(false, $userA->hasFriendship($userB, 1));
            $this->assertEquals(false, $userA->hasFriendship($userB, 0, 1));
            $this->assertEquals(true, $userA->isPendingFriend($userB));
            $this->assertEquals(false, $userA->hasAcceptedFriend($userB));
            $this->assertEquals(false, $userA->hasRejectedFriend($userB));
            $this->assertEquals(false, $userA->hasBlockedFriend($userB));
            $this->assertEquals(false, $userA->hasMarkedFriend($userB));

            // When
            $friendship = $userA->acceptFriend($userB);
            // Then
            $this->assertEquals(false, $friendship);

            // When
            $friendship = $userB->acceptFriend($userA);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(true, $userA->hasFriendship($userB));
            $this->assertEquals(false, $userA->hasFriendship($userB, 0));
            $this->assertEquals(true, $userA->hasFriendship($userB, 1));
            $this->assertEquals(false, $userA->hasFriendship($userB, 1, 1));
            $this->assertEquals(false, $userA->isPendingFriend($userB));
            $this->assertEquals(false, $userA->hasAcceptedFriend($userB));
            $this->assertEquals(true, $userB->hasAcceptedFriend($userA));
            $this->assertEquals(false, $userA->hasRejectedFriend($userB));
            $this->assertEquals(false, $userA->hasBlockedFriend($userB));
            $this->assertEquals(false, $userA->hasMarkedFriend($userB));

            // When
            $friendship = $userB->blockFriend($userA);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(true, $userB->hasFriendship($userA));
            $this->assertEquals(true, $userB->hasFriendship($userA, 3));
            $this->assertEquals(true, $userB->hasBlockedFriend($userA));
            $this->assertEquals(false, $userA->hasBlockedFriend($userB));
            $friendship = $userA->pendingFriend($userB);
            $this->assertEquals(true, $userB->hasFriendship($userA, 3));
            $friendship = $userB->unBlockFriend($userA);
            $this->assertEquals(true, $userB->hasFriendship($userA, 3));

            // When
            $friendship = $userB->unBlockFriend($userA);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(false, $userB->hasFriendship($userA));

            // When
            $friendship = $userA->pendingFriend($userB);
            $friendship = $userB->rejectFriend($userA);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(true, $userA->hasFriendship($userB));
            $this->assertEquals(true, $userA->hasFriendship($userB, 2));
            $this->assertEquals(false, $userA->hasFriendship($userB, 2, 1));
            $this->assertEquals(false, $userA->isPendingFriend($userB));
            $this->assertEquals(false, $userB->hasAcceptedFriend($userA));
            $this->assertEquals(true, $userB->hasRejectedFriend($userA));

            // When
            $friendship = $userA->deleteFriend($userB);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(true, $userA->hasFriendship($userB));
            $this->assertEquals(true, $userA->hasFriendship($userB, 2));
            $this->assertEquals(true, $userB->hasRejectedFriend($userA));

            // When
            $friendship = $userB->deleteFriend($userA);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(false, $userA->hasFriendship($userB));

            // When
            $friendship = $userA->pendingFriend($userB);
            $friendship = $userB->cancelPendingFriend($userA);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(true, $userA->hasFriendship($userB));
            $this->assertEquals(true, $userA->hasFriendship($userB, 0));

            // When
            $friendship = $userA->cancelPendingFriend($userB);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(false, $userA->hasFriendship($userB));

            // When
            $friendship = $userA->pendingFriend($userB);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(true, $userA->hasFriendship($userB));
            $this->assertEquals(false, $userA->hasMarkedFriend($userB));

            // When
            $friendship = $userA->markFriend($userB);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(true, $userA->hasFriendship($userB, 0, 1));
            $this->assertEquals(true, $userA->hasMarkedFriend($userB));
            $this->assertEquals(false, $userB->hasMarkedFriend($userA));

            // When
            $friendship = $userA->unMarkFriend($userB);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(false, $userA->hasFriendship($userB, 0, 1));
            $this->assertEquals(false, $userA->hasMarkedFriend($userB));
            $this->assertEquals(false, $userB->hasMarkedFriend($userA));

            // When
            $friendship = $userA->markFriend($userB);
            $friendship = $userB->markFriend($userA);
            $friendship = $userA->unMarkFriend($userB);
            $userA = User::find(1);
            $userB = User::find(2);
            // Then
            $this->assertEquals(false, $userA->hasFriendship($userB, 0, 1));
            $this->assertEquals(false, $userA->hasMarkedFriend($userB));
            $this->assertEquals(true, $userB->hasFriendship($userA, 0, 1));
            $this->assertEquals(true, $userB->hasMarkedFriend($userA));
    }
}
