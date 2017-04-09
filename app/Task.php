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
        'name', 'intro', 'content', 'deadline', 'poster_id', 'checker_id', 'status', 'progress',
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

    public function checker() {
        return $this->belongsTo('App\User', 'checker_id', 'id');
    }

    public function users() {
        return $this->belongsToMany('App\User');
    }

    public function steps() {
        return $this->hasMany('App\Step', 'task_id', 'id');
    }

    public function files() {
        return $this->belongsToMany('App\File', 'task_file');
    }

    public function models() {
        return $this->morphedByMany('App\Model', 'taskgable')->withPivot('done_count', 'plan_count');
    }
    public function cabinets() {
        return $this->morphedByMany('App\Cabinet', 'taskgable')->withPivot('done_count', 'plan_count');
    }
    public function fans() {
        return $this->morphedByMany('App\Fan', 'taskgable')->withPivot('done_count', 'plan_count');
    }
    public function parts() {
        return $this->morphedByMany('App\Part', 'taskgable')->withPivot('done_count', 'plan_count');
    }
}