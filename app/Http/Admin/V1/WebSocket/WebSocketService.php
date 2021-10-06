<?php


namespace App\Http\Admin\V1\WebSocket;


use App\Http\Admin\V1\Logic\TokenLogic;
use Hhxsv5\LaravelS\Swoole\WebSocketHandlerInterface;
use Swoole\Http\Request;
use Swoole\WebSocket\Frame;
use Swoole\WebSocket\Server;

class WebSocketService implements WebSocketHandlerInterface
{

    private $wsTable;

    public function __construct()
    {
        $this->wsTable = app('swoole')->wsTable;
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
        //解绑
        $this->removeWsTable($fd);
    }


    /**
     * @param $server
     * @param $request
     */
    private function bindUid($server, $request)
    {

        if ($uid = $this->getUid(request()->input('token'))) {

            $this->setWsTable($uid, $request->fd);
        }

        //如果上面的if返回false 说明token不存在或token不正确 断开连接
        $server->disconnect($request->fd);

    }


    /**
     * @param $token
     * @return mixed
     */
    private function getUid($token)
    {
        return app(TokenLogic::class)->getUidByToken($token);
    }


    /**
     * @param $uid
     * @param $fd
     */
    private function setWsTable($uid, $fd)
    {

        $this->wsTable->set('uid:' . $uid, ['value' => $fd]);// 绑定uid到fd的映射
        $this->wsTable->set('fd:' . $fd, ['value' => $uid]);// 绑定fd到uid的映射

    }


    /**
     * @param $fd
     */
    private function removeWsTable($fd)
    {

        $uid = $this->wsTable->get('fd:' . $fd);

        if ($uid !== false) {
            // 解绑uid映射
            $this->wsTable->del('uid:' . $uid['value']);
        }

        // 解绑fd映射
        $this->wsTable->del('fd:' . $fd);
    }


}