<?php


namespace App\Http\Admin\V1\WebSocket;


use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Illuminate\Support\Facades\Log;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WebSocketService implements WebSocketHandlerInterface
{

    public function __construct()
    {
        // 构造函数即使为空，也不能省略
    }

    // public function onHandShake(Request $request, Response $response)
    // {
    // 自定义握手：https://wiki.swoole.com/#/websocket_server?id=onhandshake
    // 成功握手之后会自动触发onOpen事件
    // }

    public function onOpen(Server $server, Request $request)
    {
        // 在触发onOpen事件之前，建立WebSocket的HTTP请求已经经过了Laravel的路由，
        // 所以Laravel的Request、Auth等信息是可读的，Session是可读写的，但仅限在onOpen事件中。
        // \Log::info('New WebSocket connection', [$request->fd, request()->all(), session()->getId(), session('xxx'), session(['yyy' => time()])]);
        // 此处抛出的异常会被上层捕获并记录到Swoole日志，开发者需要手动try/catch
        $server->push($request->fd, 'Welcome to LaravelS');


    }


    public function onMessage(Server $server, Frame $frame)
    {
        // $frame->fd 是客户端 id，$frame->data 是客户端发送的数据
        Log::info("从 {$frame->fd} 接收到的数据: {$frame->data}");

        foreach ($server->connections as $fd) {
            if (!$server->isEstablished($fd)) {
                // 如果连接不可用则忽略
                continue;
            }
            $server->push($fd, $frame->data); // 服务端通过 push 方法向所有客户端广播消息
        }
    }


    public function onClose(Server $server, $fd, $reactorId)
    {
        // 此处抛出的异常会被上层捕获并记录到Swoole日志，开发者需要手动try/catch
    }

}