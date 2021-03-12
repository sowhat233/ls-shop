<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
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
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        //sql日志记录  如果为调试模式 则会记录并将日志写入到storage\logs文件夹里
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
                        storage_path('logs'.DIRECTORY_SEPARATOR.date('Y-m-d').'sql.log'),
                        'a+'
                    );

                    fwrite($log_file, date('Y-m-d H:i:s').': '.$query.PHP_EOL);

                    fclose($log_file);
                }
            );


        }


    }



}
