<?php


namespace Pkg\LaravelScheduleEvent\Listeners;

use Illuminate\Console\Application;
use Illuminate\Console\Events\CommandFinished;
use Illuminate\Console\Events\CommandStarting;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Log;

class ScheduleEventSubscriber
{
    /**
     * @var int  锁的过期时间，探活的时间为ttl-4
     */
    private $ttl = 30;

    /**
     * 根据命令行获取对应的event对象
     *
     * @param $full_cmd_line
     *
     * @return |null
     */
    private function getCurrentEvent($full_cmd_line)
    {
        foreach (app()->get(Schedule::class)->events() as $event) {
            if (($event instanceof Event) && $full_cmd_line === $event->command) {
                return $event;
            }
        }
        return null;
    }

    public function handle($event)
    {
        $full_cmd_line = Application::formatCommandString($event->command);
        $e = $this->getCurrentEvent($full_cmd_line);
        if (!($e instanceof Event)) {
            return false;
        }


        $pid = posix_getpid();
        switch ($child_pid = pcntl_fork()) {
            case 0:
                //在子进程里面进行判断父进程的pid值与主进程获取的$pid是一致说明父进程还正在跑，否则就是退出了
                while (posix_getppid() == $pid) {
                    $e->mutex->expire($e, $this->ttl);
                    usleep(($this->ttl - 4) * 1000000);
                }
                exit();
                break;
            case -1:
                //创建失败
                Log::error($event->command . ' pcntl_fork faild');
                break;
            default:
                //防止子进程变成僵尸进程
                pcntl_waitpid($child_pid, $status, WNOHANG);
                break;
        }

        return true;
    }

    /**
     * Register the listeners for the subscriber.
     *
     * @param  \Illuminate\Events\Dispatcher $events
     */
    public function subscribe($events)
    {
        $events->listen(
            [
                CommandStarting::class, // 命令开始的时候
            ],
            __CLASS__ . '@handle'
        );
    }
}