<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    protected $admin_namespace = 'App\Http\Admin\V1\Controllers';

    protected $wechat_namespace = 'App\Http\Wechat\V1\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {

        //后台路由
        $this->mapAdminRoutes();

        //微信小程序接口路由
        $this->mapWechatRoutes();

    }

    /**
     * 后台路由注册
     */
    protected function mapAdminRoutes()
    {
        Route::prefix('/api/v1/admin')
             ->namespace($this->admin_namespace)
             ->group(base_path('routes/admin.php'));
    }

    /**
     * 微信接口路由注册
     */
    protected function mapWechatRoutes()
    {
        Route::prefix('/api/v1/wechat')
             ->namespace($this->wechat_namespace)
             ->group(base_path('routes/wechat.php'));
    }


}
