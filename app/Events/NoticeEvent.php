<?php

namespace App\Events;

use App\Notice;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class NoticeEvent extends Event implements ShouldBroadcast
{
    use SerializesModels;

    public $notice;

    /**
     * 指定事件被放置在哪个队列上
     *
     * @var string
     */
    /*public $broadcastQueue = 'your-queue-name';*/

    /**
     * 创建一个事件实例。
     *
     * @param  Notice  $notice
     * @return void
     */
    public function __construct(Notice $notice)
    {
        $this->notice = $notice;
    }

    /**
     * 获取事件应该被广播的频道。
     *
     * @return array
     */
    public function broadcastOn()
    {
        return ['public'];
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
            'message' => '新公告提醒',
            'description' => '管理员于'.$this->notice->updated_at.'发布了一条新公告《'.$this->notice->title.'》，请注意查看。'
        ];
    }

}
