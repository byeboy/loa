<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/2/18
 * Time: 下午6:54
 */

namespace App\Http\Controllers;


use App\Events\NoticeEvent;
use App\Notice;
use Illuminate\Http\Request;
use Mockery\Matcher\Not;

class NoticeController extends Controller
{

    /**
     * NoticeController constructor.
     */
    public function __construct()
    {
    }

    /**
     * Get a list of notices with publisher
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $notices = Notice::with('publisher')->orderBy('updated_at', 'desc')->get();
        return response()->json([
            'success' => true,
            'post' => [
                'notices' => $notices,
            ],
            'message' => '已获取所有公告信息',
        ]);
    }

    /**
     * Create a Notice
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {
        $input = $request->json()->all();
        $notice = Notice::create($input);
        event(new NoticeEvent($notice));
        return response()->json([
            'success' => true,
            'post' => [
                'notice' => $notice,
            ],
            'message' => '公告已成功创建',
        ]);
    }

    /**
     * Update a Notice
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id) {
        $notice = Notice::find($id);
        $notice->title = $request->json()->get('title');
        $notice->intro = $request->json()->get('intro');
        $notice->content = $request->json()->get('content');
        $notice->save();
        return response()->json([
            'success' => true,
            'post' => [
                'notice' => $notice,
            ],
            'message' => '公告更新成功',
        ]);
    }

    /**
     * Delete Notices
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete($id) {
        $res = Notice::destroy($id);
        if($res === 0) {
            return response()->json([
                'success' => false,
                'post' => [

                ],
                'message' => '删除失败',
            ]);
        }
        return response()->json([
            'success' => true,
            'post' => [

            ],
            'message' => '删除成功，即将更新',
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
        $notices = Notice::with('publisher')->where($param, 'LIKE', '%'.urldecode($val).'%')->get();
        return response()->json([
            'success' => true,
            'post' => [
                'notices' => $notices,
            ],
            'message' => '查询完成，以下是查询结果',
        ]);
    }

    /**
     * function for Get
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function get($id) {
        $notice = Notice::with('publisher')->find($id);
        if($notice === null) {
            return response()->json([
                'success' => true,
                'post' => [
                    'notice' => null,
                ],
                'message' => '该公告不存在',
            ]);
        }
        return response()->json([
            'success' => true,
            'post' => [
                'notice' => $notice,
            ],
            'message' => '已成功获取公告详情',
        ]);
    }
}