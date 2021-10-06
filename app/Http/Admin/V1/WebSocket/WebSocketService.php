<?php


namespace App\Http\Admin\V1\WebSocket;


use App\Http\Admin\V1\Logic\TokenLogic;
use App\Http\Admin\V1\Logic\FdLogic;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WebSocketService implements WebSocketHandlerInterface
{

    private $fdLogic;

    public function __construct()
    {
        $this->fdLogic = app(FdLogic::class);
    }


    public function onOpen(Server $server, Request $request)
    {
        $this->bindUid($server, $request);
    }


    public function onMessage(Server $server, Frame $frame)
    {
        //
    }


    public function onClose(Server $server, $fd, $reactorId)
    {
        $this->del($fd);
    }


    /**
     * @param $server
     * @param $request
     */
    private function bindUid($server, $request)
    {

        if (!$uid = $this->getUid(request()->input('token'))) {

            //如果$uid返回false 说明token不存在或token不正确 断开连接
            $server->disconnect($request->fd);
        }
        else {

            $this->save($request->fd, $uid);
        }

    }


    /**
     * @param $token
     * @return mixed
     */
    private function getUid($token)
    {
        return $token == '' ? false : app(TokenLogic::class)->getUidByToken($token);
    }


    /**
     * @param $fd
     * @param $uid
     */
    private function save($fd, $uid)
    {
        //目前不考虑给单人推送消息 所以只需要把fd当做hash的key uid是随手存的。
        $this->fdLogic->add($fd, $uid);
    }


    /**
     * @param $fd
     */
    private function del($fd)
    {
        $this->fdLogic->del($fd);;
    }


}