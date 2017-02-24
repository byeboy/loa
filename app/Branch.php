<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/2/15
 * Time: 上午11:40
 */

namespace App;

use Illuminate\Database\Eloquent\Model;

class Branch extends Model
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
        'name', 'authority', 'intro'
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
    public function users() {
        return $this->hasMany('App\User', 'branch_id', 'id');
    }
}