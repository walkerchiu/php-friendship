<?php

namespace WalkerChiu\Friendship\Tests\Entities;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use WalkerChiu\Friendship\Models\Entities\UserTrait;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;
    use UserTrait;

    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    /**
     * The attributes that are mass assignable.
     *
     * @var Array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var Array
     */
	/**
	 * The attributes that should be hidden for arrays.
	 *
	 * @var Array
	 */
    protected $hidden = [
        'password', 'remember_token'
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
    /**
     * The attributes that should be cast to native types.
     *
     * @var Array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];
}
