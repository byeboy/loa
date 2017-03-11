<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/10
 * Time: 上午4:10
 */

namespace App;


class Model extends \Illuminate\Database\Eloquent\Model
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
        'name', 'intro',
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
    public function parts() {
        return $this->hasMany('App\Part', 'model_id', 'id');
    }
}