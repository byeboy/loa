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
        'name', 'intro', 'count', 'remark', 'created_at', 'updated_at',
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
    /**
     * 获取该车型下所有的柜体。
     */
    public function cabinets()
    {
        return $this->morphedByMany('App\Cabinet', 'modelgable')->withPivot('required_count');
    }

    /**
     * 获取该车型下所有的风机。
     */
    public function fans()
    {
        return $this->morphedByMany('App\Fan', 'modelgable')->withPivot('required_count');
    }

    /**
     * 获取该车型下所有的零件。
     */
    public function parts()
    {
        return $this->morphToMany('App\Part', 'partgable')->withPivot('required_count');
    }

    /**
     * 获取该车型下所有的工程文件。
     */
    public function files()
    {
        return $this->morphToMany('App\File', 'filegable');
    }

    /**
     * 获取该车型下所有的仓储记录。
     */
    public function records()
    {
        return $this->morphMany('App\Record', 'recordable');
    }
}