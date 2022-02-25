<?php

namespace WalkerChiu\Friendship\Models\Constants;

/**
 * @license MIT
 * @package WalkerChiu\Friendship
 *
 *
 */

class FriendshipState
{
    /**
     * @return Array
     */
    public static function getCodes(): array
    {
        $items = [];
        $states = self::all();
        foreach ($states as $code => $state) {
            array_push($items, $code);
        }

        return $items;
    }

    /**
     * @return Array
     */
    public static function all(): array
    {
        return [
            '0' => 'pending',
            '1' => 'accepted',
            '2' => 'rejected',
            '3' => 'blocked'
        ];
    }

    /**
     * @param String  $state
     * @return Array
     */
    public static function getDirections(string $state): array
    {
        $items = [$state];

        switch ($state) {
            case '0':
                return array_merge($items, ['1', '2', '3']);
            case '1':
                return array_merge($items, ['2', '3']);
            case '2':
                return array_merge($items, ['1', '3']);
            case '3':
            default:
                return $items;
        }
    }

    /**
     * @param String  $state
     * @param Bool    $onlyKey
     * @return Array
     */
    public static function findOptions(string $state, $onlyKey = false): array
    {
        $items = self::getDirections($state);

        if ($onlyKey)
            return $items;

        $list = [];
        foreach ($items as $item) {
            $list = array_merge($list, [
                $item => trans('php-friendship::state.option.'.$item)
            ]);
        }

        return $list;
    }
}
