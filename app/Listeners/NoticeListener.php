<?php
/**
 * Created by PhpStorm.
 * User: Gilbert
 * Date: 17/5/5
 * Time: 上午11:07
 */

namespace App\Listeners;

use App\Events\NoticeEvent;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class NoticeListener implements ShouldQueue
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NoticeEvent  $event
     * @return void
     */
    public function handle(NoticeEvent $event)
    {
        //
    }
}
