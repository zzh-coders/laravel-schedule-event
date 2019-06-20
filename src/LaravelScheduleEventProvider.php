<?php

namespace Pkg\LaravelScheduleEvent;

use Illuminate\Console\Scheduling\EventMutex;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Pkg\LaravelScheduleEvent\Listeners\ScheduleEventSubscriber;
use Pkg\LaravelScheduleEvent\Schedules\RedisEventMutex;


class LaravelScheduleEventProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    public function boot()
    {
        //命令行下执行
        if ($this->app->runningInConsole()) {
            $this->app->bindIf(EventMutex::class, function () {
                return $this->app->make(RedisEventMutex::class);
            });

            Event::subscribe(ScheduleEventSubscriber::class);
        }
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {

    }
}