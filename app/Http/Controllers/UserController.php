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
        if($this->check($email)) {
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

    public function rewrite(Request $request, $id) {

        $user = User::find($id);
//        $oldPwd = $request->json()->get('oldPwd');
//        return response()->json([
//           'id' => $id,
//            'old' => $oldPwd,
//        ]);
        if($request->json()->get('oldPwd') === decrypt($user->password)) {
            if($request->json()->get('newPwd') === decrypt($user->password)) {
                return response()->json([
                    'success' => false,
                    'post' => null,
                    'message' => '新旧密码不可相同',
                ]);
            } else {
                $user->password = encrypt($request->json()->get('newPwd'));
                $user->save();
                return response()->json([
                    'success' => true,
                    'post' => null,
                    'message' => '密码修改成功，请重新登录',
                ]);
            }
        } else {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '原密码错误，请确认用户权限',
            ]);
        }
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
        $users = User::with('branch')->where($param, 'LIKE', '%'.urldecode($val).'%')->get();
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
            if($user) {
                return response()->json([
                    'success' => true,
                    'post' => [
                        'loginUser' => $user
                    ],
                    'message' => '尊敬的'.$user->name.'，欢迎回来^_^',
                ]);
            }
            return response('Unauthorized.', 401);
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
        if($this->check($user->email)) {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '该邮箱('.$user->email.')已被注册',
            ]);
        }
        $user->phone = $request->json()->get('phone');
        if($this->check($user->phone, 'phone')) {
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '该联系方式('.$user->phone.')已存在',
            ]);
        }
        $user->password = encrypt($request->json()->get('password'));
        $user->name = $request->json()->get('name');
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
        if ($this->check($email)) {
            $user = User::with('branch')->where('email', $email)->first();
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
    public function check($param, $val = 'email') {
        return User::where($val, $param)->exists();
    }

    public function test(){
        $test = User::with('tasks.files', 'branch')->get();
        return response()->json([
            'test' => $test,
        ]);
    }
}