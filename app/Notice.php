<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/2/15
 * Time: 上午11:49
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Notice extends Model
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
        'title', 'intro', 'content', 'publisher_id',
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
    public function publisher() {
        return $this->belongsTo('App\User', 'publisher_id', 'id');
    }

}