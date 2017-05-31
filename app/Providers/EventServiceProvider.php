<?php

namespace App\Providers;

use Laravel\Lumen\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * 应用程序的事件侦听器映射。
     *
     * @var array
     */
    protected $listen = [
        'App\Events\ExampleEvent' => [
            'App\Listeners\ExampleListener',
        ],
        'App\Events\NoticeEvent' => [
            'App\Listeners\NoticeListener',
        ],
        'App\Events\TaskCreate' => [
            'App\Listeners\TaskListener@onTaskCreate',
        ],
        'App\Events\TaskUpdate' => [
            'App\Listeners\TaskListener@onTaskUpdate',
        ],
    ];

    /**
     *  订阅者类进行注册。
     *
     * @var array
     */
    protected $subscribe = [
        'App\Listeners\TaskListener',
    ];
}
