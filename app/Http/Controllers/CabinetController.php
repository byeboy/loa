<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/10
 * Time: 上午4:26
 */

namespace App\Http\Controllers;


use App\Cabinet;
use Illuminate\Http\Request;

class CabinetController extends Controller
{
    /**
     * Get a list of Cabinet
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $cabinets = Cabinet::with('parts.files', 'files')->get();
        if($cabinets) {
            return response()->json([
                'success' => true,
                'post' => [
                    'cabinets' => $cabinets,
                ],
                'message' => '已获取所有柜体信息'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '获取柜体信息失败'
            ]);
        }
    }


    public function relation($type, $id) {
        $relations = [];
        foreach (Cabinet::whereHas($type, function ($cabinet) use($id) {
            $cabinet->where('model_id', $id);
        })->cursor() as $relation) {
            array_push($relations, $relation->id);
        }
        $cabinets = Cabinet::whereNotIn('id', $relations)->get();
        return response()->json([
            'success' => true,
            'post' => [
                'relations' => $relations,
                'cabinets' => $cabinets,
            ],
            'message' => '已获取所有可关联的柜体信息',
        ]);
    }

    /**
     * Create a Cabinet
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
                'message' => '柜体（'.$name.'）已存在',
            ]);
        }
        $input = $request->json()->all();
        $cabinet = Cabinet::create($input);
        if($cabinet) {
            $post = Cabinet::with('parts.files', 'files')->find($cabinet->id);
            return response()->json([
                'success' => true,
                'post' => $post,
                'message' => '柜体（'.$cabinet->name.'）已创建成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '柜体创建失败',
            ]);
        }
    }

    public function createRelation(Request $request, $upId) {
        $type = $request->json()->get('type');
        $id = $request->json()->get('id');
        $relations = [];
        if($type === 'files'){
            //        检测并删除已存在的关联ID
            foreach (Cabinet::find($upId)->files()
                         ->whereIn('file_id', $id)
                         ->cursor() as $relation) {
                array_push($relations, $relation->file_id);
            }
            $newId = array_diff($id, $relations);
            Cabinet::find($upId)->files()->attach($newId);
//        检测关联成功与否
            $res = Cabinet::find($upId)->files()->whereIn('file_id', $id)->exists();
            if($res) {
                $cabinet = Cabinet::with('files')->find($upId);
                return response()->json([
                    'success' => true,
                    'post' => $cabinet,
                    'message' => '关联成功',
                ]);
            }
        } elseif($type === 'parts') {
//        检测并删除已存在的关联ID
            foreach (Cabinet::find($upId)->$type()
                         ->whereIn('partgable_id', $id)
                         ->cursor() as $relation) {
                array_push($relations, $relation->part_id);
            }
            $newId = array_diff($id, $relations);
            Cabinet::find($upId)->$type()->attach($newId, ['required_count' => 1]);
//        检测关联成功与否
            $res = Cabinet::find($upId)->$type()->whereIn('part_id', $id)->exists();
            if($res) {
                $cabinet = Cabinet::with($type.'.files')->find($upId);
                return response()->json([
                    'success' => true,
                    'post' => $cabinet,
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
        Cabinet::find($upId)->$type()->updateExistingPivot($id, ['required_count' => $required_count]);
        $post = Cabinet::with($type.'.files')->find($upId);
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
        Cabinet::find($id)->$type()->detach($rid);
        if($type === 'files'){
            $post = Cabinet::with($type)->find($id);
        } else {
            $post = Cabinet::with($type.'.files')->find($id);
        }
        return response()->json([
            'success' => true,
            'post' => $post,
            'message' => '删除关联成功',
        ]);
    }

    /**
     * Patch a Cabinet
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
                    'message' => '柜体（'.$value.'）已存在',
                ]);
            }
        }
        $cabinet = Cabinet::where('id', $id)->update([$key => $value]);
        if($cabinet){
            return response()->json([
                'success' => true,
                'post' => [

                ],
                'message' => '柜体信息更新成功',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => [

            ],
            'message' => '柜体信息更新失败',
        ]);
    }

    /**
     * Update a Cabinet
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){
        $cabinet = Cabinet::find($id);
        $cabinet->name = $request->json()->get('name');
        $Mcheck = $this->check($cabinet->name);
        if($Mcheck != null && $Mcheck->id != $id){
            return response()->json([
                'success' => false,
                'post' => $Mcheck,
                'message' => '柜体名称不可重复，请修改',
            ]);
        }
        $cabinet->intro = $request->json()->get('intro');
        if($cabinet->save()){
            $post = Cabinet::with('parts.files', 'files')->find($cabinet->id);
            return response()->json([
                'success' => true,
                'post' => $post,
                'message' => '柜体信息更新成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '柜体信息更新失败',
            ]);
        }
    }

    /**
     * Delete a Cabinet
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        $res = Cabinet::destroy($id);
        if($res) {
            return response()->json([
                'success' => true,
                'post' => null,
                'message' => '柜体信息删除成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '柜体信息删除失败',
            ]);
        }
    }

    /**
     * Check name of Cabinet
     *
     * @param $name
     * @return mixed
     */
    public function check($name) {
        $cabinet = Cabinet::where('name', $name)->first();
        return $cabinet;
    }
}