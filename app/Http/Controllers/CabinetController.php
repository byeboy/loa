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
        $cabinets = Cabinet::withCount('components')->get();
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
            return response()->json([
                'success' => true,
                'post' => $cabinet,
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
        if($Mcheck->id !== $id){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '柜体名称不可重复，请修改',
            ]);
        }
        $cabinet->intro = $request->json()->get('intro');
        if($cabinet->save()){
            return response()->json([
                'success' => true,
                'post' => null,
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