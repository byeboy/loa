<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/2/19
 * Time: 下午10:11
 */

namespace App\Http\Controllers;


use App\Task;
use App\User;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    /**
     * TaskController constructor.
     */
    public function __construct()
    {
    }

    /**
     * get a list of tasks
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index() {
        $tasks = Task::with('poster')->with('users')->orderBy('deadline', 'asc')->get();
        return response()->json([
            'success' => true,
            'post' => [
                'tasks' => $tasks,
            ],
            'message' => '已获取所有任务信息',
        ]);
    }

    /**
     * get a list of tasks
     *
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function all($id) {
        $user = User::find($id);
        $tasks = $user->tasks()->with('poster')->orderBy('deadline', 'asc')->get();
        $posts = $user->posts()->with('users')->orderBy('deadline', 'asc')->get();
//        $todos = $user->tasks()->with('poster')->where('status', 1)->get();
//        $dones = $user->tasks()->with('poster')->where('status', 5)->get();
        return response()->json([
            'success' => true,
            'post' => [
                'tasks' => $tasks,
//                'todos' => $todos,
//                'dones' => $dones,
                'posts' => $posts,
            ],
            'message' => '已获取与'.$user->name.'有关的所有任务信息',
        ]);
    }

    /**
     * Create a Task
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {
        $input = $request->json()->all();
        $task = Task::create($input);
        $users = $request->json()->get('users');
        if($task->id) {
            $task->users()->sync($users);
            return response()->json([
                'success' => true,
                'post' => [
                    'task' => $task,
                ],
                'message' => '任务'.$task->name.'已成功创建',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => [
                'task' => $task,
            ],
            'message' => '任务'.$task->name.'创建失败',
        ]);
    }

    /**
     * Patch a Task
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function patch(Request $request, $id){
        $input = $request->json()->all();
        $task = Task::where('id', $id)->update($input);
        if($task){
            return response()->json([
                'success' => true,
                'post' => [

                ],
                'message' => '任务信息更新成功',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => [

            ],
            'message' => '任务信息更新失败',
        ]);
    }

    public function update(Request $request, $id){
        $task = Task::find($id);
        $task->name = $request->json()->get('name');
        $task->intro = $request->json()->get('intro');
        $task->content = $request->json()->get('content');
        $task->deadline = $request->json()->get('deadline');
        $users = $request->json()->get('users');
        if($task->save()){
            $task->users()->sync($users);
            return response()->json([
                'success' => true,
                'post' => [

                ],
                'message' => '任务信息更新成功',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => [

            ],
            'message' => '任务信息更新失败',
        ]);
    }

    public function delete($id){
        $task = Task::destroy($id);
        if($task){
            return response()->json([
                'success' => true,
                'post' => [

                ],
                'message' => '任务删除成功',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => [

            ],
            'message' => '任务删除失败',
        ]);
    }

}