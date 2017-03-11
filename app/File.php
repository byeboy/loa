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

    public function parts() {
        return $this->belongsToMany('App\Part', 'part_file');
    }
}