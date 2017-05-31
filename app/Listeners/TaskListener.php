<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/5/5
 * Time: 上午11:07
 */

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;

class TaskListener implements ShouldQueue
{
    /**
     * 处理新增任务事件。
     */
    public function onTaskCreate($event) {}

    /**
     * 处理更新任务事件。
     */
    public function onTaskUpdate($event) {}

    /**
     * 处理完成任务事件。
     */
    public function onTaskDone($event) {}

    /**
     * 注册侦听器的订阅者。
     *
     * @param  Illuminate\Events\Dispatcher  $events
     * @return array
     */
    public function subscribe($events)
    {
        $events->listen(
            'App\Events\TaskCreate',
            'App\Listeners\TaskListener@onTaskCreate'
        );

        $events->listen(
            'App\Events\TaskUpdate',
            'App\Listeners\TaskListener@onTaskUpdate'
        );

        $events->listen(
            'App\Events\TaskDone',
            'App\Listeners\TaskListener@onTaskDone'
        );
    }

}
