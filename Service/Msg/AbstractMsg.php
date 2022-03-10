<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/7 14:25
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Websocket\Service\Msg;

use App\Application\Websocket\Model\WebsocketMsg;

/**
 * @property $room;
 */
abstract class AbstractMsg
{
    protected WebsocketMsg $msg_model;
    protected string $user_target = '';

    /**
     * @param WebsocketMsg $msg_model
     * @param string       $user_target
     */
    public function __construct(WebsocketMsg $msg_model, string $user_target = '')
    {
        $this->msg_model = $msg_model;
        $this->user_target = $user_target;
    }

    public function saveMsg(): bool
    {
        $this->msg_model->save();
        if ($this->msg_model->id > 0) {
            return true;
        } else {
            return false;
        }
    }

    abstract public function getMsg(): array;

    /**
     * @return WebsocketMsg
     */
    public function getMsgModel(): WebsocketMsg
    {
        return $this->msg_model;
    }

    public function __get($name)
    {
        return $this->msg_model->$name ?? '';
    }

    /**
     * @param string $user_target
     */
    public function setUserTarget(string $user_target): void
    {
        $this->user_target = $user_target;
    }
}