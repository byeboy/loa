<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/16
 * Time: 上午6:59
 */

namespace App\Http\Controllers;


use App\Fan;
use Illuminate\Http\Request;

class FanController extends Controller
{
    /**
     * Get a list of Fan
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $fans = Fan::with('parts.files', 'files')->get();
        if($fans) {
            return response()->json([
                'success' => true,
                'post' => [
                    'fans' => $fans,
                ],
                'message' => '已获取所有风机信息'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '获取风机信息失败'
            ]);
        }
    }

    public function relation($type, $id) {
        $relations = [];
        foreach (Fan::whereHas($type, function ($fan) use($id) {
            $fan->where('model_id', $id);
        })->cursor() as $relation) {
            array_push($relations, $relation->id);
        }
        $fans = Fan::whereNotIn('id', $relations)->get();
        return response()->json([
            'success' => true,
            'post' => [
                'relations' => $relations,
                'fans' => $fans,
            ],
            'message' => '已获取所有可关联的风机信息',
        ]);
    }

    /**
     * Create a Fan
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
                'message' => '风机（'.$name.'）已存在',
            ]);
        }
        $input = $request->json()->all();
        $fan = Fan::create($input);
        if($fan) {
            $post = Fan::with('parts.files', 'files')->find($fan->id);
            return response()->json([
                'success' => true,
                'post' => $post,
                'message' => '风机（'.$fan->name.'）已创建成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '风机创建失败',
            ]);
        }
    }

    public function createRelation(Request $request, $upId) {
        $type = $request->json()->get('type');
        $id = $request->json()->get('id');
        $relations = [];
        if($type === 'files'){
            //        检测并删除已存在的关联ID
            foreach (Fan::find($upId)->files()
                         ->whereIn('file_id', $id)
                         ->cursor() as $relation) {
                array_push($relations, $relation->file_id);
            }
            $newId = array_diff($id, $relations);
            Fan::find($upId)->files()->attach($newId);
//        检测关联成功与否
            $res = Fan::find($upId)->files()->whereIn('file_id', $id)->exists();
            if($res) {
                $fan = Fan::with('files')->find($upId);
                return response()->json([
                    'success' => true,
                    'post' => $fan,
                    'message' => '关联成功',
                ]);
            }
        } elseif($type === 'parts') {
//        检测并删除已存在的关联ID
            foreach (Fan::find($upId)->$type()
                         ->whereIn('partgable_id', $id)
                         ->cursor() as $relation) {
                array_push($relations, $relation->partgable_id);
            }
            $newId = array_diff($id, $relations);
            Fan::find($upId)->$type()->attach($newId, ['required_count' => 1]);
//        检测关联成功与否
            $res = Fan::find($upId)->$type()->whereIn('part_id', $id)->exists();
            if($res) {
                $fan = Fan::with($type.'.files')->find($upId);
                return response()->json([
                    'success' => true,
                    'post' => $fan,
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
        Fan::find($upId)->$type()->updateExistingPivot($id, ['required_count' => $required_count]);
        $post = Fan::with($type.'.files')->find($upId);
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
        Fan::find($id)->$type()->detach($rid);
        if($type === 'files'){
            $post = Fan::with($type)->find($id);
        } else {
            $post = Fan::with($type.'.files')->find($id);
        }
        return response()->json([
            'success' => true,
            'post' => $post,
            'message' => '删除关联成功',
        ]);
    }

    /**
     * Patch a Fan
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
                    'message' => '风机（'.$value.'）已存在',
                ]);
            }
        }
        $fan = Fan::where('id', $id)->update([$key => $value]);
        if($fan){
            return response()->json([
                'success' => true,
                'post' => [

                ],
                'message' => '风机信息更新成功',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => [

            ],
            'message' => '风机信息更新失败',
        ]);
    }

    /**
     * Update a Fan
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){
        $fan = Fan::find($id);
        $fan->name = $request->json()->get('name');
        $Mcheck = $this->check($fan->name);
        if($Mcheck->id != $id){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '风机名称不可重复，请修改',
            ]);
        }
        $fan->intro = $request->json()->get('intro');
        if($fan->save()){
            $post = Fan::with('parts.files', 'files')->find($fan->id);
            return response()->json([
                'success' => true,
                'post' => $post,
                'message' => '风机信息更新成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '风机信息更新失败',
            ]);
        }
    }

    /**
     * Delete a Fan
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        $res = Fan::destroy($id);
        if($res) {
            return response()->json([
                'success' => true,
                'post' => null,
                'message' => '风机信息删除成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '风机信息删除失败',
            ]);
        }
    }

    /**
     * Check name of Fan
     *
     * @param $name
     * @return mixed
     */
    public function check($name) {
        $fan = Fan::where('name', $name)->first();
        return $fan;
    }
}