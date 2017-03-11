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
        $models = Model::withCount('components')->get();
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
            return response()->json([
                'success' => true,
                'post' => $model,
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
        if($Mcheck->id !== $id){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '车型名称不可重复，请修改',
            ]);
        }
        $model->intro = $request->json()->get('intro');
        if($model->save()){
            return response()->json([
                'success' => true,
                'post' => null,
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