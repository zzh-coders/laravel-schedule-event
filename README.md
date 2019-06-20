# laravel-schedule-event使用说明
## 关于
这个包主要是监控定时任务命令开启withoutOverlapping后，异常退出命令导致锁不能及时释放的问题

如果使用composer引入，那么就直接写就可以
```$xslt
composer require zehua/laravel-schedule-event
```

# 单独github下载使用请使用下面的方法操作
composer 依赖 或者 使用phpredis
predis/predis

执行一下composer dump-autoload 整个扩展包放在package下面的


在config/app.php
增加 providers 变量的值
```
...
'providers' => [
    ...  
        Pkg\LaravelScheduleEvent\LaravelScheduleEventProvider::class
    ],
    ...
```
composer.json里面
```$xslt
...
"autoload": {
    "classmap": [
      "database/seeds",
      "database/factories"
    ],
    "psr-4": {
      "App\\": "app/",
      "Pkg\\LaravelScheduleEvent\\": "packages/laravel-schedule-event/src"
    }
  },
```
