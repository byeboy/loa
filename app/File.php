<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/7
 * Time: 下午3:30
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class File extends Model
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
        'name', 'url', 'uploader_id',
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
    public function uploader() {
        return $this->belongsTo('App\User', 'uploader_id', 'id');
    }

    public function tasks() {
        return $this->belongsToMany('App\Task', 'task_file');
    }

    public function models()
    {
        return $this->morphedByMany('App\Model', 'filegable');
    }

    public function cabinets()
    {
        return $this->morphedByMany('App\Cabinet', 'filegable');
    }

    public function fans()
    {
        return $this->morphedByMany('App\Fan', 'filegable');
    }

    public function parts()
    {
        return $this->morphedByMany('App\Part', 'filegable');
    }
}