<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/28
 * Time: 上午10:38
 */

namespace App;


class Record extends Model
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
        'type', 'count', 'remark', 'operator_id',
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
    public function operator()
    {
        return $this->belongsTo('App\User', 'operator_id');
    }
    /**
     * 获取所有拥有的 recordable 模型。
     */
    public function recordable()
    {
        return $this->morphTo();
    }
}