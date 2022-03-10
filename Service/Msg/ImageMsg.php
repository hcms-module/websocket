<?php
/**
 * Created by: zhlhuang (364626853@qq.com)
 * Time: 2022/3/7 14:26
 * Blog: https://www.yuque.com/huangzhenlian
 */

declare(strict_types=1);

namespace App\Application\Websocket\Service\Msg;

use Hyperf\Utils\Codec\Json;

class ImageMsg extends AbstractMsg
{
    public function getMsg(): array
    {
        $this->msg_model->is_self = $this->msg_model->user_target === $this->user_target;
        $res = $this->msg_model->setHidden([
            'id',
            'updated_at',
            'deleted_at'
        ])
            ->toArray();
        if (!empty($res['msg_content']) && is_string($res['msg_content'])) {
            try {
//                var_dump(Json::decode($this->msg_model->msg_content, true));
                $res['msg_content'] = Json::decode($res['msg_content'], true);
            } catch (\Exception $exception) {
                $res['msg_content'] = [];
            }
        }

        return $res;
    }
}