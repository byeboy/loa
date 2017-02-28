<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/2/15
 * Time: 上午11:53
 */

namespace App\Http\Controllers;


use App\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{

    /**
     * BranchController constructor.
     */
    public function __construct()
    {
    }

    /**
     * get a list of branches with users_count
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $branches = Branch::withCount('users')->get();
        return response()->json([
            'success' => true,
            'post' => [
                'branches' => $branches,
            ],
            'message' =>  '已获取所有部门信息',
        ]);
    }


    /**
     * Create a Branch
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {
        $input = $request->json()->all();
        $name = $request->json()->get('name');
        if ($this->check($name) === null) {
            $branch = Branch::create($input);
            return response()->json([
                'success' => true,
                'post' => [
                    'branch' => $branch,
                ],
                'message' =>  '部门已成功创建',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' =>  '部门('.name.')已存在',
            ]);
        }

    }

    /**
     * Update a Branch
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {
        $branch = Branch::find($id);
        $branch->name = $request->json()->get('name');
        $branch->intro = $request->json()->get('intro');
        $branch->authority = $request->json()->get('authority');
        if($branch->save()) {
            return response()->json([
                'success' => true,
                'post' => [
                    'branch' => $branch,
                ],
                'message' =>  '部门信息更新成功',
            ]);
        } else {
            return response()->json([
                'success' => false,
                'post' => [
                    'branch' => $branch,
                ],
                'message' =>  '部门('.$branch->name.')已存在',
            ]);
        }
    }

    /**
     * Delete Branches
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        $res = Branch::destroy($id);
        $branches = Branch::withCount('users')->get();
        if($res === 0) {
            return response()->json([
                'success' => false,
                'post' => [
                    'branches' => $branches,
                ],
                'message' => '删除失败',
            ]);
        }
        return response()->json([
            'success' => true,
            'post' => [
                'branches' => $branches,
            ],
            'message' => '删除成功，即将更新部门列表',
        ]);
    }

    /**
     * function for search
     *
     * @param $param
     * @param $val
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($param, $val) {
        $branches = Branch::withCount('users')->where($param, 'LIKE', '%'.$val.'%')->get();
        return response()->json([
            'success' => true,
            'post' => [
                'branches' => $branches,
            ],
            'message' => '查询已完成，以下是查询结果',
        ]);
    }

    /**
     * function for Get
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id) {
        $branch = Branch::withCount('users')->find($id);
        if($branch === null) {
            return response()->json([
                'success' => true,
                'post' => [
                    'branch' => null,
                ],
                'message' => '该部门不存在',
            ]);
        }
        return response()->json([
            'success' => true,
            'post' => [
                'branch' => $branch,
            ],
            'message' => '已得到该部门信息',
        ]);
    }

    /**
     * function for Check
     *
     * @param $name
     * @return mixed
     */
    public function check($name) {
        $branch = Branch::where('name', $name)->first();
        return $branch;
    }

}