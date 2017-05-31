<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/10
 * Time: 上午4:26
 */

namespace App\Http\Controllers;

use App\Part;
use Illuminate\Http\Request;

class PartController extends Controller
{
    /**
     * Get a list of Part
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $parts = Part::with('files')->get();
        if($parts) {
            return response()->json([
                'success' => true,
                'post' => [
                    'parts' => $parts,
                ],
                'message' => '已获取所有零件信息'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '获取零件信息失败'
            ]);
        }
    }

    public function relation($type, $id) {
        $relations = [];
        foreach (Part::whereHas($type, function ($part) use($id) {
            $part->where('partgable_id', $id);
        })->cursor() as $relation) {
            array_push($relations, $relation->id);
        }
        $parts = Part::whereNotIn('id', $relations)->get();
        return response()->json([
            'success' => true,
            'post' => [
                'relations' => $relations,
                'parts' => $parts,
            ],
            'message' => '已获取所有可关联的零件信息',
        ]);
    }

    /**
     * Create a Part
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
                'message' => '零件（'.$name.'）已存在',
            ]);
        }
        $input = $request->json()->all();
        $part = Part::create($input);
        if($part) {
            $post = Part::with('files')->find($part->id);
            return response()->json([
                'success' => true,
                'post' => $post,
                'message' => '零件（'.$part->name.'）已创建成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '零件创建失败',
            ]);
        }
    }

    public function createRelation(Request $request, $upId) {
        $type = $request->json()->get('type');
        $id = $request->json()->get('id');
        $relations = [];
        if($type === 'files'){
            //        检测并删除已存在的关联ID
            foreach (Part::find($upId)->files()
                         ->whereIn('file_id', $id)
                         ->cursor() as $relation) {
                array_push($relations, $relation->file_id);
            }
            $newId = array_diff($id, $relations);
            Part::find($upId)->files()->attach($newId);
//        检测关联成功与否
            $res = Part::find($upId)->files()->whereIn('file_id', $id)->exists();
            if($res) {
                $part = Part::with('files')->find($upId);
                return response()->json([
                    'success' => true,
                    'post' => $part,
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
        Part::find($upId)->$type()->updateExistingPivot($id, ['required_count' => $required_count]);
        $post = Part::with($type.'.files')->find($upId);
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
        Part::find($id)->$type()->detach($rid);
        if($type === 'files'){
            $post = Part::with($type)->find($id);
        } else {
            $post = Part::with($type.'.files')->find($id);
        }
        return response()->json([
            'success' => true,
            'post' => $post,
            'message' => '删除关联成功',
        ]);
    }

    /**
     * Patch a Part
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
                    'message' => '零件（'.$value.'）已存在',
                ]);
            }
        }
        $part = Part::where('id', $id)->update([$key => $value]);
        if($part){
            return response()->json([
                'success' => true,
                'post' => [

                ],
                'message' => '零件信息更新成功',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => [

            ],
            'message' => '零件信息更新失败',
        ]);
    }

    /**
     * Update a Part
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){
        $part = Part::find($id);
        $part->name = $request->json()->get('name');
        $Mcheck = $this->check($part->name);
        if($Mcheck->id != $id){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '零件名称不可重复，请修改',
            ]);
        }
        $part->intro = $request->json()->get('intro');
        if($part->save()){
            $post = Part::with('files')->find($part->id);
            return response()->json([
                'success' => true,
                'post' => $post,
                'message' => '零件信息更新成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '零件信息更新失败',
            ]);
        }
    }

    /**
     * Delete a Part
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        $res = Part::destroy($id);
        if($res) {
            return response()->json([
                'success' => true,
                'post' => null,
                'message' => '零件信息删除成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '零件信息删除失败',
            ]);
        }
    }

    public function record() {
        $parts = Part::with('records.operator')->get();
        if($parts) {
            return response()->json([
                'success' => true,
                'post' => [
                    'parts' => $parts,
                ],
                'message' => '已获取所有零件信息'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '获取零件信息失败'
            ]);
        }
    }

    public function recorder(Request $request, $id){
        $part = Part::find($id);
        if($part === null) {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '该零件不存在',
            ]);
        }
        $type = $request->json()->get('type');
        $count = $request->json()->get('count');
        if($type === 0 && $count > $part->count){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '该零件库存不足',
            ]);
        }
        $remark = $request->json()->get('remark');
        $operator_id = $request->json()->get('operator_id');
        $recordrst = $part->records()->create([
            'type' => $type,
            'count' => $count,
            'remark' => $remark,
            'operator_id' => $operator_id,
        ]);
        if($recordrst !== null) {
            $operate = null;
            switch ($type) {
                case 0: {
                    $part->count -= $count;
                    $operate = '出库';
                    break;
                }
                case 1: {
                    $part->count += $count;
                    $operate = '入库';
                    break;
                }
                default: {
                    return response()->json([
                        'success' => false,
                        'post' => null,
                        'message' => '非法操作',
                    ]);
                }
            }
            if($part->save()){
                $newPart = Part::with('records.operator')->find($id);
                return response()->json([
                    'success' => true,
                    'post' => $newPart,
                    'message' => $operate.'操作成功',
                ]);
            } else {
                $part->records()->detach([$recordrst->id]);
                return response()->json([
                    'success' => false,
                    'post' => $part,
                    'message' => $operate.'操作失败',
                ]);
            }
        }
        return response()->json([
            'success' => false,
            'post' => $part,
            'message' => '操作失败',
        ]);
    }

    /**
     * Check name of Part
     *
     * @param $name
     * @return mixed
     */
    public function check($name) {
        $part = Part::where('name', $name)->first();
        return $part;
    }
}