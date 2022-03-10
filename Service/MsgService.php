<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/7 14:21
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Websocket\Service;

use App\Application\Websocket\Model\WebsocketMsg;
use App\Application\Websocket\Service\Msg\AbstractMsg;
use App\Application\Websocket\Service\Msg\ImageMsg;
use App\Application\Websocket\Service\Msg\TextMsg;
use Hyperf\SocketIOServer\Socket;

class MsgService
{
    protected Socket $socket;
    protected string $user_target;
    protected string $room;
    protected array $msg = [];

    const MSG_TYPE_TEXT = 'text';
    const MSG_TYPE_IMAGE = 'image';

    public function __construct(Socket $socket, array $data = [])
    {
        $this->socket = $socket;
        if (!empty($data)) {
            $this->user_target = $data['user_target'] ?? '';
            $room = $data['room'] ?? '';
            $this->msg = $data['msg'] ?? [];
            $room_list = $socket->getAdapter()
                ->clientRooms($socket->getSid());
            if (!empty($this->msg) && !in_array($room, $room_list)) {
                //发送消息事，房间不存在，则报错
                $this->sendError('not_in_room', '抱歉，你当前连接不在房间');
            }
            $this->room = $room;
        }
    }

    public function joinRoom()
    {
        //离开现有的房间，加入当前的房间，保证每个连接只能在一个房间。
        $this->socket->leave(...$this->socket->getAdapter()
            ->clientRooms($this->socket->getSid()));
        // 将当前用户加入房间
        $this->socket->join($this->room);
        $event = [
            'type' => 'join_room',
            'msg' => $this->socket->getSid() . " has joined "
        ];
        $this->socket->to($this->room)
            ->emit('event', $event);
        $this->getMsgList($this->room);
    }

    /**
     * 下发消息列表
     *
     * @param        $room
     * @param string $last_id
     */
    public function getMsgList($room, string $last_id = '')
    {
        $where = [
            ['room', '=', $room]
        ];
        if ($last_id !== '') {
            $id = WebsocketMsg::where('msg_id', $last_id)
                ->value('id');
            $where[] = ['id', '<', $id];
        }
        $res = WebsocketMsg::where($where)
            ->limit(20)
            ->orderBy('id', 'DESC')
            ->select()
            ->get();
        $lists = [];
        foreach ($res as $item) {
            $msg = $this->getMsg($item);
            $lists[] = $msg->getMsg();
        }
        $last_id = $lists[count($lists) - 1]['msg_id'] ?? '';
        $this->socket->emit('lists', compact('lists', 'last_id'));
    }

    /**
     * 接收处理文本消息
     */
    public function receiveText()
    {
        $this->receive(self::MSG_TYPE_TEXT);
    }

    /**
     * 接收处理图片信息
     */
    public function receiveImage()
    {
        $this->receive(self::MSG_TYPE_IMAGE);
    }

    /**
     * 获取消息处理对象
     *
     * @param WebsocketMsg $msg
     * @return AbstractMsg
     */
    private function getMsg(WebsocketMsg $msg): AbstractMsg
    {
        if ($msg->type == self::MSG_TYPE_TEXT) {
            return new TextMsg($msg, $this->getUserTarget());
        }
        if ($msg->type == self::MSG_TYPE_IMAGE) {
            return new ImageMsg($msg, $this->getUserTarget());
        }

        return new TextMsg($msg, $this->getUserTarget());
    }

    /**
     * 接收消息处理
     *
     * @param       $msg_type
     */
    private function receive($msg_type)
    {
        $msg_id = 'msg_' . $msg_type . '_' . uniqid();
        $msg = $this->msg;
        $room = $this->room;
        $msg_model = WebsocketMsg::newModelInstance([
            'msg_id' => $msg_id,
            'room' => $room,
            'type' => $msg_type,
            'msg_content' => $msg['msg_content'] ?? '',
            'user_target' => $this->getUserTarget(),
        ]);
        $msg = $this->getMsg($msg_model);
        if ($msg->saveMsg()) {
            //发送消息
            $this->newMsg($msg);
        } else {
            $this->sendError('msg_error', '消息保存错误');
        }
    }

    /**
     * 下发错误事件
     *
     * @param string $error_type
     * @param string $error_msg
     */
    private function sendError(string $error_type, string $error_msg)
    {
        //错误通知
        $data = compact('error_msg', 'error_type');
        $this->socket->emit('error', $data);
    }

    /**
     * 下发新消息
     *
     * @param AbstractMsg $msg
     */
    private function newMsg(AbstractMsg $msg)
    {
        $msg_info = $msg->getMsg();
        $this->socket->to($msg->room)
            ->emit('new_msg', ['is_self' => false] + $msg_info);

        $msg->setUserTarget($this->getUserTarget());
        $this->socket->emit('new_msg', ['is_self' => true] + $msg_info);
    }

    /**
     * 发送用户标识，默认是用socket id 可以根据自己的用户体系进行设置
     *
     * @return string
     */
    public function getUserTarget(): string
    {
        return $this->user_target ?: $this->socket->getSid();
    }
}