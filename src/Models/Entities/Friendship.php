<?php

namespace WalkerChiu\Friendship\Models\Entities;

use WalkerChiu\Core\Models\Entities\UuidModel;

class Friendship extends UuidModel
{
    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'user_id_a', 'user_id_b',
        'state',
        'flag_a', 'flag_b'
    ];

    /**
     * The attributes that should be mutated to dates.
     *
     * @var Array
     */
    protected $dates = [
        'created_at',
        'updated_at'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var Array
     */
    protected $casts = [
        'flag_a' => 'boolean',
        'flag_b' => 'boolean'
    ];



    /**
     * Create a new instance.
     *
     * @param Array  $attributes
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->table = config('wk-core.table.friendship.friendships');

        parent::__construct($attributes);
    }

    /**
     * @param Array  $value
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user($type)
    {
        if ($type == 'a')
            return $this->belongsTo(config('wk-core.class.user'), 'user_id_a', 'id');
        elseif ($type == 'b')
            return $this->belongsTo(config('wk-core.class.user'), 'user_id_b', 'id');
    }

    /**
     * Get all of the categories for the friendship.
     *
     * @param String  $type
     * @param Bool    $is_enabled
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function categories($type = null, $is_enabled = null)
    {
        $table = config('wk-core.table.morph-category.categories_morphs');
        return $this->morphToMany(config('wk-core.class.morph-category.category'), 'morph', $table)
                    ->when(is_null($type), function ($query) {
                          return $query->whereNull('type');
                      }, function ($query) use ($type) {
                          return $query->where('type', $type);
                      })
                    ->unless( is_null($is_enabled), function ($query) use ($is_enabled) {
                        return $query->where('is_enabled', $is_enabled);
                      });
    }
}
