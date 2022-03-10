<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/7 14:26
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Websocket\Service\Msg;

use Hyperf\Utils\Codec\Json;

class TextMsg extends AbstractMsg
{
    public function getMsg(): array
    {
        $this->msg_model->is_self = $this->msg_model->user_target === $this->user_target;
        return $this->msg_model->setHidden([
            'id',
            'updated_at',
            'deleted_at'
        ])
            ->toArray();
    }
}