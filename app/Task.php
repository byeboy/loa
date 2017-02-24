<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/2/15
 * Time: 上午11:44
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
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
        'name', 'intro', 'content', 'deadline', 'poster_id',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [];

    /**
     * 模型关联
     */
    public function poster() {
        return $this->belongsTo('App\User', 'poster_id', 'id');
    }

    public function users() {
        return $this->belongsToMany('App\User');
    }
}