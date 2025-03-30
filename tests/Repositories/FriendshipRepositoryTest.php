<?php

namespace WalkerChiu\Friendship;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use WalkerChiu\Friendship\Models\Entities\Friendship;
use WalkerChiu\Friendship\Models\Repositories\FriendshipRepository;

class FriendshipRepositoryTest extends \Orchestra\Testbench\TestCase
{
    use RefreshDatabase;

    protected $repository;

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

        $this->repository = $this->app->make(FriendshipRepository::class);
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
     * A basic functional test on FriendshipRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\Repository
     *
     * @return void
     */
    public function testFriendshipRepository()
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
        $id_list = [];
        for ($i=1; $i<=3; $i++) {
            $record = $this->repository->save([
                'user_id_a' => $i,
                'user_id_b' => 4,
                'state'     => $faker->randomElement(config('wk-core.class.friendship.friendshipState')::getCodes()),
                'flag_a'    => $faker->boolean,
                'flag_b'    => $faker->boolean
            ]);
            array_push($id_list, $record->id);
        }

        // Get and Count records after creation
            // When
            $records = $this->repository->get();
            $count   = $this->repository->count();
            // Then
            $this->assertCount(3, $records);
            $this->assertEquals(3, $count);

        // Find someone
            // When
            $record = $this->repository->first();
            // Then
            $this->assertNotNull($record);

            // When
            $record = $this->repository->find($faker->uuid());
            // Then
            $this->assertNull($record);

        // Delete someone
            // When
            $this->repository->deleteByIds([$id_list[0]]);
            $count = $this->repository->count();
            // Then
            $this->assertEquals(2, $count);

            // When
            $this->repository->deleteByExceptIds([$id_list[2]]);
            $count = $this->repository->count();
            $record = $this->repository->find($id_list[2]);
            // Then
            $this->assertEquals(1, $count);
            $this->assertNotNull($record);

            // When
            $count = $this->repository->where('id', '>', 0)->count();
            // Then
            $this->assertEquals(1, $count);
    }

    /**
     * Unit test about Query List on FriendshipRepository.
     *
     * For WalkerChiu\Core\Models\Repositories\RepositoryTrait
     *     WalkerChiu\Friendship\Models\Repositories\FriendshipRepository
     *
     * @return void
     */
    public function testQueryList()
    {
    }
}
