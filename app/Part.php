<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/10
 * Time: 上午4:10
 */

namespace App;


use Illuminate\Database\Eloquent\Model;

class Part extends Model
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
        'name', 'intro', 'material_id', 'model_id', 'cabinet_id', 'branch_id', 'count',
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
    public function material() {
        return $this->belongsTo('App\Material', 'material_id', 'id');
    }

    public function model() {
        return $this->belongsTo('App\Model', 'model_id', 'id');
    }

    public function cabinet() {
        return $this->belongsTo('App\Cabinet', 'cabinet_id', 'id');
    }

    public function branch() {
        return $this->belongsTo('App\Branch', 'branch_id', 'id');
    }

    public function files() {
        return $this->belongsToMany('App\File', 'part_file');
    }
}