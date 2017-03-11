<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/3/10
 * Time: 上午4:25
 */

namespace App\Http\Controllers;


use App\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    /**
     * Get a list of Material
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $materials = Material::withCount('parts')->get();
        if($materials) {
            return response()->json([
                'success' => true,
                'post' => [
                    'materials' => $materials,
                ],
                'message' => '已获取所有材质信息'
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '获取材质信息失败'
            ]);
        }
    }

    /**
     * Create a Material
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
        $material = Material::create($input);
        if($material) {
            return response()->json([
                'success' => true,
                'post' => $material,
                'message' => '材质（'.$material->name.'）已创建成功',
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
     * Patch a Material
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
                    'message' => '材质（'.$value.'）已存在',
                ]);
            }
        }
        $material = Material::where('id', $id)->update([$key => $value]);
        if($material){
            return response()->json([
                'success' => true,
                'post' => [

                ],
                'message' => '材质信息更新成功',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => [

            ],
            'message' => '材质信息更新失败',
        ]);
    }

    /**
     * Update a Material
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id){
        $material = Material::find($id);
        $material->name = $request->json()->get('name');
        $Mcheck = $this->check($material->name);
        if($Mcheck->id !== $id){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '材质名称不可重复，请修改',
            ]);
        }
        $material->intro = $request->json()->get('intro');
        if($material->save()){
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
     * Delete a Material
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        $res = Material::destroy($id);
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
     * Check name of Material
     *
     * @param $name
     * @return mixed
     */
    public function check($name) {
        $material = Material::where('name', $name)->first();
        return $material;
    }
}