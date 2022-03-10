<?php

declare(strict_types=1);

namespace App\Application\Websocket\Controller;

use App\Application\Websocket\Service\MsgService;
use Hyperf\SocketIOServer\Annotation\Event;
use Hyperf\SocketIOServer\Annotation\SocketIONamespace;
use Hyperf\SocketIOServer\BaseNamespace;
use Hyperf\SocketIOServer\Socket;

/**
 * 聊天室
 * @SocketIONamespace("/chat")
 */
class WebsocketChatController extends BaseNamespace
{
    /**
     * 加入聊天室
     * @Event("join-room")
     */
    public function onJoinRoom(Socket $socket, array $data)
    {
        $msg_service = new MsgService($socket, $data);
        $msg_service->joinRoom();
    }

    /**
     * 获取聊天记录
     * @Event("lists")
     */
    public function msgList(Socket $socket, array $data)
    {
        $room = $data['room'] ?? '';
        $last_id = $data['last_id'] ?? '';
        $msg_service = new MsgService($socket, $data);
        $msg_service->getMsgList($room, $last_id);
    }

    /**
     * 发送文本消息
     * @Event("text")
     */
    public function onText(Socket $socket, array $data)
    {
        $msg_service = new MsgService($socket, $data);
        $msg_service->receiveText();
    }

    /**
     * 发送图片消息
     * @Event("image")
     */
    public function onImage(Socket $socket, array $data)
    {
        $msg_service = new MsgService($socket, $data);
        $msg_service->receiveImage();
    }
}
