<?php

namespace App\Events;

use App\Task;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class TaskCreate extends Event implements ShouldBroadcast
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
        $channels = [];
        foreach ($this->task->users as $u) {
            $channels[] = 'private-'.$u->id;
        }
        return $channels;
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
            'message' => '新任务提醒',
            'description' => $this->task->poster['name'].'于'.$this->task->created_at.'发布了一个新任务，任务名为“'.$this->task->name.'”，请注意查看与监管。'
        ];
    }

}
