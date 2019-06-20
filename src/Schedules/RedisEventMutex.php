<?php

namespace Pkg\LaravelScheduleEvent\Schedules;

use Illuminate\Cache\RedisStore;
use Illuminate\Console\Scheduling\CacheEventMutex;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Support\Facades\Redis;

class RedisEventMutex extends CacheEventMutex
{
    public function expire(Event $event, $time)
    {
        if (!($this->cache->store($this->store)->getStore() instanceof RedisStore)) {
            return false;
        }
        $redis_key = $this->cache->store($this->store)->getStore()->getPrefix() . $event->mutexName();
        Redis::expire($redis_key, $time);
    }
}