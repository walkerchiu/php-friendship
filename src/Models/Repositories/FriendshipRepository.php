<?php

namespace WalkerChiu\Friendship\Models\Repositories;

use Illuminate\Support\Facades\App;
use WalkerChiu\Core\Models\Forms\FormTrait;
use WalkerChiu\Core\Models\Repositories\Repository;
use WalkerChiu\Core\Models\Repositories\RepositoryTrait;
use WalkerChiu\Core\Models\Services\PackagingFactory;

class FriendshipRepository extends Repository
{
    use FormTrait;
    use RepositoryTrait;

    protected $instance;



    /**
     * Create a new repository instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->instance = App::make(config('wk-core.class.friendship.friendship'));
    }

    /**
     * @param Array  $data
     * @param Bool   $auto_packing
     * @return Array|Collection|Eloquent
     */
    public function list(array $data, $auto_packing = false)
    {
        $instance = $this->instance;

        $data = array_map('trim', $data);
        $repository = $instance->when($data, function ($query, $data) {
                                    return $query->unless(empty($data['id']), function ($query) use ($data) {
                                                return $query->where('id', $data['id']);
                                            })
                                            ->unless(empty($data['user_id_a']), function ($query) use ($data) {
                                                return $query->where('user_id_a', $data['user_id_a']);
                                            })
                                            ->unless(empty($data['user_id_b']), function ($query) use ($data) {
                                                return $query->where('user_id_b', $data['user_id_b']);
                                            })
                                            ->unless(empty($data['state']), function ($query) use ($data) {
                                                return $query->where('state', $data['state']);
                                            })
                                            ->when(isset($data['flag_a']), function ($query) use ($data) {
                                                return $query->where('flag_a', $data['flag_a']);
                                            })
                                            ->when(isset($data['flag_b']), function ($query) use ($data) {
                                                return $query->where('flag_b', $data['flag_b']);
                                            });
                                })
                                ->orderBy('updated_at', 'DESC');

        if ($auto_packing) {
            $factory = new PackagingFactory(config('wk-friendship.output_format'), config('wk-friendship.pagination.pageName'), config('wk-friendship.pagination.perPage'));
            return $factory->output($repository);
        }

        return $repository;
    }

    /**
     * @param Friendship    $instance
     * @param Array|String  $code
     * @return Array
     */
    public function show($instance, $code): array
    {
    }
}
