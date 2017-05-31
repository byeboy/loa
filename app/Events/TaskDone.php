<?php

namespace App\Events;

use App\Task;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TaskDone extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $task;

    /**
     * 指定事件被放置在哪个队列上
     *
     * @var string
     */
    /*public $broadcastQueue = 'your-queue-name';*/

    /**
     * 创建一个事件实例。
     *
     * @param  Task  $task
     * @return void
     */
    public function __construct(Task $task)
    {
        $this->task = $task;
    }

    /**
     * 获取事件应该被广播的频道。
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['private-'.$this->task->poster_id];
    }

    /**
     * 事件的广播名称。
     *
     * @return string
     */
    /* public function broadcastAs()
     {
         return 'server.created';
     }*/

    /**
     * 指定广播数据
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'message' => '任务完成提醒',
            'description' => '名为“'.$this->task->name.'”的任务已完成，完成时间为：'.$this->task->created_at.'。'
        ];
    }

}
