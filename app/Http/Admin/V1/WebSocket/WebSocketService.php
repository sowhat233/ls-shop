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

    public function onOpen(Server $server, Request $request)
    {
        //不需要onPen事件
    }


    public function onMessage(Server $server, Frame $frame)
    {

        //不需要onPen事件
        // $frame->fd 是客户端 id，$frame->data 是客户端发送的数据
//        Log::info("从 {$frame->fd} 接收到的数据: {$frame->data}");

        foreach ($server->connections as $fd) {

            if (!$server->isEstablished($fd)) {
                // 如果连接不可用则忽略
                continue;
            }
            $server->push($fd, "你的$fd"); // 服务端通过 push 方法向所有客户端广播消息
        }
    }


    public function onClose(Server $server, $fd, $reactorId)
    {
        //不需要onClose事件
    }

}