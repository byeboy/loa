<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/2/19
 * Time: 下午10:11
 */

namespace App\Http\Controllers;


use App\Events\TaskCreate;
use App\Events\TaskUpdate;
use App\Step;
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
        $tasks = Task::with('poster','users','fans')->orderBy('deadline', 'asc')->get();
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
        $tasks = $user->tasks()->with('users', 'poster', 'parts.files', 'cabinets.files', 'fans.files', 'models.files')->orderBy('deadline', 'asc')->get();
        $posts = $user->posts()->with('users', 'parts.files', 'cabinets.files', 'fans.files', 'models.files')->orderBy('deadline', 'asc')->get();
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
    /*public function create(Request $request) {
        $input = $request->json()->all();
        $users = $request->json()->get('users');
        $files = $request->json()->get('files');
        $steps = $request->json()->get('steps');
        if(!($users && count($users) !== 0)){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '任务创建失败，请检查执行者信息',
            ]);
        }
        if(!($steps && count($steps) !== 0)){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '任务创建失败，请检查任务流程信息',
            ]);
        }
        $task = Task::create($input);
        if($task->id) {
            foreach ($steps as $step){
                Step::create([
                    'name' => $step,
                    'task_id' => $task->id,
                ]);
            }
            $task->users()->sync($users);
            if($files && count($files) !== 0){
                $task->files()->sync($files);
            }
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
    }*/
    public function create(Request $request) {
        $input = $request->json()->all();
        $doers = $request->json()->get('doers');
        $products = $request->json()->get('products');
        /*if(!($doers && count($doers) !== 0)){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '任务创建失败，请检查执行者信息',
            ]);
        }*/
        $task = Task::create($input);
        if($task->id) {
            if($products && count($products) !== 0){
                foreach ($products as $product){
                    switch($product['key']){
                        case 'models': {
                            foreach ($product['data'] as $pd){
                                $task->models()->attach($pd['id'], ['plan_count' => $pd['plan_count']]);
                                if(array_has($pd, 'cabinets') && count($pd['cabinets']) !== 0){
                                    foreach ($pd['cabinets'] as $cabinet){
                                        $old = $task->cabinets()->where('taskgable_id', $cabinet['id'])->first();
                                        if(count($old) !== 0){
                                            $task->cabinets()->syncWithoutDetaching([$cabinet['id'], ['plan_count' => $old->plan_count + $pd['plan_count'] * $cabinet['pivot']['required_count']]]);
                                        } else {
                                            $task->cabinets()->attach($cabinet['id'], ['plan_count' => $pd['plan_count'] * $cabinet['pivot']['required_count']]);
                                        }
                                    }
                                }
                                if (array_has($pd, 'fans') && count($pd['fans']) !== 0){
                                    foreach ($pd['fans'] as $fan){
                                        $old = $task->fans()->where('taskgable_id', $fan['id'])->first();
                                        if(count($old) !== 0){
                                            $task->fans()->syncWithoutDetaching([$fan['id'], ['plan_count' => $old->plan_count + $pd['plan_count'] * $fan['pivot']['required_count']]]);
                                        } else {
                                            $task->fans()->attach($fan['id'], ['plan_count' => $pd['plan_count'] * $fan['pivot']['required_count']]);
                                        }
                                    }
                                }
                                if (array_has($pd, 'parts') && count($pd['parts']) !== 0){
                                    foreach ($pd['parts'] as $part){
                                        $old = $task->parts()->where('taskgable_id', $part['id'])->first();
                                        if(count($old) !== 0){
                                            $task->parts()->syncWithoutDetaching([$part['id'], ['plan_count' => $old->plan_count + $pd['plan_count'] * $part['pivot']['required_count']]]);
                                        } else {
                                            $task->parts()->attach($part['id'], ['plan_count' => $pd['plan_count'] * $part['pivot']['required_count']]);
                                        }
                                    }
                                }
                            };
                            break;
                        };
                        case 'cabinets': {
                            foreach ($product['data'] as $pd){
                                $old = $task->cabinets()->where('taskgable_id', $pd['id'])->first();
                                if(count($old) !== 0){
                                    $task->cabinets()->syncWithoutDetaching([$pd['id'],
                                        ['plan_count' => $old->pivot->plan_count + $pd['plan_count']]]);
                                } else {
                                    $task->cabinets()->attach($pd['id'], ['plan_count' => $pd['plan_count']]);
                                }
                                if (array_has($pd, 'parts') && count($pd['parts']) !== 0){
                                    foreach ($pd['parts'] as $part){
                                        $old = $task->parts()->where('taskgable_id', $part['id'])->first();
                                        if(count($old) !== 0){
                                            $task->parts()->syncWithoutDetaching([$part['id'],
                                                ['plan_count' => $old->plan_count + $pd['plan_count'] * $part['pivot']['required_count']]]);
                                        } else {
                                            $task->parts()->attach($part['id'],
                                                ['plan_count' => $pd['plan_count'] * $part['pivot']['required_count']]);
                                        }
                                    }
                                }
                            };
                            break;
                        };
                        case 'fans': {
                            foreach ($product['data'] as $pd){
                                $old = $task->fans()->where('taskgable_id', $pd['id'])->first();
                                if(count($old) !== 0){
                                    $task->fans()->syncWithoutDetaching([$pd['id'], ['plan_count' => $old->plan_count + $pd['plan_count']]]);
                                } else {
                                    $task->fans()->attach($pd['id'], ['plan_count' => $pd['plan_count']]);
                                }
                                if (array_has($pd, 'parts') && count($pd['parts']) !== 0){
                                    foreach ($pd['parts'] as $part){
                                        $old = $task->parts()->where('taskgable_id', $part['id'])->first();
                                        if(count($old) !== 0){
                                            $task->parts()->syncWithoutDetaching([$part['id'], ['plan_count' => $old->plan_count + $pd['plan_count'] * $part['pivot']['required_count']]]);
                                        } else {
                                            $task->parts()->attach($part['id'], ['plan_count' => $pd['plan_count'] * $part['pivot']['required_count']]);
                                        }
                                    }
                                }
                            };
                            break;
                        };
                        case 'parts': {
                            foreach ($product['data'] as $pd) {
                                $old = $task->parts()->where('taskgable_id', $pd['id'])->first();
                                if (count($old) !== 0) {
                                    $task->parts()->syncWithoutDetaching([$pd['id'], ['plan_count' => $old->plan_count + $pd['plan_count']]]);
                                } else {
                                    $task->parts()->attach($pd['id'], ['plan_count' => $pd['plan_count']]);
                                }
                            }
                            break;
                        };
                        default: return;
                    }
                }
            }
            $task->users()->sync($doers);
            $post = Task::with('models.files', 'cabinets.files', 'fans.files', 'parts.files', 'users', 'poster')->where('id', $task->id)->first();
            event(new TaskCreate($post));
            return response()->json([
                'success' => true,
                'post' => [
                    'task' => $post,
                ],
                'message' => '任务'.$post->name.'已成功创建',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => [
                'task' => null,
            ],
            'message' => '任务创建失败',
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

    public function progressPatch(Request $request, $id){
        $progress = $request->json()->get('progress');
        $models = [];
        $cabinets = [];
        $fans = [];
        $parts = [];
        foreach ($progress as $p){
//            return response()->json(['p' => $p]);
            switch($p['type']){
                case 'models':
                    $models[$p['id']]= ['done_count' => $p['newDone']];
                    break;
                case 'cabinets':
                    $cabinets[$p['id']]= ['done_count' => $p['newDone']];
                    break;
                case 'fans':
                    $fans[$p['id']]= ['done_count' => $p['newDone']];
                    break;
                case 'parts':
                    $parts[$p['id']]= ['done_count' => $p['newDone']];
                    break;
            }
        }
        $task = Task::find($id);
        if(count($models) !== 0 ){
            $task->models()->syncWithoutDetaching($models);
        }
        if(count($cabinets) !== 0 ){
            $task->cabinets()->syncWithoutDetaching($cabinets);
        }
        if(count($fans) !== 0 ){
            $task->fans()->syncWithoutDetaching($fans);
        }
        if(count($parts) !== 0 ){
            $task->parts()->syncWithoutDetaching($parts);
        }
        $task = Task::with('models', 'cabinets', 'fans', 'parts')->find($id);
        $doneCount = 0;
        $planCount = 0;
        $done = true;
        if(count($task->models) !== 0){
            foreach ($task->models as $model){
                $doneCount += $model->pivot->done_count;
                $planCount += $model->pivot->plan_count;
            }
        }
        if(count($task->cabinets) !== 0){
            foreach ($task->cabinets as $cabinet){
                $doneCount += $cabinet->pivot->done_count;
                $planCount += $cabinet->pivot->plan_count;
            }
        }
        if(count($task->fans) !== 0){
            foreach ($task->fans as $fan){
                $doneCount += $fan->pivot->done_count;
                $planCount += $fan->pivot->plan_count;
            }
        }
        if(count($task->parts) !== 0){
            foreach ($task->parts as $part){
                $doneCount += $part->pivot->done_count;
                $planCount += $part->pivot->plan_count;
            }
        }
        /*$done = true;
        if(count($task->models) !== 0){
            foreach ($task->models as $model){
                if($model->pivot->done_count < $model->pivot->plan_count){
                    $done = false;
                }
            }
        }
        if(count($task->cabinets) !== 0){
            foreach ($task->cabinets as $cabinet){
                if($cabinet->pivot->done_count < $cabinet->pivot->plan_count){
                    $done = false;
                }
            }
        }
        if(count($task->fans) !== 0){
            foreach ($task->fans as $fan){
                if($fan->pivot->done_count < $fan->pivot->plan_count){
                    $done = false;
                }
            }
        }
        if(count($task->parts) !== 0){
            foreach ($task->parts as $part){
                if($part->pivot->done_count < $part->pivot->plan_count){
                    $done = false;
                }
            }
        }
        if($done){
            $task->status = 5;
        } else {
            $task->status = 1;
        }*/
        $progress = round($doneCount * 100 / $planCount, 0);
        if($progress == 100){
            $task->status = 5;
            $task->progress = $progress;
        } else {
            $task->status = 1;
            $task->progress = $progress;
        }
        $task->save();
        $post = Task::with('models.files', 'cabinets.files', 'fans.files', 'parts.files', 'poster', 'users')->where('id', $task->id)->first();
        event(new TaskUpdate($post));
        return response()->json([
            'success' => true,
            'post' => $post,
            'message' => '任务'.$post->name.'进度同步完成'
        ]);
    }

    public function stepPatch(Request $request, $id){
        $input = $request->json()->all();
        $step = Step::where('id', $id)->update($input);
        if($step){
            return response()->json([
                'success' => true,
                'post' => [

                ],
                'message' => '任务状态更新成功',
            ]);
        }
        return response()->json([
            'success' => false,
            'post' => [

            ],
            'message' => '任务状态更新失败',
        ]);
    }

    public function update(Request $request, $id){
        $task = Task::find($id);
        $task->name = $request->json()->get('name');
        $task->deadline = $request->json()->get('deadline');
        $doers = $request->json()->get('doers');
        if(!($doers && count($doers) !== 0)){
            return response()->json([
                'success' => false,
                'post' => null,
                'message' => '任务更新失败，请检查执行者信息',
            ]);
        }
        if($task->save()){
            $task->users()->sync($doers);
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

    public function getAllFiles($id){
        $files = [];
        $task = Task::with('models.files', 'cabinets.files', 'fans.files', 'parts.files')->find($id);
        if($task->models !== null){
            foreach ($task->models as $model){
                if(count($model->files) !== 0){
                    foreach ($model->files as $file){
                        array_push($files, [$file->url, $file->name]);
                    }
                }
            }
        }
        if($task->cabinets !== null){
            foreach ($task->cabinets as $cabinet){
                if(count($cabinet->files) !== 0){
                    foreach ($cabinet->files as $file){
                        array_push($files, [$file->url, $file->name]);
                    }
                }
            }
        }
        if($task->fans !== null){
            foreach ($task->fans as $fan){
                if(count($fan->files) !== 0){
                    foreach ($fan->files as $file){
                        array_push($files, [$file->url, $file->name]);
                    }
                }
            }
        }
        if($task->parts !== null){
            foreach ($task->parts as $part){
                if(count($part->files) !== 0){
                    foreach ($part->files as $file){
                        array_push($files, [$file->url, $file->name]);
                    }
                }
            }
        }
        return FileController::getZip($files, $task->name);
        /*return response()->json([
            'files' => $zip,
        ]);*/
    }

}