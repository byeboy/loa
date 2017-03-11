<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/7
 * Time: 下午3:30
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Step extends Model
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
        'name', 'task_id', 'user_id',
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
    public function task() {
        return $this->belongsTo('App\Task', 'task_id', 'id');
    }

    public function user() {
        return $this->belongsTo('App\User', 'user_id', 'id');
    }
}