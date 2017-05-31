<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/10
 * Time: 上午4:26
 */

namespace App\Http\Controllers;


use App\Model;
use Illuminate\Http\Request;

class ModelController extends Controller
{
    /**
     * Get a list of Model
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $models = Model::with('cabinets.files', 'fans.files', 'parts.files', 'files')->get();
        if($models) {
            return response()->json([
                'success' => true,
                'post' => [
                    'models' => $models,
                ],
                'message' => '已获取所有车型信息'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '获取车型信息失败'
            ]);
        }
    }

    /**
     * Create a Model
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request){
        $name = $request->json()->get('name');
        if($this->check($name) !== null){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '车型（'.$name.'）已存在',
            ]);
        }
        $input = $request->json()->all();
        $model = Model::create($input);
        if($model) {
            if(sizeof($input['files']) !== 0){
                $model->files()->attach($input['files']);
            }
            $post = Model::with('cabinets.files', 'fans.files', 'parts.files', 'files')->find($model->id);
            return response()->json([
                'success' => true,
                'post' => $post,
                'message' => '车型（'.$model->name.'）已创建成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '车型创建失败',
            ]);
        }
    }

    public function createRelation(Request $request, $upId) {
        $type = $request->json()->get('type');
        $id = $request->json()->get('id');
        $relations = [];
        if($type === 'files'){
            //        检测并删除已存在的关联ID
            foreach (Model::find($upId)->files()
                         ->whereIn('file_id', $id)
                         ->cursor() as $relation) {
                array_push($relations, $relation->file_id);
            }
            $newId = array_diff($id, $relations);
            Model::find($upId)->files()->attach($newId);
            //        检测关联成功与否
            $res = Model::find($upId)->files()->whereIn('file_id', $id)->exists();
            if($res) {
                $model = Model::with('files')->find($upId);
                return response()->json([
                    'success' => true,
                    'post' => $model,
                    'message' => '关联成功',
                ]);
            }
        } elseif($type === 'parts') {
//        检测并删除已存在的关联ID
            foreach (Model::find($upId)->$type()
                         ->whereIn('partgable_id', $id)
                         ->cursor() as $relation) {
                array_push($relations, $relation->partgable_id);
            }
            $newId = array_diff($id, $relations);
            Model::find($upId)->$type()->attach($newId, ['required_count' => 1]);
//        检测关联成功与否
            $res = Model::find($upId)->$type()->whereIn('part_id', $id)->exists();
            if ($res) {
                $model = Model::with($type.'.files')->find($upId);
                return response()->json([
                    'success' => true,
                    'post' => $model,
                    'message' => '关联成功',
                ]);
            }
        } else {
//        检测并删除已存在的关联ID
            foreach (Model::find($upId)->$type()
                         ->whereIn('modelgable_id', $id)
                         ->cursor() as $relation) {
                array_push($relations, $relation->modelgable_id);
            }
            $newId = array_diff($id, $relations);
            Model::find($upId)->$type()->attach($newId, ['required_count' => 1]);
//        检测关联成功与否
            $res = Model::find($upId)->$type()->whereIn('modelgable_id', $id)->exists();
            if($res) {
                $model = Model::with($type.'.files')->find($upId);
                return response()->json([
                    'success' => true,
                    'post' => $model,
                    'message' => '关联成功',
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'post' => null,
            'message' => $type,
        ]);
    }

    public function patchRelation(Request $request, $upId) {
        $type = $request->json()->get('type');
        $id = $request->json()->get('id');
        $required_count = $request->json()->get('required_count');
        Model::find($upId)->$type()->updateExistingPivot($id, ['required_count' => $required_count]);
        $post = Model::with($type.'.files')->find($upId);
        $success = false;
        foreach ($post->$type as $relation){
            if($relation->id === $id && $relation->pivot->required_count === $required_count){
                $success = true;
            }
        }
        if($success) {
            return response()->json([
                'success' => true,
                'post' => $post,
                'message' => '更新关联信息成功',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => null,
            'message' => '更新关联信息失败',
        ]);
    }

    public function delRelation($id, $type, $rid) {
        Model::find($id)->$type()->detach($rid);
        if($type === 'files'){
            $post = Model::with($type)->find($id);
        } else {
            $post = Model::with($type.'.files')->find($id);
        }
        return response()->json([
            'success' => true,
            'post' => $post,
            'message' => '删除关联成功',
        ]);
    }

    /**
     * Patch a Model
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function patch(Request $request, $id){
        $key = $request->json()->get('key');
        $value = $request->json()->get('value');
        if($key === 'name'){
            if($this->check($value) !== null){
                return response()->json([
                    'success' => false,
                    'post' => null,
                    'message' => '车型（'.$value.'）已存在',
                ]);
            }
        }
        $model = Model::where('id', $id)->update([$key => $value]);
        if($model){
            return response()->json([
                'success' => true,
                'post' => [

                ],
                'message' => '车型信息更新成功',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => [

            ],
            'message' => '车型信息更新失败',
        ]);
    }

    /**
     * Update a Model
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){
        $model = Model::find($id);
        $model->name = $request->json()->get('name');
        $Mcheck = $this->check($model->name);
        if($Mcheck != null && $Mcheck->id != $id){
            return response()->json([
                'success' => false,
                'post' => $Mcheck,
                'message' => '车型名称不可重复，请修改',
            ]);
        }
        $model->intro = $request->json()->get('intro');
        if($model->save()){
            $post = Model::with('cabinets.files', 'fans.files', 'parts.files', 'files')->find($model->id);
            return response()->json([
                'success' => true,
                'post' => $post,
                'message' => '车型信息更新成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '车型信息更新失败',
            ]);
        }
    }

    /**
     * Delete a Model
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        $res = Model::destroy($id);
        if($res) {
            return response()->json([
                'success' => true,
                'post' => null,
                'message' => '车型信息删除成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '车型信息删除失败',
            ]);
        }
    }

    public function record() {
        $models = Model::with('records.operator')->get();
        if($models) {
            return response()->json([
                'success' => true,
                'post' => [
                    'models' => $models,
                ],
                'message' => '已获取所有车型信息'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '获取车型信息失败'
            ]);
        }
    }

    public function recorder(Request $request, $id){
        $model = Model::find($id);
        if($model === null) {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '该车型不存在',
            ]);
        }
        $type = $request->json()->get('type');
        $count = $request->json()->get('count');
        if($type === 0 && $count > $model->count){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '该车型库存不足',
            ]);
        }
        $remark = $request->json()->get('remark');
        $operator_id = $request->json()->get('operator_id');
        $recordrst = $model->records()->create([
            'type' => $type,
            'count' => $count,
            'remark' => $remark,
            'operator_id' => $operator_id,
        ]);
        if($recordrst !== null) {
            $operate = null;
            switch ($type) {
                case 0: {
                    $model->count -= $count;
                    $operate = '出库'; break;
                }
                case 1: {
                    $model->count += $count;
                    $operate = '入库'; break;
                }
                default: {
                    return response()->json([
                        'success' => false,
                        'post' => null,
                        'message' => '非法操作',
                    ]);
                }
            }
            if($model->save()){
                $newModel = Model::with('records.operator')->find($id);
                return response()->json([
                    'success' => true,
                    'post' => $newModel,
                    'message' => $operate.'操作成功',
                ]);
            } else {
                $model->records()->detach([$recordrst->id]);
                return response()->json([
                    'success' => false,
                    'post' => $model,
                    'message' => $operate.'操作失败',
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'post' => $model,
            'message' => '操作失败',
        ]);
    }

    /**
     * Check name of Model
     *
     * @param $name
     * @return mixed
     */
    public function check($name) {
        $model = Model::where('name', $name)->first();
        return $model;
    }
}