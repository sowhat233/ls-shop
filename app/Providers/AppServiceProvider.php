<?php

namespace App\Providers;


use App\Models\Order;
use App\Observers\OrderObserver;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Validator;
use App\Http\Validators\CostPriceValidator;
use App\Http\Validators\SkuCostPriceValidator;
use DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }


    /**
     * @var array
     * 自定义验证
     * 这个数组的key不能是驼峰法
     */
    protected $validators = [
        //单规格商品 进货价 对比 售价
        'cost_price'     => CostPriceValidator::class,
        'sku_cost_price' => SkuCostPriceValidator::class,
    ];


    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        //注册自定义验证 详见 https://learnku.com/articles/35283
        $this->registerValidators();

        //注册观察者
        $this->registerObserver();

        //sql debug
        $this->sqlDebugLog();

    }


    /**
     * 注册观察者
     */
    protected function registerObserver()
    {
        Order::observe(OrderObserver::class);
    }


    /**
     * sql日志记录  如果为调试模式 则会记录并将日志写入到storage\logs文件夹里
     */
    protected function sqlDebugLog()
    {

        if (env('APP_DEBUG')) {

            DB::listen(

                function ($sql) {

                    foreach ($sql->bindings as $i => $binding) {

                        if ($binding instanceof DateTime) {

                            $sql->bindings[$i] = $binding->format('\'Y-m-d H:i:s\'');

                        }
                        else {

                            if (is_string($binding)) {
                                $sql->bindings[$i] = "'$binding'";
                            }
                        }

                    }

                    // Insert bindings into query
                    $query = str_replace(array('%', '?'), array('%%', '%s'), $sql->sql);

                    $query = vsprintf($query, $sql->bindings);

                    // Save the query to file
                    $log_file = fopen(
                        storage_path('logs' . DIRECTORY_SEPARATOR . date('Y-m-d') . 'sql.log'),
                        'a+'
                    );

                    fwrite($log_file, date('Y-m-d H:i:s') . ': ' . $query . PHP_EOL);

                    fclose($log_file);
                }
            );


        }
    }


    /**
     * 注册自定义验证
     */
    protected function registerValidators()
    {
        foreach ($this->validators as $rule => $validator) {
            Validator::extend($rule, "{$validator}@validate");
        }
    }
}
