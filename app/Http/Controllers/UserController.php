<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/2/17
 * Time: 下午12:54
 */

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class UserController extends Controller
{

    /**
     * UserController constructor.
     */
    public function __construct()
    {
    }

    /**
     * Get all users with branch
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $users = User::with('branch')->get();
        return response()->json([
            'success' => true,
            'post' => [
                'users' => $users,
            ],
            'message' => '已获取所有用户信息',
        ]);
    }

    /**
     * Create a User
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {
        $input = $request->json()->all();
        $email = $request->json()->get('email');
        if($this->check($email) === null) {
            $user = User::create($input);
            return response()->json([
                'success' => true,
                'post' => [
                    'user' => $user,
                ],
                'message' =>  '新增用户成功',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => null,
            'message' => '该电子邮箱已存在',
        ]);
    }

    /**
     * Updata a User
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {
        $user = User::find($id);
        $user->phone = $request->json()->get('phone');
        $user->branch_id = $request->json()->get('branch_id');
//        return $request->json()->all();
        $user->save();
        return response()->json([
            'success' => true,
            'post' => [
                'user' => $user,
            ],
            'message' => '更新成功',
        ]);
    }

    /**
     * Delete Users
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        $res = User::destroy($id);
        $users = User::with('branch')->get();
        if($res === 0) {
            return response()->json([
                'success' => false,
                'post' => [
                    'users' => $users,
                ],
                'message' => '删除失败',
            ]);
        }
        return response()->json([
            'success' => true,
            'post' => [
                'users' => $users,
            ],
            'message' => '删除成功,即将更新用户列表',
        ]);
    }

    /**
     * function for Search
     *
     * @param $param
     * @param $val
     * @return \Illuminate\Http\JsonResponse
     */
    public function search($param, $val) {
//        if($val == null || $val == '') {
//            $users = User::with('branch')->get();
//        } else {
        $users = User::with('branch')->where($param, 'LIKE', '%'.$val.'%')->get();
//        }
        return response()->json([
            'success' => true,
            'post' => [
                'users' => $users,
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
        $user = User::with('branch')->find($id);
        if($user === null) {
            return response()->json([
                'success' => false,
                'post' => [
                    'user' => null,
                ],
                'message' => '该用户不存在',
            ]);
        }
        return response()->json([
            'success' => true,
            'post' => [
                'user' => $user,
            ],
            'message' => '已得到该用户信息',
        ]);
    }

    public function auth(Request $request)
    {
        if ($request->header('authorization')) {
            $email = decrypt($request->header('authorization'));
            $user = User::with('branch')->where('email', $email)->first();
            return response()->json([
                'success' => true,
                'post' => [
                    'loginUser' => $user
                ],
                'message' => '尊敬的'.$user->name.'，欢迎回来^_^',
            ]);
        }
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '您尚未登录，请登录。:(',
            ]);
    }

    /**
     * function for Reg
     *
     * @param Request $request
     * @return $this|\Illuminate\Http\JsonResponse
     */
    public function reg(Request $request) {
        $user = new User();
        $user->email = $request->json()->get('email');
        $Ucheck = $this->check($user->email);
        if($Ucheck) {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '该邮箱('.$Ucheck->email.')已被注册',
            ]);
        }
        $user->password = encrypt($request->json()->get('password'));
        $user->name = $request->json()->get('name');
        $user->phone = $request->json()->get('phone');
        $user->branch_id = 28;
        $user->save();
        return response()->json([
            'success' => true,
            'post' => $user,
            'message' => '注册成功',
        ])->header('status', 201);

    }

    /**
     * function for Login
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request) {
        $email = $request->json()->get('email');
        $user = $this->check($email);
        if ($user) {
            $password = $request->json()->get('password');
            if ($password === decrypt($user->password)) {
                $authorization = encrypt($user->email);
                return response()->json([
                    'success' => true,
                    'post' => [
                        'loginUser' => $user,
                    ],
                    'message' => '登录成功，欢迎回来',
                ])->withHeaders([
                    "authorization" => $authorization,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'post' => null,
                    'message' => '登录失败，请检查密码',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '该电子邮箱未注册',
            ]);
        }
    }

    /**
     * funciton for Check
     *
     * @param $email
     * @return mixed
     */
    public function check($email) {
        $user = User::with('branch')->where('email', $email)->first();
        return $user;
    }
}