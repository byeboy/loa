<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/11
 * Time: 下午1:39
 */

namespace App\Http\Controllers;


use App\Cabinet;
use App\Material;
use App\Model;

class PartPropertyController extends Controller
{
    /**
     * Get a list of Properties
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $materials = Material::withCount('parts')->get();
        $models = Model::withCount('parts')->get();
        $cabinets = Cabinet::withCount('parts')->get();
        if($materials && $models && $cabinets) {
            return response()->json([
                'success' => true,
                'post' => [
                    'materials' => $materials,
                    'models' => $models,
                    'cabinets' => $cabinets,
                ],
                'message' => '已获取所有属性信息'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '获取属性信息失败'
            ]);
        }
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
                'message' => '材质（'.$name.'）已存在',
            ]);
        }
        $input = $request->json()->all();
        $cabinet = Cabinet::create($input);
        if($cabinet) {
            return response()->json([
                'success' => true,
                'post' => null,
                'message' => '材质（'.$cabinet->name.'）已创建成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '材质创建失败',
            ]);
        }
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
        if($Mcheck->id !== $id){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '材质名称不可重复，请修改',
            ]);
        }
        $cabinet->intro = $request->json()->get('intro');
        if($cabinet->save()){
            return response()->json([
                'success' => true,
                'post' => null,
                'message' => '材质信息更新成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '材质信息更新失败',
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
                'message' => '材质信息删除成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '材质信息删除失败',
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