<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Laravel\Lumen\Auth\Authorizable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;

class User extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    /**
     * 设置主键
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'email', 'name', 'password', 'img', 'api_token', 'branch_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    /**
     * 模型关联
     */
    public function branch() {
        return $this->belongsTo('App\Branch', 'branch_id', 'id');
    }

    public function posts() {
        return $this->hasMany('App\Task', 'poster_id', 'id');
    }

    public function tasks() {
        return $this->belongsToMany('App\Task');
    }

    public function notices() {
        return $this->hasMany('App\Notice', 'publisher_id', 'id');
    }
}
