<?php

namespace WalkerChiu\Friendship\Models\Entities;

trait UserTrait
{
    /**
     * @param String  $type
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function friendships($type = null)
    {
        if ($type == 'A')
            return $this->hasMany(config('wk-core.class.friendship.friendship'), 'user_id_a', 'id');
        if ($type == 'B')
            return $this->hasMany(config('wk-core.class.friendship.friendship'), 'user_id_b', 'id');

        return $this->friendships('A')
                    ->union( $this->friendships('B') );
    }

    /**
     * Get friendship.
     *
     * @param User  $user
     * @param Bool  $flag
     * @return Collection
     */
    public function getFriendship($user, $flag = null)
    {
        $a = $this->friendships->where('user_id_a', $this->id)
                               ->where('user_id_b', $user->id)
                               ->unless(is_null($flag), function ($query) use ($flag) {
                                        return $query->where('flag_a', $flag);
                                    });
        $b = $this->friendships->where('user_id_a', $user->id)
                               ->where('user_id_b', $this->id)
                               ->unless(is_null($flag), function ($query) use ($flag) {
                                        return $query->where('flag_b', $flag);
                                    });
        return $a->merge($b);
    }

    /**
     * Check if A and B have a friendship.
     *
     * @param User|Array  $entity
     * @param Mixed       $state
     * @param Bool        $flag
     * @return Bool
     */
    public function hasFriendship($entity, $state = null, $flag = null): bool
    {
        if (is_array($entity)) {
            $flag = false;
            foreach ($entity as $value) {
                if ( !$this->hasFriendship($value, $state) )
                    return false;
                else
                    $flag = true;
            }
            return $flag;
        } else {
            return !$this->getFriendship($entity, $flag)
                         ->unless(is_null($state), function ($query) use ($state) {
                                return $query->where('state', $state);
                            })
                         ->isEmpty();
        }
    }

    /**
     * Check if A is pending B.
     *
     * @param User  $user
     * @return Bool
     */
    public function isPendingFriend($user): bool
    {
        return !$this->friendships->where('user_id_a', $this->id)
                                  ->where('user_id_b', $user->id)
                                  ->where('state', 0)
                                  ->isEmpty();
    }

    /**
     * Check if A has accepted B's invitation.
     *
     * @param User  $user
     * @return Bool
     */
    public function hasAcceptedFriend($user): bool
    {
        return !$this->friendships->where('user_id_a', $user->id)
                                  ->where('user_id_b', $this->id)
                                  ->where('state', 1)
                                  ->isEmpty();
    }

    /**
     * Check if A has rejected B.
     *
     * @param User  $user
     * @return Bool
     */
    public function hasRejectedFriend($user): bool
    {
        return !$this->friendships->where('user_id_a', $user->id)
                                  ->where('user_id_b', $this->id)
                                  ->where('state', 2)
                                  ->isEmpty();
    }

    /**
     * Check if A has blocked B.
     *
     * @param User  $user
     * @return Bool
     */
    public function hasBlockedFriend($user): bool
    {
        return !$this->friendships->where('user_id_a', $this->id)
                                  ->where('user_id_b', $user->id)
                                  ->where('state', 3)
                                  ->isEmpty();
    }

    /**
     * Check if A has marked B.
     *
     * @param User  $user
     * @return Bool
     */
    public function hasMarkedFriend($user): ?bool
    {
        $record = $this->friendships->where('user_id_a', $this->id)
                                    ->where('user_id_b', $user->id)
                                    ->first();
        if ($record) {
            return ($record->flag_a) ? true : false;
        }

        $record = $this->friendships->where('user_id_a', $user->id)
                                    ->where('user_id_b', $this->id)
                                    ->first();
        if ($record) {
            return ($record->flag_b) ? true : false;
        }

        return null;
    }

    /**
     * A invite B.
     *
     * @param User  $user
     * @return void
     */
    public function pendingFriend($user)
    {
        return config('wk-core.class.friendship.friendship')::create([
            'user_id_a' => $this->id,
            'user_id_b' => $user->id,
            'state'     => 0
        ]);
    }

    /**
     * A accept B.
     *
     * @param User  $user
     * @return void
     */
    public function acceptFriend($user): bool
    {
        return (bool) config('wk-core.class.friendship.friendship')
                        ::where('user_id_a', $user->id)
                        ->where('user_id_b', $this->id)
                        ->update(['state' => 1]);
    }

    /**
     * A reject B.
     *
     * @param User  $user
     * @return void
     */
    public function rejectFriend($user): bool
    {
        return (bool) config('wk-core.class.friendship.friendship')
                        ::where('user_id_a', $user->id)
                        ->where('user_id_b', $this->id)
                        ->update(['state' => 2]);
    }

    /**
     * A block B.
     *
     * @param User  $user
     * @return void
     */
    public function blockFriend($user)
    {
        $this->deleteFriend($user);

        return config('wk-core.class.friendship.friendship')::create([
            'user_id_a' => $this->id,
            'user_id_b' => $user->id,
            'state'     => 3
        ]);
    }

    /**
     * A unblock B.
     *
     * @param User  $user
     * @return void
     */
    public function unBlockFriend($user): void
    {
        config('wk-core.class.friendship.friendship')
            ::where('user_id_a', $this->id)
            ->where('user_id_b', $user->id)
            ->where('state', 3)
            ->delete();
    }

    /**
     * A cancel invitation.
     *
     * @param User  $user
     * @return void
     */
    public function cancelPendingFriend($user): void
    {
        if (!$user->hasRejectedFriend($this))
            config('wk-core.class.friendship.friendship')
                ::where('user_id_a', $this->id)
                ->where('user_id_b', $user->id)
                ->where('state', 0)
                ->delete();
    }

    /**
     * Delete friendship.
     *
     * @param User  $user
     * @return void
     */
    public function deleteFriend($user): void
    {
        if (!$user->hasRejectedFriend($this))
            config('wk-core.class.friendship.friendship')::
                where( function ($query) use ($user) {
                    return $query->where( function ($query) use ($user) {
                                    return $query->where('user_id_a', $this->id)
                                                 ->where('user_id_b', $user->id);
                                })->orWhere( function ($query) use ($user) {
                                    return $query->where('user_id_a', $user->id)
                                                 ->where('user_id_b', $this->id);
                                });
                })
                ->delete();
    }

    /**
     * A mark B.
     *
     * @param User  $user
     * @return void
     */
    public function markFriend($user): void
    {
        config('wk-core.class.friendship.friendship')::where('user_id_a', $this->id)
                                                     ->where('user_id_b', $user->id)
                                                     ->update(['flag_a' => 1]);
        config('wk-core.class.friendship.friendship')::where('user_id_a', $user->id)
                                                     ->where('user_id_b', $this->id)
                                                     ->update(['flag_b' => 1]);
    }

    /**
     * A unmark B.
     *
     * @param User  $user
     * @return void
     */
    public function unMarkFriend($user): void
    {
        config('wk-core.class.friendship.friendship')::where('user_id_a', $this->id)
                                                     ->where('user_id_b', $user->id)
                                                     ->update(['flag_a' => 0]);
        config('wk-core.class.friendship.friendship')::where('user_id_a', $user->id)
                                                     ->where('user_id_b', $this->id)
                                                     ->update(['flag_b' => 0]);
    }
}
